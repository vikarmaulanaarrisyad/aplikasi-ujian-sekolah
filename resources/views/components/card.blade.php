<div {{ $attributes->merge(['class' => 'card card-outline card-danger']) }}>
    @isset($header)
        <div class="card-header">
            @isset($headerButton)
                <div class="card-tools">
                    {{ $headerButton }}
                </div>
            @endisset
            {{ $header }}
        </div>
    @endisset

    <div class="card-body">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endisset
</div>
