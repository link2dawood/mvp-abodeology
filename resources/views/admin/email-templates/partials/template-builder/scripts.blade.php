@push('scripts')
    <script>
        console.log('🚀 Template builder script loading...');
        document.addEventListener('DOMContentLoaded', function () {
            console.log('✅ DOM Content Loaded');
            const hiddenTextarea = document.getElementById('template-body-editor');
            const visualCanvas = document.getElementById('visual-email-canvas');
            const canvasEmptyState = document.getElementById('canvas-empty-state');
            let draggedWidget = null;
            let selectedWidget = null;
            
            // Undo/Redo History Management
            const historyStack = {
                past: [],
                present: hiddenTextarea ? hiddenTextarea.value : '',
                future: [],
                maxSize: 50
            };
            
            const undoButton = document.getElementById('undo-button');
            const redoButton = document.getElementById('redo-button');
            
            // Save state to history
            function saveToHistory() {
                const currentValue = hiddenTextarea.value;
                if (currentValue === historyStack.present) {
                    return; // No change, don't save
                }
                
                historyStack.past.push(historyStack.present);
                historyStack.present = currentValue;
                historyStack.future = []; // Clear redo stack when new action is performed
                
                // Limit history size
                if (historyStack.past.length > historyStack.maxSize) {
                    historyStack.past.shift();
                }
                
                updateUndoRedoButtons();
            }
            
            // Update undo/redo button states
            function updateUndoRedoButtons() {
                if (undoButton) {
                    undoButton.disabled = historyStack.past.length === 0;
                }
                if (redoButton) {
                    redoButton.disabled = historyStack.future.length === 0;
                }
            }
            
            // Undo function
            function performUndo() {
                if (historyStack.past.length === 0) {
                    return;
                }
                
                historyStack.future.unshift(historyStack.present);
                historyStack.present = historyStack.past.pop();
                
                hiddenTextarea.value = historyStack.present;
                renderVisualCanvas(historyStack.present);
                updateUndoRedoButtons();
                
                console.log('✅ Undo performed');
            }
            
            // Redo function
            function performRedo() {
                if (historyStack.future.length === 0) {
                    return;
                }
                
                historyStack.past.push(historyStack.present);
                historyStack.present = historyStack.future.shift();
                
                hiddenTextarea.value = historyStack.present;
                renderVisualCanvas(historyStack.present);
                updateUndoRedoButtons();
                
                console.log('✅ Redo performed');
            }
            
            // Initialize history with current value
            if (hiddenTextarea && hiddenTextarea.value) {
                historyStack.present = hiddenTextarea.value;
            }
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl+Z or Cmd+Z for undo
                if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
                    e.preventDefault();
                    performUndo();
                }
                // Ctrl+Y or Ctrl+Shift+Z for redo
                if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.key === 'z' && e.shiftKey))) {
                    e.preventDefault();
                    performRedo();
                }
            });
            
            // Undo/Redo button handlers
            if (undoButton) {
                undoButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    performUndo();
                });
            }
            
            if (redoButton) {
                redoButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    performRedo();
                });
            }
            
            // Expose globally
            window.performUndo = performUndo;
            window.performRedo = performRedo;
            
            console.log('📋 Elements found:', {
                hiddenTextarea: !!hiddenTextarea,
                visualCanvas: !!visualCanvas,
                canvasEmptyState: !!canvasEmptyState
            });
            
            // Variable friendly names mapping
            // Build logo key separately to avoid Blade parsing it as a PHP constant
            const logoKeyName = 'logo' + '_' + 'url';
            const variableFriendlyNames = {
                'property.address': 'Property Address',
                'property.postcode': 'Property Postcode',
                'property.asking_price': 'Asking Price',
                'buyer.name': 'Buyer Name',
                'buyer.email': 'Buyer Email',
                'offer.offer_amount': 'Offer Amount',
                'viewing.viewing_date': 'Viewing Date',
                'viewing.viewing_time': 'Viewing Time',
                'viewing.status': 'Viewing Status',
                'recipient.name': 'Recipient Name',
                'user.email': 'User Email',
                'password': 'Password',
                'url': 'Link URL',
                'text': 'Text',
                'message': 'Message',
                'title': 'Title',
                'year': 'Year',
                'item1': 'Item 1',
                'item2': 'Item 2',
                'item3': 'Item 3',
                'content': 'Content'
            };
            // Add logo key dynamically to avoid Blade constant parsing
            variableFriendlyNames[logoKeyName] = 'Logo URL';

            // Store original HTML for each widget block
            const widgetOriginalHtml = new Map();

            // Render HTML to visual canvas
            function renderVisualCanvas(html) {
                if (!html || html.trim() === '') {
                    canvasEmptyState.style.display = 'flex';
                    visualCanvas.innerHTML = '';
                    visualCanvas.appendChild(canvasEmptyState);
                    return;
                }

                canvasEmptyState.style.display = 'none';
                
                // Decode any HTML entities that might be in the stored HTML
                // Only decode if HTML entities are present (avoid double-decoding)
                let decodedHtml = html;
                if (html.includes('&lt;') || html.includes('&gt;') || html.includes('&amp;')) {
                    decodedHtml = html
                        .replace(/&lt;/g, '<')
                        .replace(/&gt;/g, '>')
                        .replace(/&amp;/g, '&')
                        .replace(/&quot;/g, '"')
                        .replace(/&#039;/g, "'")
                        .replace(/&#39;/g, "'");
                }
                
                // Get actual logo URL for preview from settings
                // Use global logoUrl variable that can be updated via AJAX
                let actualLogoUrl = window.templateBuilderLogoUrl || {!! json_encode($logoUrl ?? asset('media/abodeology-logo.png')) !!} || '/media/abodeology-logo.png';
                
                // Store initial logo URL globally
                if (!window.templateBuilderLogoUrl) {
                    window.templateBuilderLogoUrl = actualLogoUrl;
                }
                
                // Function to update logo URL dynamically
                window.updateLogoUrl = function(newLogoUrl) {
                    window.templateBuilderLogoUrl = newLogoUrl;
                    actualLogoUrl = newLogoUrl;
                };
                
                // First, replace logo variable in img src attributes with actual logo URL for visual preview
                let visualHtml = decodedHtml;
                const openBrace = '{';
                const closeBrace = '}';
                // Break up logo variable string to prevent Blade from interpreting it as a PHP constant
                const logoText = 'logo' + '_' + 'url';
                const logoUrlVar = openBrace + openBrace + logoText + closeBrace + closeBrace;
                
                // Replace logo variable in img src with actual logo URL
                // Build regex pattern dynamically to avoid Blade parsing
                const logoUrlPattern1 = new RegExp('<img([^>]*)\\ssrc=["\']' + openBrace + openBrace + logoText + closeBrace + closeBrace + '["\']([^>]*)>', 'gi');
                visualHtml = visualHtml.replace(logoUrlPattern1, function(match, before, after) {
                    return '<img' + before + ' src="' + actualLogoUrl + '"' + after + '>';
                });
                
                // Also handle src with logo variable format
                const logoUrlPattern2 = new RegExp('src=["\']' + openBrace + openBrace + logoText + closeBrace + closeBrace + '["\']', 'gi');
                visualHtml = visualHtml.replace(logoUrlPattern2, 'src="' + actualLogoUrl + '"');
                
                // Replace other variables with friendly placeholders
                const variablePattern = /\{\{([^}]+)\}\}/g;
                visualHtml = visualHtml.replace(variablePattern, function(match, variable) {
                    variable = variable.trim();
                    // Skip logo variable as we already handled it (use logoText to avoid Blade constant parsing)
                    const logoVarName = 'logo' + '_' + 'url';
                    if (variable === logoVarName) {
                        return match; // Keep original if not in img src
                    }
                    const friendlyName = variableFriendlyNames[variable] || variable.replace(/\./g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    return '<span class="variable-placeholder" title="Variable: {{' + variable + '}}">' + friendlyName + '</span>';
                });

                // Wrap in email preview container
                const previewContainer = document.createElement('div');
                previewContainer.className = 'canvas-email-preview';
                
                // Use a temporary div to parse and clean the HTML
                // This ensures HTML is properly parsed and rendered, not displayed as text
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = visualHtml.trim();
                
                // Move all children to preview container (this ensures proper HTML rendering)
                // This method ensures the browser parses the HTML correctly
                while (tempDiv.firstChild) {
                    previewContainer.appendChild(tempDiv.firstChild);
                }
                
                // If no children were moved (might be text nodes or whitespace), try direct innerHTML
                if (previewContainer.children.length === 0 && visualHtml.trim()) {
                    previewContainer.innerHTML = visualHtml.trim();
                }

                // Make each top-level element clickable and editable
                // Use children property for direct children (more compatible than :scope > *)
                const topLevelElements = Array.from(previewContainer.children);
                
                // If no direct children, check for text nodes or try to parse differently
                if (topLevelElements.length === 0 && visualHtml.trim()) {
                    // Try parsing as HTML fragments
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(visualHtml, 'text/html');
                    const bodyChildren = Array.from(doc.body.children);
                    
                    bodyChildren.forEach(function(child) {
                        previewContainer.appendChild(child.cloneNode(true));
                    });
                }
                
                const finalElements = Array.from(previewContainer.children);
                finalElements.forEach(function(element, index) {
                    // Store original HTML before any modifications
                    const originalHtml = element.outerHTML;
                    widgetOriginalHtml.set(element, originalHtml);
                    
                    element.classList.add('canvas-widget-block');
                    element.setAttribute('data-widget-index', index);
                    
                    // Ensure images render properly
                    const images = element.querySelectorAll('img');
                    images.forEach(function(img) {
                        const imgSrc = img.getAttribute('src') || '';
                        const openBrace = '{';
                        const closeBrace = '}';
                        // Break up logo variable string to prevent Blade from interpreting it as a PHP constant
                        const logoVarName = 'logo' + '_' + 'url';
                        const logoUrlVar = openBrace + openBrace + logoVarName + closeBrace + closeBrace;
                        
                        // If image has no src or has variable placeholder, ensure it displays properly
                        if (!img.src || img.src === '' || imgSrc === logoUrlVar || img.src.includes(openBrace + openBrace)) {
                            // If it's logo variable, use actual logo from settings
                            if (imgSrc === logoUrlVar || img.src.includes(logoVarName)) {
                                const logoUrlToUse = window.templateBuilderLogoUrl || actualLogoUrl;
                                img.src = logoUrlToUse;
                            }
                            
                            img.style.display = 'block';
                            img.style.width = img.getAttribute('width') || '160px';
                            img.style.height = img.getAttribute('height') || 'auto';
                            img.style.objectFit = img.getAttribute('style')?.includes('object-fit') ? '' : 'contain';
                            img.alt = img.alt || 'Abodeology Logo';
                            
                            // Add error handler to show fallback text if image fails to load
                            img.onerror = function() {
                                this.style.display = 'none';
                                const fallback = this.nextElementSibling;
                                if (fallback && fallback.tagName === 'H1') {
                                    fallback.style.display = 'block';
                                }
                            };
                        }
                    });
                    
                    makeTextEditable(element);
                    
                    element.addEventListener('click', function(e) {
                        // Allow editing if clicking on editable content
                        if (e.target.contentEditable === 'true' || e.target.closest('[contenteditable="true"]')) {
                            // Focus the editable element
                            const editableEl = e.target.contentEditable === 'true' ? e.target : e.target.closest('[contenteditable="true"]');
                            if (editableEl) {
                                editableEl.focus();
                                // Place cursor at end of text
                                const range = document.createRange();
                                range.selectNodeContents(editableEl);
                                range.collapse(false);
                                const selection = window.getSelection();
                                selection.removeAllRanges();
                                selection.addRange(range);
                            }
                            return;
                        }
                        e.stopPropagation();
                        selectWidget(element, html);
                    });
                });
                
                setTimeout(syncCanvasToTextarea, 100);

                visualCanvas.innerHTML = '';
                visualCanvas.appendChild(previewContainer);
            }

            // Select widget for editing
            function selectWidget(element, originalHtml) {
                if (selectedWidget) {
                    selectedWidget.classList.remove('selected');
                }
                
                selectedWidget = element;
                element.classList.add('selected');
                showPropertiesPanel(element, originalHtml);
            }

            // Show properties panel
            function showPropertiesPanel(element, originalHtml) {
                const panel = document.getElementById('widget-properties-panel');
                const panelBody = document.getElementById('properties-panel-body');
                
                panelBody.innerHTML = `
                    <div class="property-field">
                        <label>Widget Content</label>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 15px; font-size: 12px; color: #666;">
                            This widget is rendered visually. To edit the HTML structure, use the widgets sidebar or block buttons.
                        </div>
                    </div>
                    <div class="property-field">
                        <label>Actions</label>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeSelectedWidget()" style="width: 100%;">
                            Remove Widget
                        </button>
                    </div>
                `;
                
                panel.style.display = 'flex';
            }

            // Close properties panel
            window.closePropertiesPanel = function() {
                const panel = document.getElementById('widget-properties-panel');
                panel.style.display = 'none';
                if (selectedWidget) {
                    selectedWidget.classList.remove('selected');
                    selectedWidget = null;
                }
            };

            // Remove selected widget with confirmation
            window.removeSelectedWidget = function() {
                if (!selectedWidget) {
                    return;
                }
                
                // Confirmation dialog
                if (!confirm('Are you sure you want to remove this widget? You can undo this action using Ctrl+Z.')) {
                    return;
                }
                
                // Save state before removal
                saveToHistory();
                
                selectedWidget.remove();
                closePropertiesPanel();
                syncCanvasToTextarea();
                
                // Save state after removal
                setTimeout(function() {
                    saveToHistory();
                    updateUndoRedoButtons();
                }, 100);
                
                console.log('✅ Widget removed');
            };

            // Make text content editable inline
            function makeTextEditable(element) {
                // Select all text-containing elements, excluding variable placeholders
                const textElements = element.querySelectorAll('p, h1, h2, h3, h4, h5, h6, span:not(.variable-placeholder), div:not(.variable-placeholder):not(.canvas-widget-block), li, td, th, a, strong, em, b, i, u');
                
                textElements.forEach(function(textEl) {
                    // Skip if it's a variable placeholder or already has contenteditable set
                    if (textEl.classList.contains('variable-placeholder') || 
                        textEl.closest('.variable-placeholder') ||
                        textEl.hasAttribute('contenteditable')) {
                        return;
                    }
                    
                    // Make element editable
                    textEl.setAttribute('contenteditable', 'true');
                    textEl.style.cursor = 'text';
                    
                    // Prevent event bubbling to parent click handler when editing
                    textEl.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                    
                    // Save changes on blur
                    textEl.addEventListener('blur', function() {
                        widgetOriginalHtml.set(element, element.outerHTML);
                        syncCanvasToTextarea(true); // Save to history on blur
                    });
                    
                    // Handle input events
                    textEl.addEventListener('input', function(e) {
                        // Ensure variable placeholders remain non-editable
                        const placeholders = textEl.querySelectorAll('.variable-placeholder');
                        placeholders.forEach(function(ph) {
                            ph.setAttribute('contenteditable', 'false');
                            ph.style.cursor = 'help';
                        });
                        
                        // Debounce sync to avoid too many updates
                        clearTimeout(textEl._syncTimeout);
                        textEl._syncTimeout = setTimeout(function() {
                            syncCanvasToTextarea(false);
                        }, 300);
                    });
                    
                    // Handle paste events to clean HTML
                    textEl.addEventListener('paste', function(e) {
                        e.preventDefault();
                        const text = (e.clipboardData || window.clipboardData).getData('text/plain');
                        document.execCommand('insertText', false, text);
                    });
                });
                
                // Also make the element itself editable if it contains direct text nodes
                if (element.childNodes.length > 0) {
                    let hasDirectText = false;
                    element.childNodes.forEach(function(node) {
                        if (node.nodeType === Node.TEXT_NODE && node.textContent.trim()) {
                            hasDirectText = true;
                        }
                    });
                    
                    // If element has direct text content, wrap it or make editable
                    if (hasDirectText && !element.querySelector('p, h1, h2, h3, h4, h5, h6, div, span')) {
                        // Wrap text nodes in a span to make them editable
                        const textNodes = [];
                        element.childNodes.forEach(function(node) {
                            if (node.nodeType === Node.TEXT_NODE && node.textContent.trim()) {
                                textNodes.push(node);
                            }
                        });
                        
                        textNodes.forEach(function(textNode) {
                            const span = document.createElement('span');
                            span.setAttribute('contenteditable', 'true');
                            span.style.cursor = 'text';
                            textNode.parentNode.insertBefore(span, textNode);
                            span.appendChild(textNode);
                            
                            span.addEventListener('blur', function() {
                                widgetOriginalHtml.set(element, element.outerHTML);
                                syncCanvasToTextarea(true);
                            });
                        });
                    }
                }
            }

            // Sync visual canvas to hidden textarea
            let isSyncing = false; // Prevent infinite loops
            function syncCanvasToTextarea(saveHistory = false) {
                if (isSyncing) return;
                isSyncing = true;
                
                const preview = visualCanvas.querySelector('.canvas-email-preview');
                if (preview) {
                    const blocks = preview.querySelectorAll('.canvas-widget-block');
                    let html = '';
                    
                    // Get actual logo URL from settings to restore variable
                    const actualLogoUrl = window.templateBuilderLogoUrl || {!! json_encode($logoUrl ?? asset('media/abodeology-logo.png')) !!} || '/media/abodeology-logo.png';
                    
                    blocks.forEach(function(block, index) {
                        let blockHtml = block.outerHTML;
                        
                        // Restore logo variable in img src attributes
                        const logoUrlRegex = new RegExp(actualLogoUrl.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
                        // Break up logo variable string to prevent Blade from interpreting it as a PHP constant
                        const logoVarName = 'logo' + '_' + 'url';
                        const logoUrlPlaceholder = '{{' + logoVarName + '}}';
                        blockHtml = blockHtml.replace(/<img([^>]*)\ssrc=["']([^"']*logo[^"']*)["']([^>]*)>/gi, function(match, before, src, after) {
                            if (src === actualLogoUrl || src.includes('abodeology-logo')) {
                                return '<img' + before + ' src="' + logoUrlPlaceholder + '"' + after + '>';
                            }
                            return match;
                        });
                        
                        blockHtml = blockHtml.replace(/<span class="variable-placeholder"[^>]*>([^<]+)<\/span>/g, function(match, friendlyName) {
                            for (let varName in variableFriendlyNames) {
                                if (variableFriendlyNames[varName] === friendlyName) {
                                    return '{{' + varName + '}}';
                                }
                            }
                            const varName = friendlyName.toLowerCase().replace(/\s+/g, '.');
                            return '{{' + varName + '}}';
                        });
                        
                        blockHtml = blockHtml.replace(/ class="canvas-widget-block[^"]*"/g, '');
                        blockHtml = blockHtml.replace(/ data-widget-index="[^"]*"/g, '');
                        blockHtml = blockHtml.replace(/ contenteditable="true"/g, '');
                        blockHtml = blockHtml.replace(/ contenteditable="false"/g, '');
                        
                        html += blockHtml + '\n';
                    });
                    
                    hiddenTextarea.value = html.trim();
                    
                    // Save to history if requested (for text edits)
                    if (saveHistory && html.trim() !== historyStack.present) {
                        setTimeout(function() {
                            saveToHistory();
                            updateUndoRedoButtons();
                        }, 300); // Debounce text edits
                    }
                } else {
                    hiddenTextarea.value = '';
                }
                
                isSyncing = false;
            }

            // Initialize canvas with existing content
            if (hiddenTextarea && hiddenTextarea.value) {
                historyStack.present = hiddenTextarea.value;
                renderVisualCanvas(hiddenTextarea.value);
                updateUndoRedoButtons();
            }

            // Watch for changes in hidden textarea
            const observer = new MutationObserver(function() {
                if (hiddenTextarea.value && visualCanvas.querySelector('.canvas-email-preview') === null) {
                    renderVisualCanvas(hiddenTextarea.value);
                }
            });
            
            if (hiddenTextarea) {
                observer.observe(hiddenTextarea, { attributes: true, childList: true, characterData: true });
                hiddenTextarea.addEventListener('input', function() {
                    renderVisualCanvas(this.value);
                });
            }

            // Preview template function
            window.previewTemplate = function() {
                console.log('🔍 previewTemplate called');
                try {
                    syncCanvasToTextarea();
                    
                    const html = hiddenTextarea.value;
                    if (!html || html.trim() === '') {
                        alert('Please add some content to preview.');
                        return;
                    }

                    let previewHtml = html;
                    const variablePattern = /\{\{([^}]+)\}\}/g;
                    const logoUrl = window.templateBuilderLogoUrl || {!! json_encode($logoUrl ?? asset('media/abodeology-logo.png')) !!} || '/media/abodeology-logo.png';
                    // Build logo key separately to avoid Blade parsing
                    const logoKey = 'logo' + '_' + 'url';
                    const sampleData = {
                        'property.address': '123 Example Street',
                        'property.postcode': 'SW1A 1AA',
                        'property.asking_price': '£450,000',
                        'buyer.name': 'John Doe',
                        'buyer.email': 'john.doe@example.com',
                        'offer.offer_amount': '£425,000',
                        'viewing.viewing_date': '15th February 2024',
                        'viewing.viewing_time': '2:00 PM',
                        'viewing.status': 'Confirmed',
                        'recipient.name': 'Jane Smith',
                        'user.email': 'user@example.com',
                        'password': 'TempPass123!',
                        'url': '#',
                        'text': 'Sample Text',
                        'message': 'This is a sample message',
                        'title': 'Sample Title',
                        'year': new Date().getFullYear(),
                    };
                    // Add logo key dynamically
                    sampleData[logoKey] = logoUrl;

                    previewHtml = previewHtml.replace(variablePattern, function(match, variable) {
                        variable = variable.trim();
                        return sampleData[variable] || '[Sample ' + variable + ']';
                    });

                    const escapedHtml = previewHtml
                        .replace(/\\/g, '\\\\')
                        .replace(/`/g, '\\`')
                        .replace(/\${/g, '\\${');

                    const modal = document.createElement('div');
                    modal.className = 'widget-preview-modal show';
                    modal.innerHTML = 
                        '<div class="widget-preview-modal-content">' +
                            '<div class="widget-preview-modal-header">' +
                                '<h5>Email Preview</h5>' +
                                '<button type="button" class="widget-preview-close" onclick="this.closest(\'.widget-preview-modal\').remove()">&times;</button>' +
                            '</div>' +
                            '<div class="widget-preview-modal-body">' +
                                '<div class="widget-preview-note">' +
                                    '<strong>Note:</strong> This is a preview with sample data. Variables are replaced with example values.' +
                                '</div>' +
                                '<div style="max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">' +
                                    escapedHtml +
                                '</div>' +
                            '</div>' +
                        '</div>';
                    document.body.appendChild(modal);
                    console.log('✅ Preview modal created');
                } catch (error) {
                    console.error('❌ Error in previewTemplate:', error);
                    alert('Error creating preview: ' + error.message);
                }
            };

            // Block insertion function
            function insertBlock(html) {
                try {
                    // Save state before insertion
                    saveToHistory();
                    
                    const currentHtml = hiddenTextarea.value || '';
                    const hasContent = currentHtml.trim().length > 0;
                    const spacing = hasContent ? '\n' : '';
                    
                    const newHtml = currentHtml + spacing + html;
                    hiddenTextarea.value = newHtml;
                    renderVisualCanvas(newHtml);
                    
                    // Save state after insertion
                    setTimeout(function() {
                        saveToHistory();
                        updateUndoRedoButtons();
                    }, 100);
                    
                    console.log('Block inserted.');
                } catch (e) {
                    console.error('Error inserting block:', e);
                }
            }

            window.insertBlock = insertBlock;

            // Manual variable insertion from input field
            const insertButton = document.getElementById('insert-variable-button');
            const input = document.getElementById('insert-variable-input');

            if (insertButton && input) {
                insertButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    const variable = (input.value || '').trim();
                    if (!variable) return;
                    
                    const currentHtml = hiddenTextarea.value || '';
                    const newHtml = currentHtml + '{{' + variable + '}}';
                    hiddenTextarea.value = newHtml;
                    renderVisualCanvas(newHtml);
                    input.value = '';
                });
            }

            // Widget insertion functionality
            window.insertWidget = function(widgetHtml) {
                console.log('insertWidget called with HTML length:', widgetHtml ? widgetHtml.length : 0);
                
                if (!widgetHtml || widgetHtml.trim() === '') {
                    console.error('No widget HTML provided');
                    return;
                }
                
                try {
                    // Save state before insertion
                    saveToHistory();
                    
                    const decodedHtml = widgetHtml
                        .replace(/&lt;/g, '<')
                        .replace(/&gt;/g, '>')
                        .replace(/&amp;/g, '&')
                        .replace(/&quot;/g, '"')
                        .replace(/&#039;/g, "'");
                    
                    const currentHtml = hiddenTextarea.value || '';
                    const hasContent = currentHtml.trim().length > 0;
                    const spacing = hasContent ? '\n\n' : '';
                    
                    const newHtml = currentHtml + spacing + decodedHtml;
                    hiddenTextarea.value = newHtml;
                    renderVisualCanvas(newHtml);
                    
                    // Save state after insertion
                    setTimeout(function() {
                        saveToHistory();
                        updateUndoRedoButtons();
                    }, 100);
                    
                    console.log('Widget inserted successfully');
                } catch (e) {
                    console.error('Error inserting widget:', e);
                    alert('Failed to insert widget. Please try again.');
                }
            };

            // Block Templates
            if (typeof window.blockTemplates === 'undefined') {
                window.blockTemplates = {
                    heading: [
                        { name: 'Heading 1', html: '<h1 style="font-size: 32px; font-weight: bold; margin: 20px 0; color: #333;">Your Heading Text</h1>' },
                        { name: 'Heading 2', html: '<h2 style="font-size: 24px; font-weight: bold; margin: 18px 0; color: #333;">Your Heading Text</h2>' },
                        { name: 'Heading 3', html: '<h3 style="font-size: 20px; font-weight: bold; margin: 16px 0; color: #333;">Your Heading Text</h3>' },
                        { name: 'Heading 4', html: '<h4 style="font-size: 18px; font-weight: bold; margin: 14px 0; color: #333;">Your Heading Text</h4>' },
                    ],
                    paragraph: [
                        { name: 'Standard Paragraph', html: '<p style="font-size: 16px; line-height: 1.6; margin: 15px 0; color: #555;">Your paragraph text goes here.</p>' },
                        { name: 'Large Text', html: '<p style="font-size: 18px; line-height: 1.6; margin: 15px 0; color: #555;">Your large paragraph text goes here.</p>' },
                        { name: 'Small Text', html: '<p style="font-size: 14px; line-height: 1.6; margin: 15px 0; color: #666;">Your small paragraph text goes here.</p>' },
                    ],
                    image: [
                        { name: 'Full Width Image', html: '<div style="margin: 20px 0;"><img src="https://via.placeholder.com/600x300" alt="Image" style="width: 100%; max-width: 100%; height: auto; display: block;"></div>' },
                        { name: 'Centered Image', html: '<div style="text-align: center; margin: 20px 0;"><img src="https://via.placeholder.com/400x200" alt="Image" style="max-width: 100%; height: auto;"></div>' },
                    ],
                    button: [
                        { name: 'Primary Button', html: '<div style="text-align: center; margin: 20px 0;"><a href="#" style="display: inline-block; padding: 12px 30px; background: #007bff; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: 600;">Click Here</a></div>' },
                        { name: 'Secondary Button', html: '<div style="text-align: center; margin: 20px 0;"><a href="#" style="display: inline-block; padding: 12px 30px; background: #6c757d; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: 600;">Click Here</a></div>' },
                        { name: 'Outline Button', html: '<div style="text-align: center; margin: 20px 0;"><a href="#" style="display: inline-block; padding: 12px 30px; background: transparent; color: #007bff; text-decoration: none; border: 2px solid #007bff; border-radius: 4px; font-weight: 600;">Click Here</a></div>' },
                    ],
                    divider: [
                        { name: 'Solid Line', html: '<hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">' },
                        { name: 'Dashed Line', html: '<hr style="border: none; border-top: 2px dashed #e0e0e0; margin: 30px 0;">' },
                        { name: 'Thick Line', html: '<hr style="border: none; border-top: 3px solid #007bff; margin: 30px 0;">' },
                    ],
                    spacer: [
                        { name: 'Small Spacer (20px)', html: '<div style="height: 20px;"></div>' },
                        { name: 'Medium Spacer (40px)', html: '<div style="height: 40px;"></div>' },
                        { name: 'Large Spacer (60px)', html: '<div style="height: 60px;"></div>' },
                    ]
                };
                console.log('✅ blockTemplates initialized');
            } else {
                console.log('⚠️ blockTemplates already exists, skipping re-initialization');
            }
            
            // Create local reference after initialization
            const blockTemplates = window.blockTemplates;

            function showBlockTemplates(blockType, templates, button) {
                const existing = document.querySelector('.block-templates');
                if (existing) {
                    existing.remove();
                }

                const templateSelector = document.createElement('div');
                templateSelector.className = 'block-templates show';
                
                templates.forEach(function(template) {
                    const item = document.createElement('div');
                    item.className = 'block-template-item';
                    
                    const escapedName = String(template.name || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    const escapedPreview = String(template.html || '').substring(0, 50).replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    
                    item.innerHTML = 
                        '<div style="font-weight: 600; font-size: 14px;">' + escapedName + '</div>' +
                        '<div class="block-template-preview">' + escapedPreview + '...</div>';
                    
                    item.addEventListener('click', function() {
                        insertBlock(template.html);
                        templateSelector.remove();
                    });
                    templateSelector.appendChild(item);
                });

                button.parentNode.appendChild(templateSelector);
                
                setTimeout(function() {
                    document.addEventListener('click', function closeHandler(e) {
                        if (!templateSelector.contains(e.target) && !button.contains(e.target)) {
                            templateSelector.remove();
                            document.removeEventListener('click', closeHandler);
                        }
                    });
                }, 100);
            }

            // Block button handlers
            function setupBlockButtons() {
                console.log('🔧 Setting up block buttons...');
                const blockButtons = document.querySelectorAll('.block-button');
                console.log('Found block buttons:', blockButtons.length);
                
                blockButtons.forEach(function(button) {
                    const newButton = button.cloneNode(true);
                    button.parentNode.replaceChild(newButton, button);
                    
                    newButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        const blockType = this.getAttribute('data-block');
                        console.log('Block button clicked:', blockType);
                        
                        if (!blockTemplates) {
                            console.error('❌ blockTemplates not defined');
                            alert('Block templates not loaded. Please refresh the page.');
                            return;
                        }
                        
                        const templates = blockTemplates[blockType];
                        console.log('Templates for', blockType, ':', templates ? templates.length : 0);
                        
                        if (templates && templates.length > 0) {
                            if (templates.length === 1) {
                                console.log('Inserting single template:', templates[0].name);
                                insertBlock(templates[0].html);
                            } else {
                                console.log('Showing template selector for', templates.length, 'options');
                                showBlockTemplates(blockType, templates, this);
                            }
                        } else {
                            console.warn('No templates found for block type:', blockType);
                        }
                    });
                });
                console.log('✅ Block buttons setup complete');
            }
            
            setTimeout(setupBlockButtons, 500);
            setTimeout(setupBlockButtons, 1500);

            // Drag and Drop Functionality
            function initializeDragDrop() {
                console.log('Initializing drag and drop...');
                
                function makeWidgetsDraggable() {
                    const widgetItems = document.querySelectorAll('.widget-item');
                    console.log('Found widgets:', widgetItems.length);
                    
                    widgetItems.forEach(function(widget) {
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

                function setupEditorDropZone(element) {
                    if (!element) {
                        console.error('Element not found for drop zone setup');
                        return;
                    }

                    console.log('Setting up drop zone on:', element);

                    element.addEventListener('dragover', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.dataTransfer.dropEffect = 'move';
                        element.classList.add('drag-over');
                        console.log('Drag over visual canvas');
                    }, false);

                    element.addEventListener('dragleave', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const rect = element.getBoundingClientRect();
                        const x = e.clientX;
                        const y = e.clientY;
                        if (x < rect.left || x > rect.right || y < rect.top || y > rect.bottom) {
                            element.classList.remove('drag-over');
                        }
                    }, false);

                    element.addEventListener('drop', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Drop event triggered on visual canvas');
                        
                        element.classList.remove('drag-over');

                        let widgetHtml = '';
                        if (draggedWidget && draggedWidget.dataset.widgetHtml) {
                            widgetHtml = draggedWidget.dataset.widgetHtml;
                            console.log('Got widget HTML from draggedWidget, length:', widgetHtml.length);
                        } else {
                            widgetHtml = e.dataTransfer.getData('text/html') || 
                                        e.dataTransfer.getData('text/plain');
                            console.log('Got widget HTML from dataTransfer, length:', widgetHtml ? widgetHtml.length : 0);
                        }

                        if (widgetHtml && widgetHtml.trim() !== '') {
                            console.log('Inserting widget via drag-drop...');
                            if (window.insertWidget) {
                                window.insertWidget(widgetHtml);
                            } else {
                                console.error('insertWidget function not found');
                                alert('Error: Insert function not available. Please refresh the page.');
                            }
                        } else {
                            console.error('No widget HTML found');
                            alert('No widget data found. Please try again.');
                        }
                    }, false);
                }

                makeWidgetsDraggable();

                if (visualCanvas) {
                    setupEditorDropZone(visualCanvas);
                    console.log('Drop zone set up on visual canvas');
                } else {
                    console.error('Visual canvas not found for drop zone setup');
                }
            }

            // Sample data for preview
            // Build logo key separately to avoid Blade parsing
            const widgetLogoKey = 'logo' + '_' + 'url';
            const widgetPreviewSampleData = {
                'property.address': '123 Example Street',
                'property.postcode': 'SW1A 1AA',
                'property.asking_price': '450,000',
                'buyer.name': 'John Doe',
                'buyer.email': 'john.doe@example.com',
                'offer.offer_amount': '425,000',
                'viewing.viewing_date': '2024-02-15',
                'viewing.viewing_time': '14:00',
                'viewing.status': 'Confirmed',
                'recipient.name': 'Jane Smith',
                'user.email': 'user@example.com',
                'password': 'TempPass123!',
                'url': '#',
                'text': 'Sample Text',
                'message': 'This is a sample message',
                'title': 'Sample Title',
                'year': new Date().getFullYear(),
                'item1': 'First item',
                'item2': 'Second item',
                'item3': 'Third item',
                'content': 'Sample content here'
            };
            // Add logo key dynamically
            widgetPreviewSampleData[widgetLogoKey] = window.templateBuilderLogoUrl || {!! json_encode($logoUrl ?? asset('media/abodeology-logo.png')) !!};

            function replaceWidgetVariables(html) {
                let result = html;
                const openBrace = '{';
                const closeBrace = '}';
                const variablePattern = new RegExp(openBrace + openBrace + '([^' + closeBrace + ']+)' + closeBrace + closeBrace, 'g');
                result = result.replace(variablePattern, function(match, variable) {
                    variable = variable.trim();
                    if (widgetPreviewSampleData[variable] !== undefined) {
                        return widgetPreviewSampleData[variable];
                    }
                    for (let key in widgetPreviewSampleData) {
                        if (variable.includes(key.split('.')[0])) {
                            return widgetPreviewSampleData[key];
                        }
                    }
                    return '[Sample ' + variable + ']';
                });
                return result;
            }

            function showWidgetPreview(html, name) {
                console.log('🔍 showWidgetPreview called for:', name);
                try {
                    const previewHtml = replaceWidgetVariables(html);
                    
                    const escapedPreviewHtml = previewHtml
                        .replace(/\\/g, '\\\\')
                        .replace(/`/g, '\\`')
                        .replace(/\${/g, '\\${');
                    
                    const escapedName = String(name || 'Widget')
                        .replace(/\\/g, '\\\\')
                        .replace(/`/g, '\\`')
                        .replace(/\${/g, '\\${');
                    
                    const fullHtml = '<!DOCTYPE html>' +
                        '<html>' +
                        '<head>' +
                            '<meta charset="utf-8">' +
                            '<meta name="viewport" content="width=device-width, initial-scale=1.0">' +
                            '<style>' +
                                'body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }' +
                                '.email-wrapper { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }' +
                            '</style>' +
                        '</head>' +
                        '<body>' +
                            '<div class="email-wrapper">' +
                                escapedPreviewHtml +
                            '</div>' +
                        '</body>' +
                        '</html>';
                    
                    let modal = document.getElementById('widgetPreviewModal');
                    if (!modal) {
                        modal = document.createElement('div');
                        modal.id = 'widgetPreviewModal';
                        modal.className = 'widget-preview-modal';
                        modal.innerHTML = 
                            '<div class="widget-preview-modal-content">' +
                                '<div class="widget-preview-modal-header">' +
                                    '<h5>Preview: ' + escapedName + '</h5>' +
                                    '<button type="button" class="widget-preview-close" onclick="closeWidgetPreview()">&times;</button>' +
                                '</div>' +
                                '<div class="widget-preview-modal-body">' +
                                    '<div class="widget-preview-note">' +
                                        '<strong>Note:</strong> This is a preview with sample data. Variables are replaced with example values.' +
                                    '</div>' +
                                    '<iframe id="widgetPreviewFrame" style="width: 100%; height: 500px; border: none; background: white;"></iframe>' +
                                '</div>' +
                            '</div>';
                        document.body.appendChild(modal);
                    }
                    
                    const iframe = document.getElementById('widgetPreviewFrame');
                    if (iframe) {
                        iframe.srcdoc = fullHtml;
                    }
                    modal.classList.add('show');
                    console.log('✅ Widget preview modal shown');
                } catch (error) {
                    console.error('❌ Error in showWidgetPreview:', error);
                    alert('Error showing preview: ' + error.message);
                }
            }

            function closeWidgetPreview() {
                const modal = document.getElementById('widgetPreviewModal');
                if (modal) {
                    modal.classList.remove('show');
                }
            }

            window.showWidgetPreview = showWidgetPreview;
            window.closeWidgetPreview = closeWidgetPreview;

            // Parse template body to identify widgets
            window.parseTemplateToWidgets = function(templateBody, widgets) {
                if (!templateBody || !widgets || widgets.length === 0) {
                    console.log('No template body or widgets to parse');
                    return;
                }

                console.log('Parsing template to identify widgets...');
                
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = templateBody;
                
                const identifiedWidgets = [];
                const widgetMap = {};
                
                widgets.forEach(function(widget) {
                    const normalized = widget.html.replace(/\s+/g, ' ').trim();
                    widgetMap[normalized] = widget;
                    
                    const keyPattern = new RegExp(widget.key.replace(/-/g, '[\\s-]'), 'i');
                    if (keyPattern.test(templateBody)) {
                        widgetMap[widget.key] = widget;
                    }
                });

                let remainingBody = templateBody;
                const widgetMatches = [];
                
                const sortedWidgets = widgets.slice().sort((a, b) => b.html.length - a.html.length);
                
                sortedWidgets.forEach(function(widget) {
                    const widgetHtml = widget.html.replace(/\s+/g, ' ').trim();
                    const normalizedBody = remainingBody.replace(/\s+/g, ' ').trim();
                    
                    const escapedHtml = widgetHtml.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    const pattern = new RegExp(escapedHtml.replace(/\s+/g, '\\s+'), 'i');
                    
                    if (pattern.test(normalizedBody)) {
                        const matchIndex = normalizedBody.search(pattern);
                        if (matchIndex !== -1) {
                            widgetMatches.push({
                                widget: widget,
                                index: matchIndex,
                                html: widgetHtml
                            });
                        }
                    }
                });

                widgetMatches.sort((a, b) => a.index - b.index);

                if (widgetMatches.length > 0) {
                    console.log('Found ' + widgetMatches.length + ' widget matches');
                    
                    const hiddenTextarea = document.getElementById('template-body-editor');
                    if (hiddenTextarea) {
                        hiddenTextarea.value = templateBody;
                        renderVisualCanvas(templateBody);
                        
                        const notification = document.createElement('div');
                        notification.style.cssText = 'position: fixed; top: 100px; right: 20px; background: #28a745; color: white; padding: 15px 20px; border-radius: 6px; z-index: 10001; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
                        notification.innerHTML = '<strong>Template Loaded!</strong><br>Found ' + widgetMatches.length + ' widget(s). Click on widgets to edit them.';
                        document.body.appendChild(notification);
                        
                        setTimeout(function() {
                            notification.style.transition = 'opacity 0.5s';
                            notification.style.opacity = '0';
                            setTimeout(function() {
                                notification.remove();
                            }, 500);
                        }, 3000);
                    }
                } else {
                    const hiddenTextarea = document.getElementById('template-body-editor');
                    if (hiddenTextarea) {
                        hiddenTextarea.value = templateBody;
                        renderVisualCanvas(templateBody);
                    }
                }
            };

            // Setup insert widget button handlers
            function setupInsertWidgetButtons() {
                const insertButtons = document.querySelectorAll('.btn-insert-widget');
                console.log('Found insert buttons:', insertButtons.length);
                
                insertButtons.forEach(function(button) {
                    const newButton = button.cloneNode(true);
                    button.parentNode.replaceChild(newButton, button);
                    
                    newButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        console.log('Insert button clicked');
                        const widgetHtml = this.getAttribute('data-widget-html');
                        console.log('Widget HTML from attribute:', widgetHtml ? widgetHtml.substring(0, 50) + '...' : 'NOT FOUND');
                        
                        if (widgetHtml) {
                            console.log('Button clicked, widget HTML length:', widgetHtml.length);
                            if (window.insertWidget) {
                                window.insertWidget(widgetHtml);
                            } else {
                                console.error('insertWidget function not available');
                                alert('Error: Insert function not available. Please refresh the page.');
                            }
                        } else {
                            console.error('No widget HTML found in data attribute');
                            const widgetItem = this.closest('.widget-item');
                            if (widgetItem && widgetItem.dataset.widgetHtml) {
                                console.log('Found widget HTML in parent item');
                                if (window.insertWidget) {
                                    window.insertWidget(widgetItem.dataset.widgetHtml);
                                }
                            } else {
                                alert('Widget HTML not found. Please try dragging the widget instead.');
                            }
                        }
                    });
                });

                // Setup preview button handlers
                const previewButtons = document.querySelectorAll('.btn-preview-widget');
                console.log('Found preview buttons:', previewButtons.length);
                
                previewButtons.forEach(function(button) {
                    const newButton = button.cloneNode(true);
                    button.parentNode.replaceChild(newButton, button);
                    
                    newButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        console.log('Preview button clicked');
                        const widgetHtml = this.getAttribute('data-widget-html');
                        const widgetName = this.getAttribute('data-widget-name') || 'Widget';
                        
                        if (widgetHtml) {
                            if (window.showWidgetPreview) {
                                window.showWidgetPreview(widgetHtml, widgetName);
                            } else {
                                console.error('showWidgetPreview function not available');
                                alert('Preview function not available. Please refresh the page.');
                            }
                        } else {
                            const widgetItem = this.closest('.widget-item');
                            if (widgetItem && widgetItem.dataset.widgetHtml) {
                                if (window.showWidgetPreview) {
                                    window.showWidgetPreview(widgetItem.dataset.widgetHtml, widgetItem.dataset.widgetName || 'Widget');
                                }
                            }
                        }
                    });
                });
            }

            // Initialize everything
            function initializeAll() {
                console.log('Initializing all functionality...');
                setupInsertWidgetButtons();
                initializeDragDrop();
            }

            initializeAll();

            setTimeout(function() {
                console.log('Re-initializing after delay...');
                initializeAll();
            }, 1000);

        });
    </script>
@endpush

