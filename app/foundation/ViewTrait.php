<?php

namespace App\Foundation;

trait ViewTrait
{
    private array $rootApps = [
        'page.render' => __DIR__ . '../../../view/pages/',
        'page.layout' => __DIR__ . '/../../view/layout/',
        'page.template' => __DIR__ . '/../../view/template/',
        'page.css' => __DIR__ . '/../../assets/css/',
        'page.js' => __DIR__ . '/../../assets/js/',
        'app.upload' => __DIR__ . '/../../upload/'
    ];
}
