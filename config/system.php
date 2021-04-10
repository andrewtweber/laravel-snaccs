<?php

return [

    // Easily disable registration
    'registration_enabled' => true,

    /**
     * Username requirements
     */
    'usernames' => [

        // Database table for uniqueness
        'table' => 'users',

        // Length requirements
        'min' => 1,
        'max' => 25,

        // Which characters besides alphanumeric are allowed?
        'allowed_special_chars' => '-_.',

        // Is '1234' allowed?
        'allow_numbers_only' => true,

        // Is '_._' allowed?
        'allow_special_chars_only' => false,

        // @todo Is 'café' allowed?
        // 'allow_diacritics' => false,

        // Reserved usernames
        'reserved' => [
            'admin',
            'administrator',
            'moderator',
            'guest',
            'system',
            'webmaster',
        ],

    ],

];
