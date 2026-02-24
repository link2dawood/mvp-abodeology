<div class="variables-section">
    <label class="form-label">Variables Helper</label>
    <div class="mb-2">
        <input
            type="text"
            id="insert-variable-input"
            class="form-control form-control-sm"
            placeholder="e.g. property.address or buyer.name"
            style="margin-bottom: 10px;"
        >
        <button id="insert-variable-button" class="btn btn-sm btn-outline-secondary" style="width: 100%;">
            Insert
        </button>
    </div>
    <p class="text-muted" style="font-size: 12px; margin-bottom: 10px;">
        Use variables in the template body with the syntax <code>{{ '{' }}{{ 'variable' }}{{ '}' }}</code>,
        for example <code>{{ '{' }}{{ 'property.address' }}{{ '}' }}</code> or <code>{{ '{' }}{{ 'buyer.name' }}{{ '}' }}</code>.
    </p>
    <div class="variable-list" style="max-height: calc(100vh - 250px); overflow-y: auto;">
        <div style="margin-bottom: 15px;">
            <strong style="color: #2CB8B4; font-size: 13px;">Property Variables</strong>
            <ul class="mb-2" style="font-size: 12px; padding-left: 20px;">
                <li><code>{{ '{' }}{{ 'property.address' }}{{ '}' }}</code> – Property Address</li>
                <li><code>{{ '{' }}{{ 'property.postcode' }}{{ '}' }}</code> – Property Postcode</li>
                <li><code>{{ '{' }}{{ 'property.asking_price' }}{{ '}' }}</code> – Asking Price</li>
            </ul>
        </div>

        <div style="margin-bottom: 15px;">
            <strong style="color: #2CB8B4; font-size: 13px;">Buyer Variables</strong>
            <ul class="mb-2" style="font-size: 12px; padding-left: 20px;">
                <li><code>{{ '{' }}{{ 'buyer.name' }}{{ '}' }}</code> – Buyer Name</li>
                <li><code>{{ '{' }}{{ 'buyer.email' }}{{ '}' }}</code> – Buyer Email</li>
            </ul>
        </div>

        <div style="margin-bottom: 15px;">
            <strong style="color: #2CB8B4; font-size: 13px;">Offer Variables</strong>
            <ul class="mb-2" style="font-size: 12px; padding-left: 20px;">
                <li><code>{{ '{' }}{{ 'offer.offer_amount' }}{{ '}' }}</code> – Offer Amount</li>
                <li><code>{{ '{' }}{{ 'offer.created_at' }}{{ '}' }}</code> – Offer Created Date</li>
                <li><code>{{ '{' }}{{ 'offer.funding_type' }}{{ '}' }}</code> – Funding Type</li>
            </ul>
        </div>

        <div style="margin-bottom: 15px;">
            <strong style="color: #2CB8B4; font-size: 13px;">Viewing Variables</strong>
            <ul class="mb-2" style="font-size: 12px; padding-left: 20px;">
                <li><code>{{ '{' }}{{ 'viewing.viewing_date' }}{{ '}' }}</code> – Viewing Date</li>
                <li><code>{{ '{' }}{{ 'viewing.viewing_time' }}{{ '}' }}</code> – Viewing Time</li>
                <li><code>{{ '{' }}{{ 'viewing.status' }}{{ '}' }}</code> – Viewing Status</li>
            </ul>
        </div>

        <div style="margin-bottom: 15px;">
            <strong style="color: #2CB8B4; font-size: 13px;">Valuation Variables</strong>
            <ul class="mb-2" style="font-size: 12px; padding-left: 20px;">
                <li><code>{{ '{' }}{{ 'valuation.property_address' }}{{ '}' }}</code> – Valuation Property Address</li>
                <li><code>{{ '{' }}{{ 'valuation.postcode' }}{{ '}' }}</code> – Valuation Postcode</li>
                <li><code>{{ '{' }}{{ 'valuation.valuation_date' }}{{ '}' }}</code> – Valuation Date</li>
            </ul>
        </div>

        <div style="margin-bottom: 15px;">
            <strong style="color: #2CB8B4; font-size: 13px;">Instruction Variables</strong>
            <ul class="mb-2" style="font-size: 12px; padding-left: 20px;">
                <li><code>{{ '{' }}{{ 'instruction.signed_at' }}{{ '}' }}</code> – Instruction Signed Date</li>
                <li><code>{{ '{' }}{{ 'instruction.fee_percentage' }}{{ '}' }}</code> – Fee Percentage</li>
                <li><code>{{ '{' }}{{ 'instruction.status' }}{{ '}' }}</code> – Instruction Status</li>
            </ul>
        </div>

        <div style="margin-bottom: 15px;">
            <strong style="color: #2CB8B4; font-size: 13px;">User & Recipient Variables</strong>
            <ul class="mb-2" style="font-size: 12px; padding-left: 20px;">
                <li><code>{{ '{' }}{{ 'recipient.name' }}{{ '}' }}</code> – Recipient Name</li>
                <li><code>{{ '{' }}{{ 'user.email' }}{{ '}' }}</code> – User Email</li>
                <li><code>{{ '{' }}{{ 'password' }}{{ '}' }}</code> – Password</li>
            </ul>
        </div>

        <div style="margin-bottom: 15px;">
            <strong style="color: #2CB8B4; font-size: 13px;">General & Widget Variables</strong>
            <ul class="mb-2" style="font-size: 12px; padding-left: 20px;">
                <li><code>{{ '{' }}{{ 'url' }}{{ '}' }}</code> – Link URL</li>
                <li><code>{{ '{' }}{{ 'text' }}{{ '}' }}</code> – Text</li>
                <li><code>{{ '{' }}{{ 'message' }}{{ '}' }}</code> – Message</li>
                <li><code>{{ '{' }}{{ 'title' }}{{ '}' }}</code> – Title</li>
                <li><code>{{ '{' }}{{ 'year' }}{{ '}' }}</code> – Year</li>
                <li><code>{{ '{' }}{{ 'item1' }}{{ '}' }}</code> – Item 1</li>
                <li><code>{{ '{' }}{{ 'item2' }}{{ '}' }}</code> – Item 2</li>
                <li><code>{{ '{' }}{{ 'item3' }}{{ '}' }}</code> – Item 3</li>
                <li><code>{{ '{' }}{{ 'content' }}{{ '}' }}</code> – Content</li>
                <li><code>{{ '{' }}{{ 'logo_url' }}{{ '}' }}</code> – Logo URL</li>
            </ul>
        </div>
    </div>
</div>

