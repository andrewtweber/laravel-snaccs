<?php /** @var \Snaccs\Menu\MenuItem $item */ ?>

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ $item->isActive($active ?? null, $sub_active ?? null) ? 'active' : '' }}"
       href="#" id="{{ $item->section ?? 'nav' }}Dropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
       aria-expanded="false">
        <i class="{{ $item->icon }} mr-md-2"></i><span>{{ $item->label }}</span>{{
            isset($item->badge) ? $item->badge->html() : '' }}</a>
    <div class="dropdown-menu" aria-labelledby="{{ $item->section ?? 'nav' }}Dropdown">
        @foreach ($item->children as $child)
            <a href="{{ $child->url }}" class="dropdown-item">
                <i class="{{ $child->icon }} fa-fw mr-2"></i><span>{{ $child->label }}</span>{{
                    isset($child->badge) ? $child->badge->html() : '' }}
            </a>
        @endforeach
    </div>
</li>
