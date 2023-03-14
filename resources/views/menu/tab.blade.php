<?php
/** @var \Snaccs\Menu\MenuItem $item */
/** @var bool $is_active */
?>

<li class="nav-item" role="presentation">
    <a class="nav-link{{ $is_active ? ' active' : '' }}" id="{{ $item->url }}-tab" data-toggle="tab"
       href="#{{ $item->url }}-body" role="tab" aria-controls="{{ $item->url }}-body"
       aria-selected="{{ $is_active ? 'true' : 'false' }}">
        {{ $item->icon?->html('mr-2') }}{{ $item->label }}{{
            isset($item->badge) ? $item->badge->html() : '' }}
    </a>
</li>
