@push('styles')
    {{-- Summernote CSS (free, CDN-hosted) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
    <style>
        .template-builder-page {
            padding: 0 !important;
            margin: 0 !important;
            height: calc(100vh - 70px);
            overflow: hidden;
            background: #f5f5f5;
            padding-top: 140px !important;
        }

        .template-builder-grid {
            display: grid;
            grid-template-columns: 320px 1fr 320px;
            gap: 0;
            height: 100%;
            align-items: stretch;
            background: #ffffff;
        }

        .editor-main-section,
        .variables-section,
        .widgets-section {
            display: flex;
            flex-direction: column;
            height: 100%;
            border-right: 1px solid #e0e0e0;
            overflow: hidden;
            min-height: 0;
        }

        .variables-section {
            border-right: none;
        }

        .editor-main-section .form-label,
        .variables-section .form-label,
        .widgets-section .form-label {
            font-weight: 600;
            font-size: 14px;
            color: #212529;
            margin: 0;
            padding: 15px 20px;
            background: #fafafa;
            border-bottom: 1px solid #e0e0e0;
            display: block;
        }

        @media (max-width: 1400px) {
            .template-builder-grid {
                grid-template-columns: 280px 1fr 280px;
            }
            
            .variables-section {
                min-width: 280px;
                max-width: 280px;
            }
        }

        @media (max-width: 1200px) {
            .template-builder-grid {
                grid-template-columns: 1fr;
                height: auto;
            }
            
            .editor-main-section,
            .variables-section,
            .widgets-section {
                height: 500px;
                border-right: none;
                border-bottom: 1px solid #e0e0e0;
            }
        }

        .variable-list, .widget-list {
            flex: 1 1 auto;
            overflow-y: auto;
            overflow-x: hidden;
            border: none;
            border-radius: 0;
            padding: 15px;
            background: #ffffff;
            font-size: 13px;
            min-height: 0;
            max-height: 100%;
            -webkit-overflow-scrolling: touch;
        }

        .widgets-section {
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .widgets-section > .form-label {
            flex-shrink: 0;
        }

        .widgets-section > .text-muted {
            flex-shrink: 0;
        }

        .widgets-section > .widget-list {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
        }

        .variables-section {
            min-width: 320px;
            max-width: 320px;
        }

        .variables-section .mb-2,
        .widgets-section .text-muted {
            padding: 0 15px;
            margin-bottom: 10px;
        }

        .variables-section .mb-2 {
            padding: 0 15px;
        }

        .widgets-section .text-muted {
            padding: 0 15px;
        }

        .variable-list code, .widget-list code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            color: #495057;
        }

        /* Editor Wrapper Styles */
        .editor-wrapper {
            position: relative;
            border: none;
            border-radius: 0;
            background: #ffffff;
            overflow: hidden;
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .editor-wrapper.drag-over {
            border: 2px dashed #007bff;
            background: #f0f8ff;
        }

        .editor-wrapper.drag-over::after {
            content: 'Drop widget here';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #007bff;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            z-index: 1000;
            pointer-events: none;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }

        /* Editor Tabs */
        .editor-tabs {
            display: flex;
            border-bottom: 1px solid #dee2e6;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .editor-tab {
            padding: 12px 20px;
            cursor: pointer;
            border: none;
            background: transparent;
            color: #6c757d;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .editor-tab:hover {
            color: #007bff;
            background: #ffffff;
        }

        .editor-tab.active {
            color: #007bff;
            background: #ffffff;
            border-bottom-color: #007bff;
        }

        .editor-tab-content {
            display: none;
            flex: 1;
            overflow: hidden;
        }

        .editor-tab-content.active {
            display: flex;
            flex-direction: column;
        }

        #tab-editor {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        #tab-editor textarea {
            flex: 1;
        }

        /* Summernote Customization */
        .note-editor {
            border: none !important;
            border-radius: 0 !important;
        }

        .note-toolbar {
            background: #ffffff !important;
            border-bottom: 1px solid #e9ecef !important;
            padding: 10px 15px !important;
        }

        .note-editable {
            min-height: 100% !important;
            padding: 20px !important;
            font-size: 14px !important;
            line-height: 1.6 !important;
            overflow-y: auto !important;
        }

        .note-editable:focus {
            outline: none !important;
        }

        /* Preview Pane */
        .preview-pane {
            padding: 20px;
            background: #ffffff;
            border: none;
            border-radius: 0;
            min-height: 400px;
            max-height: 500px;
            overflow-y: auto;
            flex: 1;
        }

        .preview-pane iframe {
            width: 100%;
            min-height: 400px;
            border: none;
            background: #ffffff;
        }

        /* Code View */
        .code-view {
            padding: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .code-view textarea {
            width: 100%;
            flex: 1;
            min-height: 400px;
            max-height: 500px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.5;
            border: none;
            background: #f8f9fa;
            resize: none;
            overflow-y: auto;
        }

        .code-view textarea:focus {
            outline: none;
            background: #ffffff;
        }

        /* Widget Item Styles */
        .widget-item {
            padding: 12px;
            margin-bottom: 10px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            background: #ffffff;
            cursor: grab;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .widget-item:active {
            cursor: grabbing;
        }

        .widget-item:hover {
            border-color: #007bff;
            background: #f8f9ff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
            transform: translateY(-2px);
        }

        .widget-item.dragging {
            opacity: 0.5;
            transform: scale(0.95);
        }

        .widget-item-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 8px;
        }

        .widget-item-name {
            font-weight: 600;
            font-size: 14px;
            color: #212529;
            margin: 0;
            flex: 1;
        }

        .widget-item-category {
            font-size: 10px;
            color: #6c757d;
            background: #e9ecef;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            margin-left: 8px;
        }

        .widget-item-description {
            font-size: 12px;
            color: #6c757d;
            margin: 6px 0 0 0;
            line-height: 1.4;
        }

        .widget-drag-handle {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #adb5bd;
            font-size: 14px;
            cursor: grab;
            line-height: 1;
            user-select: none;
            font-weight: normal;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .widget-drag-handle::before,
        .widget-drag-handle::after {
            content: '⋮';
            display: block;
            line-height: 1;
        }

        .widget-item:hover .widget-drag-handle {
            color: #007bff;
        }

        .widget-item {
            padding-right: 35px; /* Make room for drag handle */
        }

        .widget-item-actions {
            margin-top: 10px;
            display: flex;
            gap: 6px;
        }

        .btn-insert-widget {
            font-size: 11px;
            padding: 5px 12px;
            border-radius: 4px;
            font-weight: 500;
        }

        .widget-category-section {
            margin-bottom: 16px;
        }

        .widget-category-section:last-child {
            margin-bottom: 0;
        }

        .widget-category-title {
            font-size: 11px;
            font-weight: 700;
            color: #495057;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e9ecef;
        }

        /* Scrollbar styling */
        .widget-list::-webkit-scrollbar,
        .variable-list::-webkit-scrollbar {
            width: 8px;
        }

        .widget-list::-webkit-scrollbar-track,
        .variable-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .widget-list::-webkit-scrollbar-thumb,
        .variable-list::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }

        .widget-list::-webkit-scrollbar-thumb:hover,
        .variable-list::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        /* Empty state */
        .widget-empty-state {
            text-align: center;
            padding: 30px 20px;
            color: #6c757d;
        }

        .widget-empty-state-icon {
            font-size: 32px;
            margin-bottom: 10px;
            opacity: 0.5;
        }
    </style>
@endpush

@push('scripts')
    {{-- Summernote JS (free, CDN-hosted) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editor = $('#template-body-editor');
            const editorWrapper = document.querySelector('.editor-wrapper');
            let draggedWidget = null;

            if (editor.length) {
                editor.summernote({
                    placeholder: 'Start building your email template... Drag widgets here or type your content.',
                    tabsize: 2,
                    height: '100%',
                    minHeight: 400,
                    focus: true,
                    dialogsInBody: true,
                    dialogsFade: true,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                        ['fontname', ['fontname']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph', 'height']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video', 'hr']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    fontNames: ['Arial', 'Helvetica', 'Times New Roman', 'Courier New', 'Verdana', 'Georgia'],
                    fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '24', '36', '48'],
                    codeviewFilter: true,
                    codeviewIframeFilter: true,
                    callbacks: {
                        onInit: function() {
                            // Editor initialized - drag drop will be initialized separately
                            console.log('Summernote editor initialized');
                        }
                    }
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

                // Widget insertion functionality
                window.insertWidget = function(widgetHtml) {
                    if (!editor || !editor.length) {
                        console.error('Editor not initialized');
                        return;
                    }
                    
                    try {
                        const currentCode = editor.summernote('code') || '';
                        const hasContent = currentCode.trim().length > 0;
                        const spacing = hasContent ? '\n\n' : '';
                        
                        // Try to insert at cursor position
                        const $editable = editor.next('.note-editable');
                        if ($editable.length) {
                            editor.summernote('editor.insertHTML', spacing + widgetHtml);
                        } else {
                            // Fallback: append to end
                            editor.summernote('code', currentCode + spacing + widgetHtml);
                        }
                        
                        // Update preview and code view
                        setTimeout(function() {
                            updatePreview();
                            syncCodeView();
                        }, 100);
                    } catch (e) {
                        console.error('Error inserting widget:', e);
                        // Fallback: append to end
                        const currentCode = editor.summernote('code') || '';
                        const hasContent = currentCode.trim().length > 0;
                        const spacing = hasContent ? '\n\n' : '';
                        editor.summernote('code', currentCode + spacing + widgetHtml);
                        updatePreview();
                        syncCodeView();
                    }
                };

                // Tab switching functionality
                function switchTab(tabName) {
                    // Remove active class from all tabs and contents
                    document.querySelectorAll('.editor-tab').forEach(function(tab) {
                        tab.classList.remove('active');
                    });
                    document.querySelectorAll('.editor-tab-content').forEach(function(content) {
                        content.classList.remove('active');
                    });

                    // Add active class to selected tab and content
                    const activeTab = document.querySelector('[data-tab="' + tabName + '"]');
                    const activeContent = document.getElementById('tab-' + tabName);
                    
                    if (activeTab) activeTab.classList.add('active');
                    if (activeContent) activeContent.classList.add('active');

                    // Special handling for code view
                    if (tabName === 'code') {
                        const codeTextarea = document.getElementById('code-view-textarea');
                        if (codeTextarea) {
                            codeTextarea.value = editor.summernote('code');
                        }
                    }

                    // Update preview when switching to preview tab
                    if (tabName === 'preview') {
                        updatePreview();
                    }
                }

                // Update preview
                function updatePreview() {
                    const previewPane = document.getElementById('preview-content');
                    if (previewPane) {
                        const html = editor.summernote('code');
                        previewPane.innerHTML = html;
                    }
                }

                // Sync code view with editor
                function syncCodeView() {
                    const codeTextarea = document.getElementById('code-view-textarea');
                    if (codeTextarea) {
                        codeTextarea.value = editor.summernote('code');
                    }
                }

                // Update editor from code view
                function updateEditorFromCode() {
                    const codeTextarea = document.getElementById('code-view-textarea');
                    if (codeTextarea) {
                        editor.summernote('code', codeTextarea.value);
                        updatePreview();
                    }
                }

                // Listen for editor changes
                editor.on('summernote.change', function() {
                    syncCodeView();
                    // Auto-update preview if preview tab is active
                    const previewTab = document.querySelector('[data-tab="preview"]');
                    if (previewTab && previewTab.classList.contains('active')) {
                        updatePreview();
                    }
                });

                // Setup tab click handlers
                document.querySelectorAll('.editor-tab').forEach(function(tab) {
                    tab.addEventListener('click', function() {
                        const tabName = this.getAttribute('data-tab');
                        switchTab(tabName);
                    });
                });

                // Setup code view sync
                const codeTextarea = document.getElementById('code-view-textarea');
                if (codeTextarea) {
                    codeTextarea.addEventListener('blur', updateEditorFromCode);
                    codeTextarea.addEventListener('keyup', function(e) {
                        // Auto-sync on Ctrl+S or Cmd+S
                        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                            e.preventDefault();
                            updateEditorFromCode();
                        }
                    });
                }

                // Expose functions globally
                window.updatePreview = updatePreview;
                window.switchTab = switchTab;

                // Drag and Drop Functionality
                function initializeDragDrop() {
                    console.log('Initializing drag and drop...');
                    
                    // Make widgets draggable
                    function makeWidgetsDraggable() {
                        const widgetItems = document.querySelectorAll('.widget-item');
                        console.log('Found widgets:', widgetItems.length);
                        
                        widgetItems.forEach(function(widget) {
                            // Skip if already has draggable attribute
                            if (widget.getAttribute('draggable') === 'true') {
                                return;
                            }
                            
                            widget.setAttribute('draggable', 'true');
                            
                            widget.addEventListener('dragstart', function(e) {
                                console.log('Drag started');
                                draggedWidget = this;
                                this.classList.add('dragging');
                                e.dataTransfer.effectAllowed = 'move';
                                const widgetHtml = this.dataset.widgetHtml || '';
                                console.log('Widget HTML length:', widgetHtml.length);
                                
                                try {
                                    e.dataTransfer.setData('text/html', widgetHtml);
                                    e.dataTransfer.setData('text/plain', widgetHtml);
                                } catch(err) {
                                    console.error('Error setting drag data:', err);
                                }
                            });

                            widget.addEventListener('dragend', function(e) {
                                console.log('Drag ended');
                                this.classList.remove('dragging');
                                draggedWidget = null;
                                document.querySelectorAll('.drag-over').forEach(function(el) {
                                    el.classList.remove('drag-over');
                                });
                            });
                        });
                    }

                    // Function to setup drop zone for editor
                    function setupEditorDropZone(element) {
                        if (!element) return;

                        element.addEventListener('dragover', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            e.dataTransfer.dropEffect = 'move';
                            if (editorWrapper) {
                                editorWrapper.classList.add('drag-over');
                            }
                        }, false);

                        element.addEventListener('dragleave', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            if (editorWrapper) {
                                const rect = editorWrapper.getBoundingClientRect();
                                const x = e.clientX;
                                const y = e.clientY;
                                if (x < rect.left || x > rect.right || y < rect.top || y > rect.bottom) {
                                    editorWrapper.classList.remove('drag-over');
                                }
                            }
                        }, false);

                        element.addEventListener('drop', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            console.log('Drop event triggered');
                            
                            if (editorWrapper) {
                                editorWrapper.classList.remove('drag-over');
                            }

                            let widgetHtml = '';
                            if (draggedWidget && draggedWidget.dataset.widgetHtml) {
                                widgetHtml = draggedWidget.dataset.widgetHtml;
                                console.log('Got widget HTML from draggedWidget, length:', widgetHtml.length);
                            } else {
                                widgetHtml = e.dataTransfer.getData('text/html') || 
                                            e.dataTransfer.getData('text/plain');
                                console.log('Got widget HTML from dataTransfer, length:', widgetHtml.length);
                            }

                            if (widgetHtml) {
                                console.log('Inserting widget...');
                                window.insertWidget(widgetHtml);
                            } else {
                                console.error('No widget HTML found');
                            }
                        }, false);
                    }

                    // Initialize widgets as draggable
                    makeWidgetsDraggable();

                    // Setup drop zones - wait for Summernote to initialize
                    setTimeout(function() {
                        if (!editorWrapper) {
                            console.error('Editor wrapper not found');
                            return;
                        }
                        
                        const summernoteEditor = editorWrapper.querySelector('.note-editor');
                        const summernoteEditable = editorWrapper.querySelector('.note-editable');
                        const textarea = document.getElementById('template-body-editor');
                        
                        console.log('Setting up drop zones...');
                        console.log('Summernote editor:', !!summernoteEditor);
                        console.log('Summernote editable:', !!summernoteEditable);
                        console.log('Textarea:', !!textarea);
                        
                        // Setup drop on the editor wrapper
                        setupEditorDropZone(editorWrapper);
                        
                        // Setup drop on Summernote editor elements
                        if (summernoteEditor) {
                            setupEditorDropZone(summernoteEditor);
                        }
                        if (summernoteEditable) {
                            setupEditorDropZone(summernoteEditable);
                        }
                        
                        // Also handle drop on the textarea itself
                        if (textarea) {
                            setupEditorDropZone(textarea);
                        }
                        
                        console.log('Drop zones set up');
                    }, 1200);
                }

                // Initialize drag and drop after editor is ready
                setTimeout(function() {
                    initializeDragDrop();
                }, 1500);

                // Initialize preview with existing content
                setTimeout(function() {
                    const initialContent = editor.summernote('code');
                    if (initialContent) {
                        syncCodeView();
                        updatePreview();
                    }
                }, 1000);
            }
        });
    </script>
