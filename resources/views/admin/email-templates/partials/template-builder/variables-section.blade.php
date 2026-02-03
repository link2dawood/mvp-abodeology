<div class="variables-section">
    <label class="form-label">Variables Helper</label>
    <div class="mb-2 d-flex">
        <input
            type="text"
            id="insert-variable-input"
            class="form-control form-control-sm me-2"
            placeholder="e.g. property.address or buyer.name"
        >
        <button id="insert-variable-button" class="btn btn-sm btn-outline-secondary">
            Insert
        </button>
    </div>
    <p class="text-muted" style="font-size: 12px; margin-bottom: 10px;">
        Use variables in the template body with the syntax <code>{{ '{' }}{{ 'variable' }}{{ '}' }}</code>,
        for example <code>{{ '{' }}{{ 'property.address' }}{{ '}' }}</code> or <code>{{ '{' }}{{ 'buyer.name' }}{{ '}' }}</code>.
    </p>
    <div class="variable-list">
        <strong>Common examples:</strong>
        <ul class="mb-1">
            <li><code>{{ '{' }}{{ 'property.address' }}{{ '}' }}</code> – Property address</li>
            <li><code>{{ '{' }}{{ 'property.postcode' }}{{ '}' }}</code> – Property postcode</li>
            <li><code>{{ '{' }}{{ 'property.asking_price' }}{{ '}' }}</code> – Asking price</li>
            <li><code>{{ '{' }}{{ 'buyer.name' }}{{ '}' }}</code> – Buyer name</li>
            <li><code>{{ '{' }}{{ 'buyer.email' }}{{ '}' }}</code> – Buyer email</li>
            <li><code>{{ '{' }}{{ 'offer.offer_amount' }}{{ '}' }}</code> – Offer amount</li>
            <li><code>{{ '{' }}{{ 'viewing.viewing_date' }}{{ '}' }}</code> – Viewing date</li>
            <li><code>{{ '{' }}{{ 'recipient.name' }}{{ '}' }}</code> – Email recipient name</li>
        </ul>
        <p class="text-muted mb-0">
            Additional variables depend on the action and data passed from the system.
        </p>
    </div>
</div>

