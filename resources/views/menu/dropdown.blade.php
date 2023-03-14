<?php /** @var \Snaccs\Menu\MenuItem $item */ ?>

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ $item->isActive($active ?? null, $sub_active ?? null) ? 'active' : '' }}"
       href="#" id="{{ $item->section ?? 'nav' }}Dropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
       aria-expanded="false">
        {{ $item->icon?->html() }}<span>{{ $item->label }}</span>{{
            isset($item->badge) ? $item->badge->html() : '' }}</a>
    <div class="dropdown-menu" aria-labelledby="{{ $item->section ?? 'nav' }}Dropdown">
        @foreach ($item->children as $child)
            @if ($child instanceof \Snaccs\Menu\MenuDivider)
                <div class="dropdown-divider"></div>
            @else
                <a href="{{ $child->url }}" class="dropdown-item">
                    {{ $child->icon?->html('fa-fw mr-2') }}<span>{{ $child->label }}</span>{{
                        isset($child->badge) ? $child->badge->html() : '' }}
                </a>
            @endif
        @endforeach
    </div>
</li>
