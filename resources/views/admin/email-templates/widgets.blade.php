@extends('layouts.admin')

@section('title', 'Email Widgets')

@push('styles')
<style>
    h2 {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 25px;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .container {
        max-width: 1180px;
        margin: 35px auto;
        padding: 0 22px;
    }

    .card-header {
        background: transparent;
        color: var(--black);
        padding: 0;
        margin: 0 0 20px 0;
        border: none;
    }

    .card-header h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: var(--black);
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .table th {
        background: var(--abodeology-teal);
        color: var(--white);
        padding: 12px;
        text-align: left;
        font-size: 14px;
        font-weight: 600;
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--line-grey);
        font-size: 14px;
    }

    .table tr:hover {
        background: #f9f9f9;
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        display: inline-block;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: background 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-primary:hover {
        background: #25A29F;
    }

    .btn-outline-secondary {
        background: transparent;
        color: #666;
        border: 1px solid #ddd;
    }

    .btn-outline-secondary:hover {
        background: #f5f5f5;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-success {
        background: #d4edda;
        color: #155724;
    }

    .badge-secondary {
        background: #e0e0e0;
        color: #666;
    }

    code {
        background: #f4f4f4;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
        font-family: 'Courier New', monospace;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }

    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: var(--white);
        padding: 30px;
        border-radius: 12px;
        max-width: 800px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0px 4px 20px rgba(0,0,0,0.2);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--line-grey);
    }

    .modal-header h5 {
        margin: 0;
        font-size: 20px;
        color: var(--abodeology-teal);
    }

    .close-btn {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #666;
    }

    .close-btn:hover {
        color: #000;
    }

    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--line-grey);
        border-radius: 6px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        resize: vertical;
    }

    .text-muted {
        color: #666;
    }

    .text-center {
        text-align: center;
    }

    .preview-frame {
        border: 1px solid var(--line-grey);
        border-radius: 8px;
        background: #f9f9f9;
        padding: 20px;
        margin-top: 15px;
        min-height: 200px;
        max-height: 600px;
        overflow-y: auto;
    }

    .preview-frame iframe {
        width: 100%;
        border: none;
        min-height: 400px;
        background: white;
    }

    .preview-note {
        background: #fff3cd;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-size: 13px;
        color: #856404;
    }
</style>
@endpush

