<?php

return [

    /**
     * Money formatting
     */
    'money' => [

        // Strings
        'currency_prefix'  => '$',
        'currency_suffix'  => '',
        'positive_prefix'  => '',
        'positive_suffix'  => '',
        'negative_prefix'  => '-',
        'negative_suffix'  => '',

        // Options
        // If false, will display "$2" instead of "$2.00"
        'show_zero_cents'  => true,

        // Typically 100, but 1000 for some currencies
        'cents_per_dollar' => 100,

    ],

    /**
     * Byte formatting suffixes
     */
    'bytes' => [
        ' bytes',
        ' kb',
        ' MB',
        ' GB',
        ' TB',
    ],

];
