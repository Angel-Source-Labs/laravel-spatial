<?php

namespace Tests\Integration\Models;

use AngelSourceLabs\LaravelSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NoSpatialFieldsModel.
 *
 * @property \AngelSourceLabs\LaravelSpatial\Types\Geometry geometry
 */
class NoSpatialFieldsModel extends Model
{
    use SpatialTrait;

    protected $table = 'no_spatial_fields';

    public $timestamps = false;
}
