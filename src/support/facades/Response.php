<?php

/*
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace yuanzhihai\response\think\support\Facades;

use think\Facade as ThinkFacade;

/**
 * @method static \think\Response accepted($data = null, string $message = '', string $location = '')
 * @method static \think\Response created($data = null, string $message = '', string $location = '')
 * @method static \think\Response noContent(string $message = '')
 * @method static \think\Response localize(int $code = 200, array $headers = [], array $options = [])
 * @method static \think\Response ok(string $message = '', int $code = 200, array $headers = [], array $options = [])
 * @method static \think\Response success($data = null, string $message = '', int $code = 200, array $headers = [], array $options = [])
 * @method static void errorBadRequest(?string $message = '')
 * @method static void errorForbidden(string $message = '')
 * @method static void errorNotFound(string $message = '')
 * @method static void errorMethodNotAllowed(string $message = '')
 * @method static void errorInternal(string $message = '')
 * @method static \think\Response fail(string $message = '', int $code = 500, $errors = null, array $headers = [], array $options = [])
 *
 * @see \yuanzhihai\response\think\Response
 */
class Response extends ThinkFacade
{
    protected static function getFacadeClass()
    {
        return \yuanzhihai\response\think\Response::class;
    }
}
