<?php

return [
    'containers' => [
        'namespace' => 'App',
    ],

    /*
    |--------------------------------------------------------------------------
    | Generator Config
    |--------------------------------------------------------------------------
    |
    */
    'container'  => [
        'action'     => "Actions",
        'config'     => 'Configs',
        'provider'   => 'Providers',
        'task'       => 'Tasks',
        'model'      => 'Models',
        'data'       => 'Data',
        'repository' => 'Data/Repositories',
        'migration'  => 'Data/Migrations',
        'seeder'     => 'Data/Seeders',
        'factory'    => 'Data/Factories',
        'console'    => 'UI/CLI/Commands',
        'route'      => 'UI/{ui}/Routes'
    ],

    /*
    |--------------------------------------------------------------------------
    | API Config
    |--------------------------------------------------------------------------
    |
    */
    'api'        => [
        /*
        |--------------------------------------------------------------------------
        | API URL
        |--------------------------------------------------------------------------
        */
        'url'                    => env('API_URL', 'http://localhost'),

        /*
        |--------------------------------------------------------------------------
        | API Prefix
        |--------------------------------------------------------------------------
        */
        'prefix'                 => env('API_PREFIX', '/'),
        /*
        |--------------------------------------------------------------------------
        | API version
        |--------------------------------------------------------------------------
        |
        | 是否开启api版本管理
        |
        */
        'enable_version_prefix'  => true,

        /*
        |--------------------------------------------------------------------------
        | Access Token Expiration
        |--------------------------------------------------------------------------
        |
        | 单位分钟，默认为1440分钟(1天)
        |
        */
        'expires-in'             => env('API_TOKEN_EXPIRES', 1440),

        /*
        |--------------------------------------------------------------------------
        | Refresh Token Expiration
        |--------------------------------------------------------------------------
        |
        | 单位分钟. 默认为43,200 分钟（ 30 天 ）
        |
        */
        'refresh-expires-in'     => env('API_REFRESH_TOKEN_EXPIRES', 43200),

        /*
        |--------------------------------------------------------------------------
        | Enable Disable API Debugging
        |--------------------------------------------------------------------------
        |
        | 如果启用，错误异常跟踪将被注入到 JSON 响应中，并记录在默认日志文件中。
        |
        */
        'debug'                  => env('API_DEBUG', true),

        /*
        |--------------------------------------------------------------------------
        | 启用/禁用 隐式授权
        |--------------------------------------------------------------------------
        */
        'enabled-implicit-grant' => env('API_ENABLE_IMPLICIT_GRANT', true),

        /*
        |--------------------------------------------------------------------------
        | Rate Limit (throttle)
        |--------------------------------------------------------------------------
        | 每分钟尝试次数。
        |
        | `attempts` 次数
        | `expires` 间隔，单位分钟
        |
        */
        'throttle'               => [
            'enabled'  => env('API_RATE_LIMIT_ENABLED', true),
            'attempts' => env('API_RATE_LIMIT_ATTEMPTS', '30'),
            'expires'  => env('API_RATE_LIMIT_EXPIRES', '1'),
        ]
    ],

    'requests' => [
        /*
       |--------------------------------------------------------------------------
       | 允许 Role 访问所有路由
       |--------------------------------------------------------------------------
       |
       | 定义不需要通过"hasAccess"检查请求的角色列表。这些角色自动通过此检查。
       | 如果您想让管理员用户可以访问所有路由，这很有用。
       |
       | 例子: ['admin', 'editor']
       | 默认: []
       |
       */
        'allow-roles-to-access-all-routes'     => ['admin'],
        /*
        |--------------------------------------------------------------------------
        | Force Request Header to Contain header
        |--------------------------------------------------------------------------
        |
        | 默认情况下，用户可以发送请求而不定义接受头和将其设置为[accept=application/json]
        | 若要强制用户定义该头，请将其设置为true。
        |
        */
        'force-accept-header'                  => false,
        /*
        |--------------------------------------------------------------------------
        | Force Valid Request Include Parameters
        |--------------------------------------------------------------------------
        |
        |
        | 是否强制验证request中include参数的有效性
        |
        */
        'force-valid-includes'                 => true,
        /*
        |--------------------------------------------------------------------------
        | Use ETags
        |--------------------------------------------------------------------------
        |
        | This option appends an "ETag" HTTP Header to the Response. This ETag is a
        | calculated hash of the content to be delivered.
        | Clients can add an "If-None-Match" HTTP Header to the Request and submit
        | an (old) ETag. These ETags are validated. If they match (are the same),
        | an empty BODY with HTTP STATUS 304 (not modified) is returned!
        |
        */
        'use-etag'                             => true,
        /*
        |--------------------------------------------------------------------------
        | Automatically Apply RequestCriteria
        |--------------------------------------------------------------------------
        |
        | 对所有API请求都自动应用`RequestCriteria`请求条件
        | 如果一个请求中使用了多个Repository，可能会导致异常，因为`RequestCriteria`适用所有的Repository
        |
        */
        'automatically-apply-request-criteria' => env('API_REQUEST_APPLY_REQUEST-CRITERIA', true)
    ],

    'logging' => [
        /*
        |--------------------------------------------------------------------------
        | Log Apiato Wrong Caller Style
        |--------------------------------------------------------------------------
        |
        | 记录Porto::call调用失败的日志，首选调用方式为Porto::call("ContainerName@Action/Task")
        |
        */
        'log-wrong-api-caller-style' => true
    ]


];