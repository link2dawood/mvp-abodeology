{{-- Template Builder - Component-based structure --}}
{{-- Styles are loaded via @section('styles') in edit/create so they appear in <head> --}}

{{-- Include Scripts --}}
@include('admin.email-templates.partials.template-builder.scripts')

{{-- Main Grid Layout --}}
<div class="template-builder-grid">
    {{-- Widgets Section (Left) --}}
    @include('admin.email-templates.partials.template-builder.widgets-section')

    {{-- Editor Section (Center) --}}
    @include('admin.email-templates.partials.template-builder.editor-section')

    {{-- Properties Panel (Overlay) --}}
    @include('admin.email-templates.partials.template-builder.properties-panel')

    {{-- Variables Section (Right) --}}
    @include('admin.email-templates.partials.template-builder.variables-section')
</div>