@section('content')
    <div class="container">
        <div class="page-header">
            <h2>Email Widgets</h2>
            <p class="page-subtitle">Reusable HTML components for building email templates.</p>
            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary"><i class="fa fa-arrow-left"></i> Back to Templates</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @forelse($widgets as $category => $categoryWidgets)
        <div class="card">
            <div class="card-header">
                <h5>{{ $category }} <span class="badge badge-secondary">{{ $categoryWidgets->count() }}</span></h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Key</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th style="width: 200px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categoryWidgets as $widget)
                            <tr>
                                <td><strong>{{ $widget->name }}</strong></td>
                                <td><code>{{ $widget->key }}</code></td>
                                <td class="text-muted">{{ $widget->description ?? 'No description' }}</td>
                                <td>
                                    @if($widget->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <button 
                                        type="button" 
                                        class="btn btn-primary btn-sm" 
                                        onclick="showWidgetPreview({{ $widget->id }}, `{{ addslashes($widget->html) }}`, '{{ addslashes($widget->name) }}')"
                                        style="margin-right: 5px;"
                                    >
                                        Preview
                                    </button>
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-secondary btn-sm" 
                                        onclick="showWidgetModal({{ $widget->id }}, '{{ addslashes($widget->name) }}', '{{ addslashes($widget->category) }}', '{{ addslashes($widget->key) }}', '{{ addslashes($widget->description ?? '') }}', `{{ addslashes($widget->html) }}`)"
                                    >
                                        HTML
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @empty
            <div class="card">
                <div class="card-body text-center text-muted">
                    <p>No widgets found. Please run the seeder to populate widgets.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Modal for previewing widget --}}
    <div id="previewModal" class="modal">
        <div class="modal-content" style="max-width: 900px;">
            <div class="modal-header">
                <h5 id="previewModalTitle">Widget Preview</h5>
                <button type="button" class="close-btn" onclick="closePreviewModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="preview-note">
                    <strong>Note:</strong> This is a preview with sample data. Variables like <code>@{{property.address}}</code> are replaced with example values.
                </div>
                <div class="preview-frame" id="previewFrame"></div>
            </div>
        </div>
    </div>

    {{-- Modal for viewing widget HTML --}}
    <div id="widgetModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitle"></h5>
                <button type="button" class="close-btn" onclick="closeWidgetModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 20px;">
                    <strong>Category:</strong> <span id="modalCategory"></span><br>
                    <strong>Key:</strong> <code id="modalKey"></code><br>
                    <strong>Description:</strong> <span id="modalDescription" class="text-muted"></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <label><strong>HTML Code:</strong></label>
                    <textarea id="modalHTML" rows="15" readonly></textarea>
                </div>
                <div>
                    <button 
                        type="button" 
                        class="btn btn-primary btn-sm" 
                        onclick="copyWidgetHTML()"
                    >
                        Copy HTML
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sample data for preview
        const logoUrl = '{{ asset("media/abodeology-logo.png") }}';
    @verbatim
        const sampleData = {
            'property.address': '123 Example Street',
            'property.postcode': 'SW1A 1AA',
            'property.asking_price': '450,000',
            'buyer.name': 'John Doe',
            'buyer.email': 'john.doe@example.com',
            'offer.offer_amount': '425,000',
            'offer.funding_type': 'Cash',
            'viewing.viewing_date': 'Monday, January 15, 2024',
            'viewing.viewing_time': '2:00 PM',
            'viewing.status': 'Confirmed',
            'valuation.property_address': '123 Example Street',
            'valuation.postcode': 'SW1A 1AA',
            'valuation.valuation_date': 'Friday, January 12, 2024',
            'instruction.signed_at': 'Monday, January 15, 2024',
            'instruction.fee_percentage': '1.5',
            'instruction.status': 'Signed',
            'user.email': 'user@example.com',
            'password': 'TempPass123!',
            'recipient.name': 'Jane Smith',
            'logo_url': logoUrl,
            'company_name': 'Abodeology®',
            'company_address': '123 Business Street, London',
            'company_phone': '+44 20 1234 5678',
            'company_email': 'support@abodeology.co.uk',
            'year': new Date().getFullYear(),
            'url': '#',
            'text': 'Click Here',
            'message': 'This is a sample action required message.',
            'title': 'Action Required',
            'image_url': 'https://via.placeholder.com/600x300?text=Sample+Image',
            'alt_text': 'Sample Image',
            'link_url': '#',
            'caption': 'This is a sample image caption',
            'left_content': 'Left column content',
            'right_content': 'Right column content',
            'content': 'Widget content goes here',
            'height': '20'
        };

        function replaceVariables(html) {
            let result = html;
            // Replace all variable patterns
            const openBrace = '{';
            const closeBrace = '}';
            const variablePattern = new RegExp(openBrace + openBrace + '([^' + closeBrace + ']+)' + closeBrace + closeBrace, 'g');
            result = result.replace(variablePattern, function(match, variable) {
                variable = variable.trim();
                // Handle nested properties
                if (sampleData[variable] !== undefined) {
                    return sampleData[variable];
                }
                // Try to find partial matches for nested properties
                for (let key in sampleData) {
                    if (variable.includes(key.split('.')[0])) {
                        return sampleData[key];
                    }
                }
                // Return placeholder if not found
                return '[Sample ' + variable + ']';
            });
            return result;
        }

        function showWidgetPreview(id, html, name) {
            document.getElementById('previewModalTitle').textContent = 'Preview: ' + name;
            const previewFrame = document.getElementById('previewFrame');
            
            // Replace variables with sample data
            let previewHtml = replaceVariables(html);
            
            // Wrap in email-friendly container
            const fullHtml = `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 0;
                            padding: 20px;
                            background: #f5f5f5;
                        }
                        .email-wrapper {
                            max-width: 600px;
                            margin: 0 auto;
                            background: white;
                            padding: 20px;
                            border-radius: 8px;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                        }
                    </style>
                </head>
                <body>
                    <div class="email-wrapper">
                        ${previewHtml}
                    </div>
                </body>
                </html>
            `;
            
            // Create iframe for safe rendering
            previewFrame.innerHTML = '<iframe srcdoc="' + escapeHtml(fullHtml) + '"></iframe>';
            document.getElementById('previewModal').classList.add('show');
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        function closePreviewModal() {
            document.getElementById('previewModal').classList.remove('show');
        }

        function showWidgetModal(id, name, category, key, description, html) {
            document.getElementById('modalTitle').textContent = name;
            document.getElementById('modalCategory').textContent = category;
            document.getElementById('modalKey').textContent = key;
            document.getElementById('modalDescription').textContent = description || 'No description';
            document.getElementById('modalHTML').value = html;
            document.getElementById('widgetModal').classList.add('show');
        }

        function closeWidgetModal() {
            document.getElementById('widgetModal').classList.remove('show');
        }

        function copyWidgetHTML() {
            const textarea = document.getElementById('modalHTML');
            textarea.select();
            document.execCommand('copy');
            
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            button.style.background = '#28a745';
            
            setTimeout(() => {
                button.textContent = originalText;
                button.style.background = '';
            }, 2000);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const previewModal = document.getElementById('previewModal');
            const widgetModal = document.getElementById('widgetModal');
            if (event.target == previewModal) {
                closePreviewModal();
            }
            if (event.target == widgetModal) {
                closeWidgetModal();
            }
        }
    @endverbatim
    </script>
@endsection

