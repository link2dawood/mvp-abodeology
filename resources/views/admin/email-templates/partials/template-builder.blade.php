@push('styles')
    {{-- GrapesJS CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/grapesjs@0.21.7/dist/css/grapes.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/grapesjs-preset-newsletter@1.0.1/dist/grapesjs-preset-newsletter.min.css" rel="stylesheet">
    <style>
        /* GrapesJS Container */
        .gjs-container {
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }

        .gjs-editor {
            border: 2px solid var(--line-grey);
            border-radius: 8px;
            overflow: hidden;
        }

        .gjs-editor:focus-within {
            border-color: var(--abodeology-teal);
            box-shadow: 0 0 0 4px rgba(44, 184, 180, 0.15);
        }

        /* Template Builder Container */
        .template-builder-container {
            margin-top: 20px;
        }

        .template-builder-header {
            margin-bottom: 15px;
            border-bottom: 2px solid var(--line-grey);
        }

        .builder-tabs {
            display: flex;
            gap: 5px;
        }

        .builder-tab {
            padding: 10px 20px;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #666;
            transition: all 0.3s ease;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }

        .builder-tab:hover {
            color: var(--abodeology-teal);
            background: rgba(44, 184, 180, 0.05);
        }

        .builder-tab.active {
            color: var(--abodeology-teal);
            border-bottom-color: var(--abodeology-teal);
        }

        .builder-pane {
            display: none;
        }

        .builder-pane.active {
            display: block;
        }

        /* Preview Container */
        .preview-container {
            background: var(--white);
            border: 2px solid var(--line-grey);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 20px;
        }

        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-bottom: 2px solid var(--line-grey);
        }

        .preview-subject-display {
            font-size: 14px;
            color: #333;
        }

        .preview-subject-display strong {
            color: var(--abodeology-teal);
        }

        .preview-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .preview-refresh-btn {
            padding: 6px 12px;
            background: var(--abodeology-teal);
            color: var(--white);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .preview-refresh-btn:hover {
            background: #25A29F;
        }

        .preview-device-select {
            padding: 6px 12px;
            border: 2px solid var(--line-grey);
            border-radius: 6px;
            font-size: 13px;
            background: var(--white);
            cursor: pointer;
        }

        .preview-device-select:focus {
            outline: none;
            border-color: var(--abodeology-teal);
        }

        .preview-frame {
            padding: 30px;
            background: #f4f4f4;
            min-height: 500px;
            max-height: 800px;
            overflow-y: auto;
            position: relative;
        }

        .preview-frame.desktop {
            max-width: 680px;
            margin: 0 auto;
        }

        .preview-frame.mobile {
            max-width: 420px;
            margin: 0 auto;
        }

        .preview-frame iframe {
            width: 100%;
            height: 100%;
            border: none;
            background: var(--white);
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .preview-loading {
            text-align: center;
            padding: 50px;
            color: #666;
            font-size: 14px;
        }

        /* Hidden inputs for form submission */
        #html-content-input,
        #json-content-input {
            display: none;
        }
    </style>
@endpush

@push('scripts')
    {{-- GrapesJS JS --}}
    <script src="https://cdn.jsdelivr.net/npm/grapesjs@0.21.7/dist/grapes.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/grapesjs-preset-newsletter@1.0.1/dist/grapesjs-preset-newsletter.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let editor;
            const widgetsUrl = '{{ route("admin.email-templates.widgets") }}';
            const initialHtml = @json(old('html_content', isset($template) && $template->html_content ? $template->html_content : ''));
            const initialJson = @json(old('json_content', isset($template) && $template->json_content ? $template->json_content : ''));

            // Initialize GrapesJS
            function initGrapesJS() {
                if (typeof grapesjs === 'undefined') {
                    setTimeout(initGrapesJS, 100);
                    return;
                }

                editor = grapesjs.init({
                    container: '#gjs-editor',
                    height: '600px',
                    storageManager: false,
                    fromElement: false,
                    plugins: ['gjs-preset-newsletter'],
                    pluginsOpts: {
                        'gjs-preset-newsletter': {
                            inlineCss: true,
                            modalTitleImport: 'Import template',
                            textCleanCanvas: 'Clear all',
                            textImport: 'Import',
                            textExport: 'Export',
                        }
                    },
                    deviceManager: {
                        devices: [
                            {
                                name: 'Desktop',
                                width: '',
                            },
                            {
                                name: 'Mobile',
                                width: '420px',
                            }
                        ]
                    },
                    blockManager: {
                        appendTo: '#gjs-blocks',
                    },
                    layerManager: {
                        appendTo: '#gjs-layers',
                    },
                    styleManager: {
                        appendTo: '#gjs-styles',
                    },
                    traitManager: {
                        appendTo: '#gjs-traits',
                    },
                });

                // Load widgets from database after editor is ready
                editor.on('load', function() {
                    loadWidgets();
                });
                
                // Also try loading immediately (in case load event already fired)
                setTimeout(loadWidgets, 500);

                // Load initial content if editing
                if (initialHtml) {
                    try {
                        editor.setComponents(initialHtml);
                    } catch (e) {
                        console.error('Error loading initial HTML:', e);
                    }
                }
                
                if (initialJson) {
                    try {
                        const styleData = JSON.parse(initialJson);
                        editor.setStyle(styleData);
                    } catch (e) {
                        console.error('Error loading initial JSON:', e);
                    }
                }

                // Save on change
                editor.on('update', function() {
                    saveTemplateData();
                });

                // Update preview on change
                editor.on('update', function() {
                    updatePreview();
                });
            }

            // Load widgets from API
            function loadWidgets() {
                fetch(widgetsUrl)
                    .then(response => response.json())
                    .then(widgets => {
                        widgets.forEach(widget => {
                            editor.BlockManager.add(widget.key, {
                                label: widget.name,
                                category: widget.category,
                                content: widget.html,
                                attributes: {
                                    'data-widget-key': widget.key,
                                    'data-locked': widget.locked ? 'true' : 'false'
                                }
                            });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading widgets:', error);
                    });
            }

            // Save template data to hidden inputs
            function saveTemplateData() {
                const htmlContent = editor.getHtml();
                const jsonContent = JSON.stringify(editor.getStyle());

                const htmlInput = document.getElementById('html-content-input');
                const jsonInput = document.getElementById('json-content-input');
                const bodyInput = document.getElementById('body-input');

                if (htmlInput) htmlInput.value = htmlContent;
                if (jsonInput) jsonInput.value = jsonContent;
                if (bodyInput) bodyInput.value = htmlContent; // Keep body for backward compatibility
            }

            // Sample data for preview
            const sampleData = {
                'property_address': '123 Example Street',
                'property_postcode': 'SW1A 1AA',
                'asking_price': '450000',
                'buyer_name': 'John Doe',
                'buyer_email': 'john.doe@example.com',
                'offer_amount': '425000',
                'viewing_date': '2024-01-15',
                'viewing_time': '14:00',
                'recipient_name': 'Jane Smith',
                'user_name': 'Jane Smith',
                'user_email': 'jane.smith@example.com',
                'user_password': 'TempPass123',
                'seller_name': 'Jane Smith',
                'seller_email': 'jane.smith@example.com',
                'seller_phone': '+44 20 1234 5678',
                'status': 'sold',
                'message': 'Your property has been successfully sold.',
                'logo_url': '{{ asset("media/abodeology-logo.png") }}',
                'year': new Date().getFullYear(),
            };

            // Replace variables in template
            function replaceVariables(template, data) {
                if (!template) return '';
                
                return template.replace(/\{\{([^}]+)\}\}/g, function(match, key) {
                    key = key.trim();
                    return data[key] || match;
                });
            }

            // Update preview
            function updatePreview() {
                const activePane = document.querySelector('.builder-pane.active');
                if (!activePane || activePane.id !== 'preview-pane') return;

                const htmlContent = editor.getHtml();
                const subjectInput = document.getElementById('subject');
                const subject = subjectInput ? subjectInput.value : 'New Email Template';

                const renderedBody = replaceVariables(htmlContent, sampleData);
                const renderedSubject = replaceVariables(subject, sampleData);

                const previewSubject = document.getElementById('preview-subject');
                if (previewSubject) {
                    previewSubject.textContent = renderedSubject || 'New Email Template';
                }

                const previewFrame = document.getElementById('preview-frame');
                if (previewFrame) {
                    const deviceSelect = document.getElementById('preview-device');
                    const device = deviceSelect ? deviceSelect.value : 'desktop';
                    
                    previewFrame.className = 'preview-frame ' + device;
                    
                    let iframe = previewFrame.querySelector('iframe');
                    if (!iframe) {
                        iframe = document.createElement('iframe');
                        previewFrame.innerHTML = '';
                        previewFrame.appendChild(iframe);
                    }

                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    iframeDoc.open();
                    iframeDoc.write(renderedBody || '<p style="padding: 20px; color: #666;">Start building your email template...</p>');
                    iframeDoc.close();
                }
            }

            // Tab switching
            document.querySelectorAll('.builder-tab').forEach(function(tab) {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    
                    document.querySelectorAll('.builder-tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    document.querySelectorAll('.builder-pane').forEach(p => p.classList.remove('active'));
                    document.getElementById(tabName + '-pane').classList.add('active');
                    
                    if (tabName === 'preview') {
                        updatePreview();
                    }
                });
            });

            // Refresh preview button
            const refreshBtn = document.getElementById('refresh-preview');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function() {
                    updatePreview();
                });
            }

            // Device selector
            const deviceSelect = document.getElementById('preview-device');
            if (deviceSelect) {
                deviceSelect.addEventListener('change', function() {
                    updatePreview();
                });
            }

            // Watch subject input
            const subjectInput = document.getElementById('subject');
            if (subjectInput) {
                subjectInput.addEventListener('input', function() {
                    updatePreview();
                });
            }

            // Save before form submit
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function() {
                    saveTemplateData();
                });
            }

            // Initialize
            initGrapesJS();
        });
    </script>
