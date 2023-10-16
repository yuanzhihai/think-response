<h1 align="center"> think-response </h1>

> 为 thinkphp API 项目提供一个规范统一的响应数据格式。

## 介绍

`think-response` 主要用来统一 API 开发过程中「成功」、「失败」以及「异常」情况下的响应数据格式。

遵循一定的规范，返回易于理解的 HTTP 状态码，并支持定义 `ResponseCodeEnum` 来满足不同场景下返回描述性的业务操作码。

## 概览

- 统一的数据响应格式，固定包含：`code`、`status`、`data`、`message`、`error` (响应格式设计源于：[RESTful服务最佳实践](https://www.cnblogs.com/jaxu/p/7908111.html#a_8_2) )
- 你可以继续链式调用 `Response` 类中的所有 public 方法，比如 `Response::success()->header('X-foo','bar');`
- 合理地返回 Http 状态码，默认为 restful 严格模式，可以配置异常时返回 200 http 状态码（多数项目会这样使用）
- 根据 debug 开关，合理返回异常信息、验证异常信息等
- 支持修改 Thinkphp 特地异常的状态码或提示信息，比如将 `No query results for model` 的异常提示修改成 `数据未找到`
- 支持配置返回字段是否显示，以及为她们设置别名，比如，将 `message` 别名设置为 `msg`，或者 分页数据第二层的 `data` 改成 `list`(res.data.data -> res.data.list)
- 内置 Http 标准状态码支持，同时支持扩展 ResponseCodeEnum 来根据不同业务模块定义响应码(可选，需要安装 `yuanzhihai/think-enum`)
- 响应码 code 对应描述信息 message 支持本地化，支持配置多语言(可选，需要安装 `yuanzhihai/think-enum`)


## 安装


```shell

composer require yuanzhihai/think-response "^1.0" 
composer require yuanzhihai/think-enum "^1.0"  # 可选
```

## 使用

### 成功响应

#### 示例代码

```php
<?php
public function index()
{
    $users = User::select();

    return Response::success($users);
}

public function paginate()
{
    $users = User::paginate(5);

    return Response::success($users);
}


public function item()
{
    $user = User::find();

    return Response::success($user);
}

public function array()
{
    return Response::success([
        'name' => 'yuanzhihai',
        'email' => 'yuanzhihai@foxmail.com'
    ],'', ResponseCodeEnum::SERVICE_REGISTER_SUCCESS);
}
```

#### 返回全部数据

支持自定义内层 data 字段名称，比如 rows、list

```json
{
    "status": "success",
    "code": 200,
    "message": "操作成功",
    "data": {
        "data": [
            {
                "nickname": "Joaquin Ondricka",
                "email": "lowe.chaim@example.org"
            },
            {
                "nickname": "Jermain D'Amore",
                "email": "reanna.marks@example.com"
            },
            {
                "nickname": "Erich Moore",
                "email": "ernestine.koch@example.org"
            }
        ]
    },
    "error": {}
}
```

#### 分页数据

支持自定义内层 data 字段名称，比如 rows、list

```json
{
    "status": "success",
    "code": 200,
    "message": "操作成功",
    "data": {
        "data": [
            {
                "nickname": "Joaquin Ondricka",
                "email": "lowe.chaim@example.org"
            },
            {
                "nickname": "Jermain D'Amore",
                "email": "reanna.marks@example.com"
            },
            {
                "nickname": "Erich Moore",
                "email": "ernestine.koch@example.org"
            },
            {
                "nickname": "Eva Quitzon",
                "email": "rgottlieb@example.net"
            },
            {
                "nickname": "Miss Gail Mitchell",
                "email": "kassandra.lueilwitz@example.net"
            }
        ],
        "meta": {
            "pagination": {
                "count": 5,
                "per_page": 5,
                "current_page": 1,
                "total": 12,
            }
        }
    },
    "error": {}
}
```

#### 返回单条数据

```json
{
    "status": "success",
    "code": 200,
    "message": "操作成功",
    "data": {
        "nickname": "Joaquin Ondricka",
        "email": "lowe.chaim@example.org"
    },
    "error": {}
}
```

#### 其他快捷方法

```php
Response::ok();// 无需返回 data，只返回 message 情形的快捷方法
Response::localize(200101);// 无需返回 data，message 根据响应码配置返回的快捷方法
Response::accepted();
Response::created();
Response::noContent();
```

### 失败响应

#### 不指定 message

```php
public function fail()
{
   return Response::fail();
}
```

- 未配置多语言响应描述

```json
{
    "status": "fail",
    "code": 500,
    "message": "Http internal server error",
    "data": {},
    "error": {}
}
```

- 配置多语言描述

```json
{
    "status": "fail",
    "code": 500,
    "message": "操作失败",
    "data": {},
    "error": {}
}
```

#### 指定 message

```php
public function fail()
{
   Response Response::fail('error');
}
```

返回数据

```json
{
    "status": "fail",
    "code": 500,
    "message": "error",
    "data": {},
    "error": {}
}
```

#### 指定 code

```php
public function fail()
{
   Response Response::fail('',ResponseCodeEnum::SERVICE_LOGIN_ERROR);
}
```

返回数据

```json
{
    "status": "fail",
    "code": 500102,
    "message": "登录失败",
    "data": {},
    "error": {}
}
```

#### 其他快捷方法

```php
Response::errorBadRequest();
Response::errorUnauthorized();
Response::errorForbidden();
Response::errorNotFound();
Response::errorMethodNotAllowed();
Response::errorInternal();
```

### 异常响应

#### 表单验证异常

```json
{
    "status": "error",
    "code": 422,
    "message": "验证失败",
    "data": {},
    "error": {
        "email": [
            "The email field is required."
        ]
    }
}
```


### 自定义业务码

```php
<?php
namespace app\enums;

use yuanzhihai\enum\think\Enums\HttpStatusCodeEnum;

class ResponseCodeEnum extends HttpStatusCodeEnum
{
    // 业务操作正确码：1xx、2xx、3xx 开头，后拼接 3 位
    // 200 + 001 => 200001，也就是有 001 ~ 999 个编号可以用来表示业务成功的情况，当然你可以根据实际需求继续增加位数，但必须要求是 200 开头
    // 举个栗子：你可以定义 001 ~ 099 表示系统状态；100 ~ 199 表示授权业务；200 ~ 299 表示用户业务。..
    const SERVICE_REGISTER_SUCCESS = 200101;
    const SERVICE_LOGIN_SUCCESS = 200102;

    // 客户端错误码：400 ~ 499 开头，后拼接 3 位
    const CLIENT_PARAMETER_ERROR = 400001;
    const CLIENT_CREATED_ERROR = 400002;
    const CLIENT_DELETED_ERROR = 400003;

    const CLIENT_VALIDATION_ERROR = 422001; // 表单验证错误

    // 服务端操作错误码：500 ~ 599 开头，后拼接 3 位
    const SYSTEM_ERROR = 500001;
    const SYSTEM_UNAVAILABLE = 500002;
    const SYSTEM_CACHE_CONFIG_ERROR = 500003;
    const SYSTEM_CACHE_MISSED_ERROR = 500004;
    const SYSTEM_CONFIG_ERROR = 500005;

    // 业务操作错误码（外部服务或内部服务调用。..）
    const SERVICE_REGISTER_ERROR = 500101;
    const SERVICE_LOGIN_ERROR = 500102;
}
```

### 本地化业务码描述

```php
<?php
// app/lang/zh-cn/enums.php
use app\enums\ResponseCodeEnum;

return [
    // 响应状态码
    ResponseCodeEnum::class => [
        // 成功
        ResponseCodeEnum::HTTP_OK => '操作成功', // 自定义 HTTP 状态码返回消息
        ResponseCodeEnum::HTTP_INTERNAL_SERVER_ERROR => '操作失败', // 自定义 HTTP 状态码返回消息
        ResponseCodeEnum::HTTP_UNAUTHORIZED => '授权失败',

        // 业务操作成功
        ResponseCodeEnum::SERVICE_REGISTER_SUCCESS => '注册成功',
        ResponseCodeEnum::SERVICE_LOGIN_SUCCESS => '登录成功',

        // 客户端错误
        ResponseCodeEnum::CLIENT_PARAMETER_ERROR => '参数错误',
        ResponseCodeEnum::CLIENT_CREATED_ERROR => '数据已存在',
        ResponseCodeEnum::CLIENT_DELETED_ERROR => '数据不存在',
        ResponseCodeEnum::CLIENT_VALIDATION_ERROR => '表单验证错误',

        // 服务端错误
        ResponseCodeEnum::SYSTEM_ERROR => '服务器错误',
        ResponseCodeEnum::SYSTEM_UNAVAILABLE => '服务器正在维护，暂不可用',
        ResponseCodeEnum::SYSTEM_CACHE_CONFIG_ERROR => '缓存配置错误',
        ResponseCodeEnum::SYSTEM_CACHE_MISSED_ERROR => '缓存未命中',
        ResponseCodeEnum::SYSTEM_CONFIG_ERROR => '系统配置错误',

        // 业务操作失败：授权业务
        ResponseCodeEnum::SERVICE_REGISTER_ERROR => '注册失败',
        ResponseCodeEnum::SERVICE_LOGIN_ERROR => '登录失败',
    ],
];
```

## 协议

MIT 许可证（MIT）。有关更多信息，请参见[协议文件](LICENSE)。