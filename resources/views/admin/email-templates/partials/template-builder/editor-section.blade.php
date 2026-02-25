<div class="editor-main-section">
    <label for="template-body-editor" class="form-label">Email Template Builder</label>
    <div class="editor-wrapper">
        @include('admin.email-templates.partials.template-builder.block-toolbar')
        
        {{-- Visual Canvas - What admins see --}}
        <div id="visual-email-canvas" class="visual-email-canvas">
            <div class="canvas-empty-state" id="canvas-empty-state">
                <div style="text-align: center; padding: 60px 20px; color: #6c757d;">
                    <div style="font-size: 48px; margin-bottom: 20px; color: #6c757d;"><i class="fa fa-envelope"></i></div>
                    <h3 style="color: #495057; margin-bottom: 10px;">Start Building Your Email</h3>
                    <p style="font-size: 14px; margin-bottom: 0;">Drag widgets from the left sidebar or use the buttons above to add content blocks.</p>
                </div>
            </div>
        </div>
        
        {{-- Hidden textarea that stores the actual HTML --}}
        <textarea
            id="template-body-editor"
            name="body"
            class="form-control @error('body') is-invalid @enderror"
            style="display: none;"
        >{{ old('body', $template->body ?? '') }}</textarea>
        @error('body')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

