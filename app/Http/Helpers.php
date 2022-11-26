<?php

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

function locale()
{
    return Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
}

function rtl_assets()
{
    if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') {
        return '-rtl';
    }
    return '';
}

function locales()
{
    $arr = [];
    foreach (LaravelLocalization::getSupportedLocales() as $key => $value) {
        $arr[$key] = __('' . $value['name']);
    }
    return $arr;
}

function languages()
{
    if (app()->getLocale() == 'en') {
        return ['ar' => 'arabic', 'en' => 'english'];
    } else {
        return ['ar' => 'العربية', 'en' => 'النجليزية'];

    }
}
