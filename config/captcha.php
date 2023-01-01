<?php

return [
    'colors' => [
        'background' => "#000000",
        'list' => explode(',', env("CAPTCHA_COLORS_LIST", "#FFFFFF,#FF0000,#FFF000"))
    ],
    'size' => [
        'canvas_width' => 360,
        'canvas_height' => 120,
        'text' => null,
    ],
    'default_length' => 4,
    'default_level' => 2,
];
