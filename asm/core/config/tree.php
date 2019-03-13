<?php

return [
    'main' => [
        'site' => [
            'index' => [
                null,
                'page' => '[\d]{1,7}'
            ]
        ],
        'user' => [
            'index' => [
                'id' => '[\d]{1,11}'
            ],
            'list' => [
                null,
                'page' => '[\d]{1,7}'
            ],
        ],
    ],
];