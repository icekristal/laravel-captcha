<?php

namespace Icekristal\LaravelCaptcha\Services;

use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Vinkla\Hashids\Facades\Hashids;

class IceCaptchaService
{

    private string $colorBackground = "#FFFFFF";

    private array $listColors = ["#FF0000", "#000000", "#0008FF"];

    private int $canvasWidth = 360;
    private int $canvasHeight = 120;

    private mixed $image_encode;
    private mixed $image_no_encode;
    private mixed $secret_key;

    public mixed $captcha_text;

    private mixed $readyCaptcha;

    private array $levelsSymbols = [
        0 => "1",
        1 => "1234567890",
        2 => "abcdefghigklmnpoqrstuvwsyz",
        3 => "ABCDEFGHIGKLMNPOQRSTUVWSYZ",
        4 => "!@#$%",
    ];

    private int $length = 4;
    private int $level = 2;

    public function __construct()
    {
        $this->colorBackground = config('captcha.colors.background') ?? $this->colorBackground ?? "#FFFFFF";
        $this->canvasWidth = config('captcha.size.canvas_width') ?? $this->canvasWidth;
        $this->canvasHeight = config('captcha.size.canvas_height') ?? $this->canvasHeight;
        $this->listColors = config('captcha.colors.list') ?? $this->listColors ?? ["#000000"];
    }

    public function generateAndGetAllInfo(): array
    {
        $this->generate();
        return [
            'image_no_encode' => $this->getImageNoEncode(),
            'image' => $this->getImageEncode(),
            'secret_key' => $this->getSecretKey(),
        ];
    }

    public function getImageEncode(): \Intervention\Image\Image
    {
        return $this->readyCaptcha->encode('data-url');
    }

    public function getImageNoEncode(): \Intervention\Image\Image
    {
        return $this->readyCaptcha;
    }

    public function getSecretKey(): string
    {
        return Hashids::encode($this->captcha_text, now()->timestamp);
    }

    public function generate(): static
    {
        $this->captcha_text = $this->generateText();
        $textArray = str_split($this->captcha_text);

        $image = Image::canvas($this->canvasWidth, $this->canvasHeight, $this->colorBackground);
        for ($i = 0; $i <= $this->level * 4; $i++) {
            $image->line(
                rand(0, $this->canvasWidth) + 1,
                rand(1, $this->canvasHeight) - 1,
                rand(1, $this->canvasWidth) - 1,
                rand(0, $this->canvasHeight) + 1,
                function ($draw) {
                    $draw->color($this->listColors[array_rand($this->listColors)]);
                }
            );
        }

        $countSymbols = count($textArray);
        $widthOneSymbol = ceil($this->canvasWidth / $countSymbols);
        $iterSymbol = 1;

        $beginX = 1;
        $endX = $widthOneSymbol;

        $y = ceil($this->canvasHeight * 0.66);

        foreach ($textArray as $symbol) {
            $x = rand($beginX * $iterSymbol, $endX * $iterSymbol);
            $size = ceil($widthOneSymbol / 6);
            $image->text($symbol, $x, $y, function ($font) use ($widthOneSymbol, $size) {
                $font->size($size);
                $font->angle(rand(-10, 10));
                $font->color($this->listColors[array_rand($this->listColors)]);
            });
            $image->text($symbol, $x + 5, $y, function ($font) use ($widthOneSymbol, $size) {
                $font->size($size);
                $font->angle(rand(-10, 10));
                $font->color($this->listColors[array_rand($this->listColors)]);
            });
            $iterSymbol++;
        }
        $this->readyCaptcha = $image;
        return $this;
    }


    private function generateText(): string
    {
        if ($this->level == 0) {
            $accessLevelSymbol = $this->levelsSymbols[$this->level];
            return substr(str_shuffle($accessLevelSymbol), 0, $this->length);
        }

        $returnText = "";
        $levelNow = $this->level;
        $lengthNow = $this->length;

        while ($lengthNow > 0) {
            $returnText .= substr(str_shuffle($this->levelsSymbols[$levelNow]), 0, 1);
            $levelNow--;
            $lengthNow--;
            if ($levelNow < 1) {
                $levelNow = $this->level;
            }
        }
        return str_shuffle($returnText);
    }


    /**
     * @param int $length
     * @return IceCaptchaService
     */
    public function setLength(int $length): IceCaptchaService
    {
        if ($length < 2) $length = 4;
        if ($length > 10) $length = 10;
        $this->length = $length;
        return $this;
    }

    /**
     * @param int $level
     * @return IceCaptchaService
     */
    public function setLevel(int $level): IceCaptchaService
    {
        if ($level < 0) $level = 1;
        if ($level > 4) $level = 4;
        $this->level = $level;
        return $this;
    }

    /**
     * @param int $canvasHeight
     * @return IceCaptchaService
     */
    public function setCanvasHeight(int $canvasHeight): IceCaptchaService
    {
        $this->canvasHeight = $canvasHeight < 80 ? 120 : $canvasHeight;
        return $this;
    }

    /**
     * @param int $canvasWidth
     * @return IceCaptchaService
     */
    public function setCanvasWidth(int $canvasWidth): IceCaptchaService
    {
        $this->canvasWidth = $canvasWidth < 100 ? 320 : $canvasWidth;
        return $this;
    }

    /**
     * @param string $colorBackground
     * @return IceCaptchaService
     */
    public function setColorBackground(string $colorBackground): IceCaptchaService
    {
        $this->colorBackground = $colorBackground;
        return $this;
    }

    /**
     * @param array $listColors
     * @return IceCaptchaService
     */
    public function setListColors(array $listColors): IceCaptchaService
    {
        $this->listColors = $listColors;
        return $this;
    }


}