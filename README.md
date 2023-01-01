install:
```php
composer require icekristal/laravel-captcha
```

config:
```php
php artisan vendor:publish --provider="Icekristal\LaravelCaptcha\CaptchaServiceProvider" --tag="config"
```

use:

get all info. Return array keys: image_no_encode, image, secret_key
```php
$arrayInfoCaptcha = \Icekristal\LaravelCaptcha\Facades\IceCaptcha::generateAndGetAllInfo();
```

set settings
```php
$image = \Icekristal\LaravelCaptcha\Facades\IceCaptcha::setLength(4)->setLevel(2)->generateAndGetAllInfo();
```

all info settings:
```php
setColorBackground('#000000');
setListColors(['#FFFFFF', '#0000FF', '#FF0000']);
```

get only no encode image
```php
$captcha = \Icekristal\LaravelCaptcha\Facades\IceCaptcha::generate()->getImageNoEncode();
```
