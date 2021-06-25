<?php

use AngelSourceLabs\LaravelSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WithSridModel.
 *
 * @property int                                          id
 * @property \AngelSourceLabs\LaravelSpatial\Types\Point      location
 * @property \AngelSourceLabs\LaravelSpatial\Types\LineString line
 * @property \AngelSourceLabs\LaravelSpatial\Types\LineString shape
 */
class WithSridModel extends Model
{
    use SpatialTrait;

    protected $table = 'with_srid';

    protected $spatialFields = ['location', 'line'];

    public $timestamps = false;
}