@endpush

<div class="template-builder-container mb-3">
    <div class="template-builder-header">
        <div class="builder-tabs">
            <button class="builder-tab active" data-tab="builder">Builder</button>
            <button class="builder-tab" data-tab="preview">Live Preview</button>
        </div>
    </div>

    <div class="template-builder-content">
        <!-- Builder Tab -->
        <div class="builder-pane active" id="builder-pane">
            <div style="display: grid; grid-template-columns: 250px 1fr 250px; gap: 10px; height: 600px;">
                <!-- Blocks Panel -->
                <div id="gjs-blocks" style="background: #f8f9fa; border: 1px solid var(--line-grey); border-radius: 8px; padding: 15px; overflow-y: auto;">
                    <h4 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 600; color: var(--abodeology-teal);">Widgets</h4>
                    <p style="font-size: 12px; color: #666; margin-bottom: 10px;">Drag widgets into the canvas</p>
                </div>

                <!-- Canvas -->
                <div id="gjs-editor" style="border: 1px solid var(--line-grey); border-radius: 8px; overflow: hidden;"></div>

                <!-- Layers & Styles Panel -->
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div id="gjs-layers" style="background: #f8f9fa; border: 1px solid var(--line-grey); border-radius: 8px; padding: 15px; overflow-y: auto; flex: 1;">
                        <h4 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 600; color: var(--abodeology-teal);">Layers</h4>
                    </div>
                    <div id="gjs-styles" style="background: #f8f9fa; border: 1px solid var(--line-grey); border-radius: 8px; padding: 15px; overflow-y: auto; flex: 1;">
                        <h4 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 600; color: var(--abodeology-teal);">Styles</h4>
                    </div>
                </div>
            </div>

            <!-- Hidden inputs for form submission -->
            <input type="hidden" id="html-content-input" name="html_content" value="{{ old('html_content', isset($template) ? $template->html_content : '') }}">
            <input type="hidden" id="json-content-input" name="json_content" value="{{ old('json_content', isset($template) ? $template->json_content : '') }}">
            <input type="hidden" name="body" id="body-input" value="{{ old('body', isset($template) ? $template->body : '') }}">
        </div>

        <!-- Preview Tab -->
        <div class="builder-pane" id="preview-pane">
            <div class="preview-container">
                <div class="preview-header">
                    <div class="preview-subject-display">
                        <strong>Subject:</strong> <span id="preview-subject">New Email Template</span>
                    </div>
                    <div class="preview-controls">
                        <button class="preview-refresh-btn" id="refresh-preview" type="button">Refresh</button>
                        <select id="preview-device" class="preview-device-select">
                            <option value="desktop">Desktop</option>
                            <option value="mobile">Mobile</option>
                        </select>
                    </div>
                </div>
                <div class="preview-frame" id="preview-frame">
                    <div class="preview-loading">Loading preview...</div>
                </div>
            </div>
        </div>
    </div>
</div>
