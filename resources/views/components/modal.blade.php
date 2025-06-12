<div id="{{ $id ?? 'modal' }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog {{ $size ?? '' }}" role="document">
        @isset($content)
            @include($content)
        @endisset
    </div>
</div>
