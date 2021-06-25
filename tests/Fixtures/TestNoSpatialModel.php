<?php


namespace Tests\Fixtures;


use Illuminate\Database\Eloquent\Model;

class TestNoSpatialModel extends Model
{
    use \AngelSourceLabs\LaravelSpatial\Eloquent\SpatialTrait;
}
