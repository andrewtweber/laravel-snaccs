<?php /** @var \Snaccs\Breadcrumbs\BreadcrumbCollection $breadcrumbs */ ?>

<ol class="breadcrumb">
    @foreach ($breadcrumbs as $breadcrumb)
        {{ $breadcrumb->toHtml() }}
    @endforeach
</ol>