@endpush

<div class="template-builder-grid mb-3">
    <div class="widgets-section">
        <label class="form-label">Email Widgets</label>
        <p class="text-muted" style="font-size: 12px; margin-bottom: 10px;">
            <strong>Drag and drop</strong> widgets into the editor, or click the button to insert. Widgets are pre-designed components you can use.
        </p>
        <div class="widget-list">
            @if(isset($widgets) && $widgets->count() > 0)
                @foreach($widgets as $category => $categoryWidgets)
                    <div class="widget-category-section">
                        <div class="widget-category-title">{{ $category }}</div>
                        @foreach($categoryWidgets as $widget)
                            <div 
                                class="widget-item" 
                                data-widget-html="{{ addslashes($widget->html) }}"
                                data-widget-name="{{ addslashes($widget->name) }}"
                            >
                                <div class="widget-drag-handle" title="Drag to editor"></div>
                                <div class="widget-item-header">
                                    <span class="widget-item-name">{{ $widget->name }}</span>
                                    <span class="widget-item-category">{{ $widget->category }}</span>
                                </div>
                                @if($widget->description)
                                    <p class="widget-item-description">{{ $widget->description }}</p>
                                @endif
                                <div class="widget-item-actions">
                                    <button 
                                        type="button" 
                                        class="btn btn-primary btn-sm btn-insert-widget"
                                        onclick="insertWidget(`{{ addslashes($widget->html) }}`)"
                                    >
                                        Insert Widget
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <div class="widget-empty-state">
                    <div class="widget-empty-state-icon">📦</div>
                    <p class="text-muted mb-0" style="font-size: 12px;">
                        No widgets available.<br>Please run the seeder to populate widgets.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <div class="editor-main-section">
        <label for="template-body-editor" class="form-label">Email Template Builder</label>
        <div class="editor-wrapper">
            <ul class="editor-tabs">
                <li>
                    <button type="button" class="editor-tab active" data-tab="editor">
                        ✏️ Editor
                    </button>
                </li>
                <li>
                    <button type="button" class="editor-tab" data-tab="preview">
                        👁️ Preview
                    </button>
                </li>
                <li>
                    <button type="button" class="editor-tab" data-tab="code">
                        💻 Code
                    </button>
                </li>
            </ul>

            <div id="tab-editor" class="editor-tab-content active" style="flex: 1; display: flex; flex-direction: column;">
                <textarea
                    id="template-body-editor"
                    name="body"
                    class="form-control @error('body') is-invalid @enderror"
                    style="flex: 1; min-height: 400px;"
                >{{ old('body', $template->body ?? '') }}</textarea>
                @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div id="tab-preview" class="editor-tab-content" style="flex: 1; display: flex; flex-direction: column;">
                <div class="preview-pane" style="flex: 1;">
                    <div id="preview-content"></div>
                </div>
            </div>

            <div id="tab-code" class="editor-tab-content code-view" style="flex: 1; display: flex; flex-direction: column;">
                <textarea
                    id="code-view-textarea"
                    placeholder="HTML code will appear here..."
                    spellcheck="false"
                    style="flex: 1;"
                ></textarea>
            </div>
        </div>
    </div>

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
</div>
