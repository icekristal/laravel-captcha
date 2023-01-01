<?php

namespace Icekristal\LaravelCaptcha\Facades;
use Icekristal\LaravelCaptcha\Services\IceCaptchaService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static IceCaptchaService generateAndGetAllInfo()
 * @method static IceCaptchaService generate()
 * @method static IceCaptchaService getImageEncode()
 * @method static IceCaptchaService getImageNoEncode()
 * @method static IceCaptchaService getSecretKey()
 * @method static IceCaptchaService setLength(int $length)
 * @method static IceCaptchaService setLevel(int $level)
 * @method static IceCaptchaService setCanvasHeight(int $canvasHeight)
 * @method static IceCaptchaService setCanvasWidth(int $canvasWidth)
 * @method static IceCaptchaService setColorBackground(string $colorBackground)
 * @method static IceCaptchaService setListColors(array $listColors)
 */
class IceCaptcha extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ice.captcha';
    }
}
