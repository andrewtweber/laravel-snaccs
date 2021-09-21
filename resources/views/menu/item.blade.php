<?php /** @var \Snaccs\Menu\MenuItem $item */ ?>

<li class="nav-item">
    <a href="{{ $item->url }}" class="nav-link {{ $item->isActive($active ?? null, $sub_active ?? null) ? 'active' : '' }}">
        <i class="{{ $item->icon }} mr-md-2"></i><span>{{ $item->label }}</span>{{
        isset($item->badge) ? $item->badge->html() : '' }}</a>
</li>
