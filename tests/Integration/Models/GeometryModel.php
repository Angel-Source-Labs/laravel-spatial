<?php

use AngelSourceLabs\LaravelSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GeometryModel.
 *
 * @property int                                          id
 * @property \AngelSourceLabs\LaravelSpatial\Types\Point      location
 * @property \AngelSourceLabs\LaravelSpatial\Types\LineString line
 * @property \AngelSourceLabs\LaravelSpatial\Types\LineString shape
 */
class GeometryModel extends Model
{
    use SpatialTrait;

    protected $table = 'geometry';

    protected $spatialFields = ['location', 'line', 'multi_geometries'];
}
