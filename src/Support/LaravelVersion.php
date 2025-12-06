<?php

namespace AngelSourceLabs\LaravelSpatial\Support;


use Illuminate\Foundation\Application;

final class LaravelVersion
{
    public static function is12OrHigher(): bool
    {
        return version_compare(Application::VERSION, '12.0.0', '>=');
    }
}