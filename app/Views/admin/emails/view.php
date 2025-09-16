<div class="container py-4">
    <h3>Email #<?= $email['id'] ?></h3>
    <p><strong>To:</strong> <?= esc($email['to_name']) ?> &lt;<?= esc($email['to_email']) ?>&gt;</p>
    <p><strong>Subject:</strong> <?= esc($email['subject']) ?></p>
    <p><strong>Status:</strong> <?= esc($email['status']) ?></p>
    <p><strong>Sent At:</strong> <?= esc($email['sent_at']) ?></p>

    <h5>HTML Body</h5>
    <div class="border p-3 mb-3"><?= $email['body_html'] ?></div>

    <h5>Text Body</h5>
    <pre class="border p-3"><?= esc($email['body_text']) ?></pre>

    <a href="<?= base_url('admin/emails') ?>" class="btn btn-secondary">Back</a>
</div>
