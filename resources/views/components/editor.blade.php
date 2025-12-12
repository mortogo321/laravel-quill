<div
    class="quill-editor-wrapper {{ $attributes->get('class', '') }}"
    @if($height) style="--quill-editor-height: {{ $height }};" @endif
>
    {{-- Hidden input to store the content --}}
    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $id }}-input"
        value="{{ $value }}"
        @if($required) required @endif
    >

    {{-- Hidden input for HTML content --}}
    <input
        type="hidden"
        name="{{ $name }}_html"
        id="{{ $id }}-html"
    >

    {{-- The Quill editor container --}}
    <div
        id="{{ $id }}"
        class="quill-editor"
        data-quill-config='@json($getConfig())'
        data-quill-input="{{ $id }}-input"
        data-quill-html="{{ $id }}-html"
        @if($uploadUrl) data-quill-upload-url="{{ $uploadUrl }}" @endif
    >{!! $value ? (json_decode($value, true) !== null ? '' : $value) : '' !!}</div>
</div>
