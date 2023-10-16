<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Set the http status code when the response fails
    |--------------------------------------------------------------------------
    |
    | the reference options are false, 200, 500
    |
    | false, stricter http status codes such as 404, 401, 403, 500, etc. will be returned
    | 200, All failed responses will also return a 200 status code
    | 500, All failed responses return a 500 status code
    */
    'error_code' => false,

    'enum'      => '',//\yuanzhihai\enum\think\Enums\HttpStatusCodeEnum::class

    //  You can set some attributes (eg:code/message/header/options) for the exception, and it will override the default attributes of the exception
    'exception' => [
        \think\exception\ValidateException::class         => [
            'code' => 422,
        ],
        \think\exception\HttpException::class             => [
            'message' => '',
        ],
        \think\db\exception\ModelNotFoundException::class => [
            'message' => '',
        ],
    ],

    // Any key that returns data exists supports custom aliases and display.
    'format'    => [
        'class'  => \yuanzhihai\response\think\support\Format::class,
        'config' => [
            // key => config
            'status'  => ['alias' => 'status', 'show' => true],
            'code'    => ['alias' => 'code', 'show' => true],
            'message' => ['alias' => 'message', 'show' => true],
            'error'   => ['alias' => 'error', 'show' => true],
            'data'    => [
                'alias' => 'data',
                'show'  => true,

                'fields' => [
                    // When data is nested with data, such as returning paged data, you can also set an alias for the inner data
                    'data' => ['alias' => 'data', 'show' => true], // data/rows/list
                    'meta' => [
                        'alias'  => 'meta',
                        'show'   => true,
                        'fields' => [
                            'pagination' => [
                                'alias'  => 'pagination',
                                'show'   => true,
                                'fields' => [
                                    'total'        => ['alias' => 'total', 'show' => true],
                                    'count'        => ['alias' => 'count', 'show' => true],
                                    'per_page'     => ['alias' => 'per_page', 'show' => true],
                                    'current_page' => ['alias' => 'current_page', 'show' => true],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];