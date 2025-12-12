<div {{ $attributes->merge(['class' => 'quill-content ql-editor ' . ($class ?? '')]) }}>
    {!! $html !!}
</div>
