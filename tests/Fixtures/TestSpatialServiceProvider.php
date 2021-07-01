<?php


namespace Tests\Fixtures;


class TestSpatialServiceProvider extends \AngelSourceLabs\LaravelSpatial\SpatialServiceProvider
{
    public function registerGeometryTypes($connection)
    {
        /* do nothing */
    }
}