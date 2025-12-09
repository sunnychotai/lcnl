<div class="lcnl-card shadow-soft border-0 mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">

            <div class="col-md-3">
                <label class="form-label small mb-1">Status</label>
                <select id="filterStatus" class="form-select form-select-sm">
                    <option value="all">All</option>
                    <option value="pending">Pending</option>
                    <option value="active">Active</option>
                    <option value="disabled">Disabled</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small mb-1">Membership Type</label>
                <select id="filterType" class="form-select form-select-sm">
                    <option value="all">All</option>
                    <option value="life">Life</option>
                    <option value="standard">Standard</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small mb-1">City</label>
                <input type="text" id="filterCity" class="form-control form-control-sm" placeholder="e.g. Harrow">
            </div>

            <div class="col-auto">
                <button id="btnApply" class="btn btn-sm btn-brand">
                    <i class="bi bi-filter"></i>
                </button>
            </div>

            <div class="col-auto ms-auto">
                <a id="btnExport" href="#" class="btn btn-sm btn-outline-brand">
                    <i class="bi bi-download"></i> CSV
                </a>
            </div>

        </div>
    </div>
</div>
