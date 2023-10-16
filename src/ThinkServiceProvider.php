<?php

namespace yuanzhihai\response\think;

use think\Service;

class ThinkServiceProvider extends Service
{
    public function register()
    {
    }

    public function boot()
    {
        $formatter = $this->app->config->get('response.format.class', \yuanzhihai\response\think\support\Format::class);
        $config    = $this->app->config->get('response.format.config', []);

        if (is_string($formatter) && class_exists($formatter)) {
            $this->app->bind(\yuanzhihai\response\think\contracts\Format::class, function () use ($formatter, $config) {
                return new $formatter($config);
            });
        }
    }

}