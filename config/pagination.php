<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pagination View
    |--------------------------------------------------------------------------
    |
    | This option controls the default view that gets used when rendering
    | pagination links across your application. You have the option to set
    | a view that is compatible with popular CSS frameworks in Laravel.
    |
    | Supported: "bootstrap-4", "bootstrap-5", "tailwind", "headless"
    |
    */

    'view' => 'pagination.tailwind',

    /*
    |--------------------------------------------------------------------------
    | Pagination Path
    |--------------------------------------------------------------------------
    |
    | Here you may specify the URL path that will be used to generate
    | the URL structure for your pagination links.
    |
    */

    'path' => '/',

    /*
    |--------------------------------------------------------------------------
    | Pagination Query Parameter
    |--------------------------------------------------------------------------
    |
    | Here you may specify what query string variable will be used to denote
    | the pagination page in the URL.
    |
    */

    'query' => 'page',

    /*
    |--------------------------------------------------------------------------
    | Pagination Default Per Page
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default number of items per page for pagination.
    |
    */

    'per_page' => 25,

    /*
    |--------------------------------------------------------------------------
    | Pagination Options
    |--------------------------------------------------------------------------
    |
    | Available per page options for users to choose from.
    |
    */

    'per_page_options' => [10, 25, 50, 100],

    /*
    |--------------------------------------------------------------------------
    | API Pagination Defaults
    |--------------------------------------------------------------------------
    |
    | Configuration for API endpoint pagination.
    |
    */

    'api' => [
        'default_per_page' => 25,
        'max_per_page' => 100,
        'min_per_page' => 1,
    ],
];
