{{-- Template builder styles: include in @section('styles') so they render in <head> --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
<style>
        .template-builder-page {
            padding: 0 !important;
            margin: 0 !important;
            height: 100vh;
            overflow: hidden;
            background: #f5f5f5;
            display: flex;
            flex-direction: column;
        }

        .template-builder-grid {
            display: grid;
            grid-template-columns: 320px 1fr 320px;
            gap: 0;
            flex: 1;
            min-height: 0;
            align-items: stretch;
            background: #ffffff;
            margin: 0;
            padding: 0;
            overflow: hidden;
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
                /* max-width: 280px; */
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
            padding: 20px;
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
            overflow-x: hidden;
            position: relative;
            height: calc(100vh - 280px);
        }

        .widget-list:focus,
        .widget-list:hover {
            overflow-y: auto;
        }

        .variables-section {
            /* min-width: 320px; */
            max-width: 320px;
        }

        .variables-section .mb-2,
        .widgets-section .text-muted {
            padding: 0 20px;
            margin-bottom: 10px;
        }

        .variables-section .mb-2 {
            padding: 0 20px;
        }

        .variables-section > .form-label {
            padding: 15px 20px;
        }

        .variables-section .text-muted {
            padding: 0 20px;
            margin-bottom: 15px;
        }

        .variables-section .variable-list {
            padding: 15px 20px 20px 20px;
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

        .note-toolbar {
            display: none !important;
        }

        .visual-email-canvas {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            background: #f5f5f5;
            padding: 20px;
            /* min-height: 400px; */
            position: relative;
            transition: all 0.2s;
        }

        .visual-email-canvas.drag-over {
            background: #e3f2fd;
            border: 2px dashed #2196F3;
            border-radius: 4px;
        }

        .canvas-empty-state {
            display: flex;
            align-items: center;
            justify-content: center;
            /* min-height: 400px; */
        }

        .canvas-email-preview {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        /* Ensure images render properly in visual canvas */
        .canvas-email-preview img {
            display: block;
            max-width: 100%;
            height: auto;
        }

        /* Ensure all HTML elements render visually, not as code */
        .canvas-email-preview * {
            display: revert;
            visibility: visible;
        }

        /* Prevent code-like display */
        .canvas-email-preview pre,
        .canvas-email-preview code {
            display: none;
        }

        .canvas-widget-block {
            position: relative;
            margin: 0;
            padding: 0;
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid transparent;
        }

        .canvas-widget-block:hover {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .canvas-widget-block.selected {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
        }

        .canvas-widget-block p,
        .canvas-widget-block h1,
        .canvas-widget-block h2,
        .canvas-widget-block h3,
        .canvas-widget-block h4,
        .canvas-widget-block h5,
        .canvas-widget-block h6,
        .canvas-widget-block span:not(.variable-placeholder),
        .canvas-widget-block div:not(.variable-placeholder),
        .canvas-widget-block li,
        .canvas-widget-block td {
            cursor: text;
            outline: none;
        }

        .canvas-widget-block p:focus,
        .canvas-widget-block h1:focus,
        .canvas-widget-block h2:focus,
        .canvas-widget-block h3:focus,
        .canvas-widget-block h4:focus,
        .canvas-widget-block h5:focus,
        .canvas-widget-block h6:focus,
        .canvas-widget-block span:focus:not(.variable-placeholder),
        .canvas-widget-block div:focus:not(.variable-placeholder),
        .canvas-widget-block li:focus,
        .canvas-widget-block td:focus {
            background: rgba(0, 123, 255, 0.05);
            border-radius: 2px;
        }

        .canvas-widget-block::before {
            content: '✏️ Edit';
            position: absolute;
            top: 5px;
            right: 5px;
            background: #007bff;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 10;
            pointer-events: none;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        .canvas-widget-block:hover::before,
        .canvas-widget-block.selected::before {
            opacity: 1;
        }

        /* Allow text selection and editing on widget blocks */
        .canvas-widget-block {
            user-select: auto;
            -webkit-user-select: auto;
            -moz-user-select: auto;
            -ms-user-select: auto;
        }

        .canvas-widget-block * {
            user-select: text;
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
        }
        
        /* Make contenteditable elements clearly editable */
        [contenteditable="true"] {
            cursor: text;
            outline: none;
            min-height: 1em;
        }
        
        [contenteditable="true"]:focus {
            outline: 2px solid #32b3ac;
            outline-offset: 2px;
            background-color: rgba(50, 179, 172, 0.05);
            border-radius: 4px;
        }
        
        [contenteditable="true"]:hover {
            background-color: rgba(50, 179, 172, 0.02);
            border-radius: 4px;
        }

        .variable-placeholder {
            display: inline-block;
            background: #e8f4f3;
            color: #2CB8B4;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.9em;
            border: 1px dashed #2CB8B4;
            cursor: help;
        }

        .variable-placeholder::before {
            content: '📋 ';
        }

        .widget-properties-panel {
            position: fixed;
            right: 0;
            top: 70px;
            width: 350px;
            height: calc(100vh - 70px);
            background: #ffffff;
            border-left: 1px solid #e0e0e0;
            box-shadow: -2px 0 8px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .properties-panel-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fa;
        }

        .close-properties {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-properties:hover {
            color: #000;
        }

        .properties-panel-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .property-field {
            margin-bottom: 20px;
        }

        .property-field label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            color: #495057;
            margin-bottom: 8px;
        }

        .property-field input,
        .property-field textarea,
        .property-field select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 14px;
        }

        .property-field textarea {
            min-height: 80px;
            resize: vertical;
        }

        .block-editor-toolbar {
            display: flex;
            gap: 8px;
            padding: 12px 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            flex-wrap: wrap;
            align-items: center;
        }

        .toolbar-divider {
            width: 1px;
            height: 24px;
            background: #dee2e6;
            margin: 0 4px;
        }

        .undo-redo-buttons {
            display: flex;
            gap: 4px;
            margin-left: auto;
        }

        .undo-redo-button {
            padding: 6px 12px;
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            color: #495057;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .undo-redo-button:hover:not(:disabled) {
            background: #007bff;
            color: #ffffff;
            border-color: #007bff;
        }

        .undo-redo-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .block-button {
            padding: 8px 16px;
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            color: #495057;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .block-button:hover {
            background: #007bff;
            color: #ffffff;
            border-color: #007bff;
        }

        .block-button.active {
            background: #007bff;
            color: #ffffff;
            border-color: #007bff;
        }

        .spacer-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            background: #dc3545;
            border-radius: 50%;
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
            line-height: 1;
        }

        .block-templates {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-top: none;
            padding: 10px;
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
        }

        .block-templates.show {
            display: block;
        }

        .block-template-item {
            padding: 10px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.2s;
        }

        .block-template-item:hover {
            background: #f8f9fa;
        }

        .block-template-item:last-child {
            border-bottom: none;
        }

        .block-template-preview {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
        }

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
            padding-right: 35px;
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

        .widget-item-actions {
            margin-top: 10px;
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .btn-preview-widget,
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

        .widget-preview-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        .widget-preview-modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .widget-preview-modal-content {
            background-color: #ffffff;
            margin: auto;
            border-radius: 8px;
            width: 90%;
            max-width: 900px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .widget-preview-modal-header {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fa;
            border-radius: 8px 8px 0 0;
        }

        .widget-preview-modal-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .widget-preview-close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #666;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .widget-preview-close:hover {
            color: #000;
        }

        .widget-preview-modal-body {
            padding: 20px;
            overflow: auto;
            flex: 1;
        }

        .widget-preview-note {
            background: #e8f4f3;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 13px;
            color: #495057;
        }

        .widget-preview-note code {
            background: #ffffff;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
        }
    </style>

