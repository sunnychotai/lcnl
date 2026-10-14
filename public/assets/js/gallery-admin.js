/**
 * LCNL photo gallery – admin uploader and photo manager.
 *
 * Upload flow, per photo:
 *   1. Hash the original file (SHA-256) and ask the server which hashes the
 *      album already holds. Those are skipped, so dropping the same folder
 *      again after an interruption resumes where it stopped.
 *   2. Decode and resize in the browser (EXIF rotation applied) to the web
 *      size, as a high-quality JPEG. A 20 MB camera file becomes well under
 *      2 MB, so PHP's upload limits never come into play.
 *   3. POST it on its own. The server makes the WebP variants and stores
 *      them in R2. Failures are retried with back-off.
 *
 * Configuration comes from data-* attributes on #gallery-uploader.
 */
(() => {
  'use strict';

  const root = document.getElementById('gallery-uploader');
  if (!root) return;

  const cfg = root.dataset;
  const CONCURRENCY = 3;
  const MAX_ATTEMPTS = 4;
  const CHECK_BATCH = 200;
  const WEB_SIZE = parseInt(cfg.webSize, 10) || 1600;
  const IMAGE_EXT = /\.(jpe?g|png|webp|heic|heif)$/i;

  let csrfHash = cfg.csrfHash;
  let nextSort = parseInt(cfg.nextSort, 10) || 1;
  let running = false;

  const el = (id) => document.getElementById(id);
  const dropzone = el('gallery-dropzone');
  const progress = el('gallery-progress');
  const bar = progress?.querySelector('.progress-bar');
  const summary = el('gallery-summary');
  const log = el('gallery-log');
  const doneBox = el('gallery-done');
  const grid = el('gallery-photos');

  // ---------------------------------------------------------------------
  // Collecting files: picker, folder picker and drag-and-drop (folders too)
  // ---------------------------------------------------------------------

  // Absent when storage is not configured; the photo manager still works.
  if (dropzone) {
    el('gallery-files').addEventListener('change', (e) => { start([...e.target.files]); e.target.value = ''; });
    el('gallery-folder').addEventListener('change', (e) => { start([...e.target.files]); e.target.value = ''; });

    ['dragenter', 'dragover'].forEach((type) => dropzone.addEventListener(type, (e) => {
      e.preventDefault();
      dropzone.classList.add('is-dragover');
    }));
    ['dragleave', 'drop'].forEach((type) => dropzone.addEventListener(type, () => dropzone.classList.remove('is-dragover')));

    dropzone.addEventListener('drop', async (e) => {
      e.preventDefault();
      const entries = [...e.dataTransfer.items]
        .map((item) => (item.webkitGetAsEntry ? item.webkitGetAsEntry() : null))
        .filter(Boolean);

      if (entries.length) {
        start(await filesFromEntries(entries));
      } else {
        start([...e.dataTransfer.files]);
      }
    });
  }

  async function filesFromEntries(entries) {
    const files = [];
    const walk = async (entry) => {
      if (entry.isFile) {
        files.push(await new Promise((resolve, reject) => entry.file(resolve, reject)));
      } else if (entry.isDirectory) {
        const reader = entry.createReader();
        // readEntries returns at most ~100 entries per call; keep reading.
        for (;;) {
          const batch = await new Promise((resolve, reject) => reader.readEntries(resolve, reject));
          if (!batch.length) break;
          for (const child of batch) await walk(child);
        }
      }
    };
    for (const entry of entries) await walk(entry);
    return files;
  }

  // ---------------------------------------------------------------------
  // Running a batch
  // ---------------------------------------------------------------------

  const stats = { total: 0, added: 0, skipped: 0, failed: 0 };

  async function start(files) {
    if (running) {
      alert('An upload is already running. Please wait for it to finish.');
      return;
    }

    // Camera files are numbered in shooting order, so name order is usually
    // the order the event happened in.
    const photos = files
      .filter((f) => (f.type.startsWith('image/') || IMAGE_EXT.test(f.name)) && !f.name.startsWith('.'))
      .sort((a, b) => (a.webkitRelativePath || a.name).localeCompare(b.webkitRelativePath || b.name, undefined, { numeric: true }));

    if (!photos.length) {
      alert('No photos found. Choose JPEG, PNG or WebP files.');
      return;
    }

    running = true;
    window.addEventListener('beforeunload', warnOnLeave);
    Object.assign(stats, { total: photos.length, added: 0, skipped: 0, failed: 0 });
    log.innerHTML = '';
    doneBox.hidden = true;
    progress.hidden = false;
    dropzone.classList.add('opacity-50', 'pe-none');

    try {
      setSummary(`Checking ${photos.length} files…`);
      const jobs = [];
      for (const [i, file] of photos.entries()) {
        jobs.push({ file, hash: await hashFile(file) });
        if (i % 20 === 0) setBar(i / photos.length * 100, `Checking files… ${i + 1} of ${photos.length}`);
      }

      const existing = await existingHashes(jobs.map((j) => j.hash));
      const seen = new Set();
      const todo = [];
      for (const job of jobs) {
        if (existing.has(job.hash) || seen.has(job.hash)) {
          stats.skipped++;
        } else {
          seen.add(job.hash);
          job.sort = nextSort++;
          todo.push(job);
        }
      }

      updateProgress();
      await runPool(todo, uploadJob, CONCURRENCY);
    } catch (e) {
      addLog('The upload stopped: ' + e.message, 'danger');
    } finally {
      running = false;
      window.removeEventListener('beforeunload', warnOnLeave);
      dropzone.classList.remove('opacity-50', 'pe-none');
      updateProgress();
      finish();
    }
  }

  async function runPool(items, worker, size) {
    let index = 0;
    const lanes = Array.from({ length: Math.min(size, items.length) }, async () => {
      while (index < items.length) {
        await worker(items[index++]);
      }
    });
    await Promise.all(lanes);
  }

  async function uploadJob(job) {
    let blob;
    try {
      blob = await resize(job.file);
    } catch (e) {
      stats.failed++;
      addLog(`${job.file.name}: this browser can’t open it. Save it as a JPEG and try again.`, 'danger');
      updateProgress();
      return;
    }

    let lastError = '';
    for (let attempt = 1; attempt <= MAX_ATTEMPTS; attempt++) {
      const body = new FormData();
      body.append(cfg.csrfName, csrfHash);
      body.append('photo', blob, job.file.name.replace(/\.[^.]+$/, '') + '.jpg');
      body.append('hash', job.hash);
      body.append('name', job.file.name);
      body.append('sort', job.sort);

      let response;
      let data = {};
      try {
        response = await fetch(cfg.uploadUrl, {
          method: 'POST',
          body,
          headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
          credentials: 'same-origin',
        });
        data = (await readJson(response)) || {};
        lastError = data.error || describe(response, data);
      } catch (e) {
        response = null; // network drop: retry
        lastError = 'no connection to the server';
      }

      if (data.csrf) csrfHash = data.csrf;

      if (response && response.ok) {
        if (data.status === 'added') {
          stats.added++;
          addThumb(data.photo);
        } else {
          stats.skipped++;
        }
        updateProgress();
        return;
      }

      // 4xx other than an expired token is the file's fault; don't retry.
      if (response && response.status >= 400 && response.status < 500 && response.status !== 403) {
        stats.failed++;
        addLog(`${job.file.name}: ${data.error || 'rejected (' + response.status + ')'}`, 'danger');
        updateProgress();
        return;
      }

      // Expired session token, server hiccup or network drop.
      if (!response || response.status === 403 || response.redirected || response.badJson) {
        await refreshToken();
      }

      if (attempt < MAX_ATTEMPTS) {
        await sleep([2000, 5000, 10000][attempt - 1] || 10000);
      }
    }

    stats.failed++;
    addLog(`${job.file.name}: upload failed after ${MAX_ATTEMPTS} tries (${lastError}). Drop the folder again to retry the missing ones.`, 'danger');
    updateProgress();
  }

  // ---------------------------------------------------------------------
  // Image work
  // ---------------------------------------------------------------------

  async function resize(file) {
    // createImageBitmap applies the EXIF rotation by default.
    const bitmap = await createImageBitmap(file, { imageOrientation: 'from-image' });
    const scale = Math.min(1, WEB_SIZE / bitmap.width, WEB_SIZE / bitmap.height);
    const width = Math.max(1, Math.round(bitmap.width * scale));
    const height = Math.max(1, Math.round(bitmap.height * scale));

    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#fff';
    ctx.fillRect(0, 0, width, height);
    ctx.imageSmoothingQuality = 'high';
    ctx.drawImage(bitmap, 0, 0, width, height);
    bitmap.close?.();

    const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.92));
    canvas.width = canvas.height = 0; // release memory promptly on mobile Safari
    if (!blob) throw new Error('encode failed');
    return blob;
  }

  async function hashFile(file) {
    if (window.crypto?.subtle) {
      const digest = await crypto.subtle.digest('SHA-256', await file.arrayBuffer());
      return [...new Uint8Array(digest)].map((b) => b.toString(16).padStart(2, '0')).join('');
    }
    // SubtleCrypto needs HTTPS, so on a plain-HTTP development site fall back
    // to hashing the file's name, size and date — enough to spot a repeat.
    return sha256Hex(new TextEncoder().encode(`${file.name}|${file.size}|${file.lastModified}`));
  }

  // ---------------------------------------------------------------------
  // Server calls
  // ---------------------------------------------------------------------

  async function existingHashes(hashes) {
    const found = new Set();
    for (let i = 0; i < hashes.length; i += CHECK_BATCH) {
      const body = new FormData();
      body.append(cfg.csrfName, csrfHash);
      hashes.slice(i, i + CHECK_BATCH).forEach((h) => body.append('hashes[]', h));

      const response = await fetch(cfg.checkUrl, {
        method: 'POST',
        body,
        headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
        credentials: 'same-origin',
      });
      const data = await readJson(response);
      if (!response.ok || !data) throw new Error('could not check for existing photos. ' + describe(response, data));
      if (data.csrf) csrfHash = data.csrf;
      data.existing.forEach((h) => found.add(h));
    }
    return found;
  }

  // Parse a JSON reply, keeping the raw text when it isn't JSON so the
  // message can say what actually came back.
  async function readJson(response) {
    const text = await response.text();
    try {
      return JSON.parse(text);
    } catch (e) {
      response.badJson = true;
      response.rawText = text;
      return null;
    }
  }

  function describe(response, data) {
    if (response.redirected) return 'The server redirected the request — you may have been signed out. Reload the page and sign in again.';
    if (data) return `The server replied ${response.status}.`;
    const snippet = (response.rawText || '').replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim().slice(0, 160);
    return `The server replied ${response.status} with something unexpected${snippet ? ': “' + snippet + '”' : ''}.`;
  }

  async function refreshToken() {
    try {
      const response = await fetch(cfg.tokenUrl, { credentials: 'same-origin', headers: { Accept: 'application/json' } });
      const data = await response.json();
      if (data.token) csrfHash = data.token;
    } catch (e) { /* the retry will report it */ }
  }

  // Keep the token fresh during long uploads (it expires after two hours).
  setInterval(() => { if (running) refreshToken(); }, 20 * 60 * 1000);

  // ---------------------------------------------------------------------
  // UI
  // ---------------------------------------------------------------------

  function updateProgress() {
    const done = stats.added + stats.skipped + stats.failed;
    setBar(stats.total ? done / stats.total * 100 : 0,
      `${done} of ${stats.total} — ${stats.added} added, ${stats.skipped} already here` +
      (stats.failed ? `, ${stats.failed} failed` : ''));
  }

  function setBar(percent, text) {
    bar.style.width = percent.toFixed(1) + '%';
    bar.setAttribute('aria-valuenow', Math.round(percent));
    setSummary(text);
  }

  function setSummary(text) {
    summary.textContent = text;
  }

  function finish() {
    doneBox.hidden = false;
    doneBox.querySelector('[data-done-text]').textContent = stats.failed
      ? `Finished with ${stats.failed} failed. Drop the same files again to retry just those.`
      : 'All done.';
    bar.classList.remove('progress-bar-animated');
  }

  function addLog(text, tone) {
    const item = document.createElement('li');
    item.className = `list-group-item small text-${tone}`;
    item.textContent = text;
    log.append(item);
  }

  function addThumb(photo) {
    if (!grid || !photo) return;
    grid.closest('[data-photos-card]')?.removeAttribute('hidden');
    const tile = document.createElement('div');
    tile.className = 'gallery-admin-photo';
    const img = document.createElement('img');
    img.src = photo.thumb;
    img.alt = photo.name || '';
    img.loading = 'lazy';
    tile.append(img);
    grid.append(tile);
  }

  function warnOnLeave(e) {
    e.preventDefault();
    e.returnValue = '';
  }

  const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

  // Compact SHA-256 for the plain-HTTP fallback above (FIPS 180-4).
  function sha256Hex(bytes) {
    const K = [];
    const H = [];
    const frac = (x) => ((x - Math.floor(x)) * 0x100000000) >>> 0;
    for (let n = 2, found = 0; found < 64; n++) {
      let prime = true;
      for (let d = 2; d * d <= n; d++) if (n % d === 0) { prime = false; break; }
      if (!prime) continue;
      if (found < 8) H.push(frac(Math.sqrt(n)));
      K.push(frac(Math.cbrt(n)));
      found++;
    }

    const length = bytes.length;
    const padded = new Uint8Array(((length + 9 + 63) >> 6) << 6);
    padded.set(bytes);
    padded[length] = 0x80;
    const view = new DataView(padded.buffer);
    view.setUint32(padded.length - 8, Math.floor(length / 0x20000000));
    view.setUint32(padded.length - 4, (length << 3) >>> 0);

    const w = new Uint32Array(64);
    const rotr = (x, n) => (x >>> n) | (x << (32 - n));
    for (let offset = 0; offset < padded.length; offset += 64) {
      for (let i = 0; i < 16; i++) w[i] = view.getUint32(offset + i * 4);
      for (let i = 16; i < 64; i++) {
        const s0 = rotr(w[i - 15], 7) ^ rotr(w[i - 15], 18) ^ (w[i - 15] >>> 3);
        const s1 = rotr(w[i - 2], 17) ^ rotr(w[i - 2], 19) ^ (w[i - 2] >>> 10);
        w[i] = (w[i - 16] + s0 + w[i - 7] + s1) >>> 0;
      }
      let [a, b, c, d, e, f, g, h] = H;
      for (let i = 0; i < 64; i++) {
        const t1 = (h + (rotr(e, 6) ^ rotr(e, 11) ^ rotr(e, 25)) + ((e & f) ^ (~e & g)) + K[i] + w[i]) >>> 0;
        const t2 = ((rotr(a, 2) ^ rotr(a, 13) ^ rotr(a, 22)) + ((a & b) ^ (a & c) ^ (b & c))) >>> 0;
        h = g; g = f; f = e; e = (d + t1) >>> 0; d = c; c = b; b = a; a = (t1 + t2) >>> 0;
      }
      [a, b, c, d, e, f, g, h].forEach((v, i) => { H[i] = (H[i] + v) >>> 0; });
    }
    return H.map((v) => v.toString(16).padStart(8, '0')).join('');
  }

  // ---------------------------------------------------------------------
  // Photo manager: selection, drag-to-reorder
  // ---------------------------------------------------------------------

  const form = el('gallery-photos-form');
  if (form && grid) {
    const deleteButton = el('gallery-delete-selected');
    const countLabel = el('gallery-selected-count');

    const refreshSelection = () => {
      const checked = grid.querySelectorAll('input[name="ids[]"]:checked');
      grid.querySelectorAll('.gallery-admin-photo').forEach((tile) => {
        tile.classList.toggle('is-selected', !!tile.querySelector('input:checked'));
      });
      countLabel.textContent = checked.length;
      deleteButton.disabled = checked.length === 0;
    };
    grid.addEventListener('change', refreshSelection);

    el('gallery-select-none')?.addEventListener('click', () => {
      grid.querySelectorAll('input[name="ids[]"]').forEach((box) => { box.checked = false; });
      refreshSelection();
    });

    form.addEventListener('submit', (e) => {
      if (e.submitter === deleteButton) {
        const n = countLabel.textContent;
        if (!confirm(`Delete ${n} photo${n === '1' ? '' : 's'}? This cannot be undone.`)) e.preventDefault();
      }
    });

    if (window.Sortable) {
      const saved = el('gallery-order-status');
      Sortable.create(grid, {
        animation: 150,
        filter: 'input, button, a',
        preventOnFilter: false,
        onEnd: async () => {
          const order = [...grid.querySelectorAll('.gallery-admin-photo[data-id]')].map((t) => t.dataset.id);
          const body = new FormData();
          body.append(cfg.csrfName, csrfHash);
          // One comma-separated field: thousands of order[] fields would
          // exceed PHP's max_input_vars and be silently cut off.
          body.append('order', order.join(','));
          saved.textContent = 'Saving order…';
          try {
            const response = await fetch(cfg.reorderUrl, {
              method: 'POST', body, credentials: 'same-origin',
              headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            });
            const data = await response.json();
            if (data.csrf) csrfHash = data.csrf;
            saved.textContent = response.ok ? 'Order saved.' : 'Order not saved — please reload.';
          } catch (e) {
            saved.textContent = 'Order not saved — please reload.';
          }
        },
      });
    }
  }
})();
