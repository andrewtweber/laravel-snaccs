<?php /** @var \Snaccs\Menu\Menu $menu */ ?>

<ul class="navbar-nav mr-auto">
    @foreach ($menu->items as $item)
        @if (count($item->children) > 0)
            @include('snaccs::menu.dropdown')
        @else
            @include('snaccs::menu.item')
        @endif
    @endforeach
</ul>
