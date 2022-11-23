<?php /** @var \Snaccs\Breadcrumbs\Breadcrumb $breadcrumb */ ?>

<li class="breadcrumb-item{{ $breadcrumb->active ? ' active' : '' }}">
    <a href="{{ $breadcrumb->url }}">{{ $breadcrumb->label }}</a>
</li>
