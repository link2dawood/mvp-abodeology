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
                                    class="btn btn-outline-primary btn-sm btn-preview-widget"
                                    data-widget-html="{{ htmlspecialchars($widget->html, ENT_QUOTES, 'UTF-8') }}"
                                    data-widget-name="{{ $widget->name }}"
                                    title="Preview widget"
                                >
                                    Preview
                                </button>
                                <button 
                                    type="button" 
                                    class="btn btn-primary btn-sm btn-insert-widget"
                                    data-widget-html="{{ htmlspecialchars($widget->html, ENT_QUOTES, 'UTF-8') }}"
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

