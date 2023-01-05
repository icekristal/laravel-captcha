<?php

namespace Icekristal\LaravelCaptcha\Services;

use Illuminate\Support\Facades\Crypt;
use Intervention\Image\Facades\Image;

class IceCaptchaService
{

    public string $colorBackground = "#FFFFFF";

    public array $listColors = ["#FF0000", "#000000", "#0008FF"];

    public int $canvasWidth = 360;
    public int $canvasHeight = 120;

    public mixed $captcha_text;

    public mixed $readyCaptcha;
    public mixed $sizeText = 30;

    private array $levelsSymbols = [
        0 => "1",
        1 => "1234567890",
        2 => "abcdefghigklmnpoqrstuvwsyz",
        3 => "ABCDEFGHIGKLMNPOQRSTUVWSYZ",
        4 => "!@#$%",
    ];

    public int $length = 4;
    public int $level = 2;

    public function __construct()
    {
        $this->colorBackground = config('captcha.colors.background') ?? $this->colorBackground ?? "#FFFFFF";
        $this->canvasWidth = config('captcha.size.canvas_width') ?? $this->canvasWidth;
        $this->canvasHeight = config('captcha.size.canvas_height') ?? $this->canvasHeight;
        $this->listColors = config('captcha.colors.list') ?? $this->listColors ?? ["#000000"];
        $this->level = config('captcha.default_length') ?? 2;
        $this->length = config('captcha.default_length') ?? 2;
        $this->sizeText = config('captcha.size.text')  ?? ceil($this->canvasWidth / 5) ?? 30;
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
        return Crypt::encryptString($this->captcha_text."&&".now()->timestamp);
    }

    public function generate(): static
    {
        $this->captcha_text = $this->generateText();
        $textArray = str_split($this->captcha_text);
        $rateLines = config('captcha.rate_lines_x') ?? 4;
        if(!is_numeric($rateLines)) $rateLines = 4;

        $image = Image::canvas($this->canvasWidth, $this->canvasHeight, $this->colorBackground);
        for ($i = 0; $i <= $this->level * $rateLines; $i++) {
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
        $widthOneSymbol = floor(($this->canvasWidth - $this->sizeText) / $countSymbols);
        $iterSymbol = 1;

        $beginX = 1;
        $endX = $widthOneSymbol;

        $y = ceil($this->canvasHeight * 0.66);
        foreach ($textArray as $symbol) {
            if($iterSymbol == 1) {
                $x = rand($beginX, floor($endX * $iterSymbol));
            }else{
                $x = rand($endX * ($iterSymbol - 1), $endX * $iterSymbol);
            }

            $image->text($symbol, $x, $y, function ($font) {
                $font->file(config('captcha.url_font', null));
                $font->size($this->sizeText);
                $font->angle(rand(-10, 10));
                $font->color($this->listColors[array_rand($this->listColors)]);
            });
            $image->text($symbol, $x + rand(-2, 2), $y + rand(-2, 2), function ($font) {
                $font->file(config('captcha.url_font', null));
                $font->size($this->sizeText);
                $font->angle(rand(-5, 5));
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

    /**
     * @param mixed $sizeText
     * @return IceCaptchaService
     */
    public function setSizeText(mixed $sizeText): IceCaptchaService
    {
        $this->sizeText = $sizeText;
        return $this;
    }
}
