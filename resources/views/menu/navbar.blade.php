<?php /** @var \Snaccs\Menu\Menu $menu */ ?>

<ul class="navbar-nav mr-auto">
    @foreach ($menu->items as $item)
        @if (count($item->children) > 0)
            @include('includes.menu.dropdown')
        @else
            @include('includes.menu.item')
        @endif
    @endforeach
</ul>
