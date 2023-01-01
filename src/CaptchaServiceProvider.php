<?php

namespace Icekristal\LaravelCaptcha;

use Icekristal\LaravelCaptcha\Services\IceCaptchaService;
use Illuminate\Support\ServiceProvider;

class CaptchaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('ice.captcha', IceCaptchaService::class);
        $this->registerConfig();
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/captcha.php', 'captcha');
    }

    protected function publishConfigs(): void
    {
        $this->publishes([
            __DIR__ . '/../config/captcha.php' => config_path('captcha.php'),
        ], 'config');
    }
}
