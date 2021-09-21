<?php /** @var \Snaccs\Menu\Menu $menu */ ?>

<ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
    @foreach ($menu->items as $counter => $item)
        @include('snaccs::menu.tab', ['is_active' => $counter === 0])
    @endforeach
</ul>
