<?php /** @var \Snaccs\Menu\MenuItem $item */ ?>

<li class="nav-item">
    <a href="{{ $item->url }}" class="nav-link {{ $item->isActive($active ?? null, $sub_active ?? null) ? 'active' : '' }}">
        {{ $item->icon?->html() }}<span>{{ $item->label }}</span>{{
        isset($item->badge) ? $item->badge->html() : '' }}</a>
</li>
