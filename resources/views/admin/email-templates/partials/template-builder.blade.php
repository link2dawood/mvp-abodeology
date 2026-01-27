@push('styles')
    {{-- Summernote CSS (free, CDN-hosted) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
    <style>
        .template-builder-grid {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(260px, 1fr);
            gap: 20px;
        }

        @media (max-width: 992px) {
            .template-builder-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        .variable-list {
            max-height: 260px;
            overflow: auto;
            border: 1px solid var(--line-grey);
            border-radius: 8px;
            padding: 10px 12px;
            background: #fafafa;
            font-size: 13px;
        }

        .variable-list code {
            background: #eee;
            padding: 2px 5px;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
@endpush

@push('scripts')
    {{-- Summernote JS (free, CDN-hosted) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editor = $('#template-body-editor');

            if (editor.length) {
                editor.summernote({
                    placeholder: 'Write your email template here...',
                    tabsize: 2,
                    height: 320,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture', 'table']],
                        ['view', ['codeview', 'help']]
                    ]
                });

                // Static variable insertion buttons
                document.querySelectorAll('[data-insert-variable]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const variable = this.getAttribute('data-insert-variable');
                        editor.summernote('editor.insertText', '{{' + variable + '}}');
                    });
                });

                // Manual variable insertion from input field
                const insertButton = document.getElementById('insert-variable-button');
                const input = document.getElementById('insert-variable-input');

                if (insertButton && input) {
                    insertButton.addEventListener('click', function (e) {
                        e.preventDefault();
                        const variable = (input.value || '').trim();
                        if (!variable) return;
                        editor.summernote('editor.insertText', '{{' + variable + '}}');
                    });
                }
            }
        });
    </script>
@endpush

<div class="template-builder-grid mb-3">
    <div>
        <label for="template-body-editor" class="form-label">Body</label>
        <textarea
            id="template-body-editor"
            name="body"
            class="form-control @error('body') is-invalid @enderror"
        >{{ old('body', $template->body ?? '') }}</textarea>
        @error('body')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="form-label">Variables helper</label>
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
        <p class="text-muted" style="font-size: 12px; margin-bottom: 8px;">
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
</div>
