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

    // lang/zh-cn/enums.php
    'locale'     => 'enums', //\yuanzhihai\enum\think\Enums\HttpStatusCodeEnum::class

    //  You can set some attributes (eg:code/message/header/options) for the exception, and it will override the default attributes of the exception
    'exception'  => [
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
    'format'     => [
        'class'  => \yuanzhihai\response\think\support\Format::class,
        'config' => [
            // key => config
            'status'    => ['alias' => 'status', 'show' => true],
            'code'      => ['alias' => 'code', 'show' => true],
            'message'   => ['alias' => 'message', 'show' => true],
            'error'     => ['alias' => 'error', 'show' => true],
            'data.data' => ['alias' => 'data.data', 'show' => true], // rows/items/list
        ],
    ],
];