<div class="variables-section">
    <label class="form-label">Variables Helper</label>
    <div class="variable-insert-row">
        <div class="variable-input-wrap">
            <input
                type="text"
                id="insert-variable-input"
                class="form-control form-control-sm"
                placeholder="Search or type variable (e.g. property.address)"
                autocomplete="off"
            >
        </div>
        <button type="button" id="insert-variable-button" class="btn btn-sm btn-primary variable-insert-btn">
            <i class="fa fa-plus"></i> Insert
        </button>
    </div>
    <p class="text-muted variable-helper-hint">
        Type a variable name and click Insert, or click a variable below to insert it.
    </p>
    <div class="variable-search-wrap">
        <input type="text" id="variable-search-input" class="form-control form-control-sm variable-search-input" placeholder="Filter variables..." autocomplete="off">
    </div>
    <div class="variable-list" id="variable-list" style="max-height: calc(100vh - 250px); overflow-y: auto;">
        <div class="variable-group" data-group="property">
            <strong class="variable-group-title">Property Variables</strong>
            <ul class="variable-group-list">
                <li class="variable-item" data-variable="property.address"><code>{{ '{' }}{{ 'property.address' }}{{ '}' }}</code> – Property Address</li>
                <li class="variable-item" data-variable="property.postcode"><code>{{ '{' }}{{ 'property.postcode' }}{{ '}' }}</code> – Property Postcode</li>
                <li class="variable-item" data-variable="property.asking_price"><code>{{ '{' }}{{ 'property.asking_price' }}{{ '}' }}</code> – Asking Price</li>
            </ul>
        </div>

        <div class="variable-group" data-group="buyer">
            <strong class="variable-group-title">Buyer Variables</strong>
            <ul class="variable-group-list">
                <li class="variable-item" data-variable="buyer.name"><code>{{ '{' }}{{ 'buyer.name' }}{{ '}' }}</code> – Buyer Name</li>
                <li class="variable-item" data-variable="buyer.email"><code>{{ '{' }}{{ 'buyer.email' }}{{ '}' }}</code> – Buyer Email</li>
            </ul>
        </div>

        <div class="variable-group" data-group="offer">
            <strong class="variable-group-title">Offer Variables</strong>
            <ul class="variable-group-list">
                <li class="variable-item" data-variable="offer.offer_amount"><code>{{ '{' }}{{ 'offer.offer_amount' }}{{ '}' }}</code> – Offer Amount</li>
                <li class="variable-item" data-variable="offer.created_at"><code>{{ '{' }}{{ 'offer.created_at' }}{{ '}' }}</code> – Offer Created Date</li>
                <li class="variable-item" data-variable="offer.funding_type"><code>{{ '{' }}{{ 'offer.funding_type' }}{{ '}' }}</code> – Funding Type</li>
            </ul>
        </div>

        <div class="variable-group" data-group="viewing">
            <strong class="variable-group-title">Viewing Variables</strong>
            <ul class="variable-group-list">
                <li class="variable-item" data-variable="viewing.viewing_date"><code>{{ '{' }}{{ 'viewing.viewing_date' }}{{ '}' }}</code> – Viewing Date</li>
                <li class="variable-item" data-variable="viewing.viewing_time"><code>{{ '{' }}{{ 'viewing.viewing_time' }}{{ '}' }}</code> – Viewing Time</li>
                <li class="variable-item" data-variable="viewing.status"><code>{{ '{' }}{{ 'viewing.status' }}{{ '}' }}</code> – Viewing Status</li>
            </ul>
        </div>

        <div class="variable-group" data-group="valuation">
            <strong class="variable-group-title">Valuation Variables</strong>
            <ul class="variable-group-list">
                <li class="variable-item" data-variable="valuation.property_address"><code>{{ '{' }}{{ 'valuation.property_address' }}{{ '}' }}</code> – Valuation Property Address</li>
                <li class="variable-item" data-variable="valuation.postcode"><code>{{ '{' }}{{ 'valuation.postcode' }}{{ '}' }}</code> – Valuation Postcode</li>
                <li class="variable-item" data-variable="valuation.valuation_date"><code>{{ '{' }}{{ 'valuation.valuation_date' }}{{ '}' }}</code> – Valuation Date</li>
            </ul>
        </div>

        <div class="variable-group" data-group="instruction">
            <strong class="variable-group-title">Instruction Variables</strong>
            <ul class="variable-group-list">
                <li class="variable-item" data-variable="instruction.signed_at"><code>{{ '{' }}{{ 'instruction.signed_at' }}{{ '}' }}</code> – Instruction Signed Date</li>
                <li class="variable-item" data-variable="instruction.fee_percentage"><code>{{ '{' }}{{ 'instruction.fee_percentage' }}{{ '}' }}</code> – Fee Percentage</li>
                <li class="variable-item" data-variable="instruction.status"><code>{{ '{' }}{{ 'instruction.status' }}{{ '}' }}</code> – Instruction Status</li>
            </ul>
        </div>

        <div class="variable-group" data-group="recipient">
            <strong class="variable-group-title">User & Recipient Variables</strong>
            <ul class="variable-group-list">
                <li class="variable-item" data-variable="recipient.name"><code>{{ '{' }}{{ 'recipient.name' }}{{ '}' }}</code> – Recipient Name</li>
                <li class="variable-item" data-variable="user.email"><code>{{ '{' }}{{ 'user.email' }}{{ '}' }}</code> – User Email</li>
                <li class="variable-item" data-variable="password"><code>{{ '{' }}{{ 'password' }}{{ '}' }}</code> – Password</li>
            </ul>
        </div>

        <div class="variable-group" data-group="general">
            <strong class="variable-group-title">General & Widget Variables</strong>
            <ul class="variable-group-list">
                <li class="variable-item" data-variable="url"><code>{{ '{' }}{{ 'url' }}{{ '}' }}</code> – Link URL</li>
                <li class="variable-item" data-variable="text"><code>{{ '{' }}{{ 'text' }}{{ '}' }}</code> – Text</li>
                <li class="variable-item" data-variable="message"><code>{{ '{' }}{{ 'message' }}{{ '}' }}</code> – Message</li>
                <li class="variable-item" data-variable="title"><code>{{ '{' }}{{ 'title' }}{{ '}' }}</code> – Title</li>
                <li class="variable-item" data-variable="year"><code>{{ '{' }}{{ 'year' }}{{ '}' }}</code> – Year</li>
                <li class="variable-item" data-variable="item1"><code>{{ '{' }}{{ 'item1' }}{{ '}' }}</code> – Item 1</li>
                <li class="variable-item" data-variable="item2"><code>{{ '{' }}{{ 'item2' }}{{ '}' }}</code> – Item 2</li>
                <li class="variable-item" data-variable="item3"><code>{{ '{' }}{{ 'item3' }}{{ '}' }}</code> – Item 3</li>
                <li class="variable-item" data-variable="content"><code>{{ '{' }}{{ 'content' }}{{ '}' }}</code> – Content</li>
                <li class="variable-item" data-variable="logo_url"><code>{{ '{' }}{{ 'logo_url' }}{{ '}' }}</code> – Logo URL</li>
            </ul>
        </div>
    </div>
</div>

