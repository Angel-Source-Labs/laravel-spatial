<?php


namespace Tests\Fixtures;

use AngelSourceLabs\LaravelSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    use SpatialTrait;

    protected $spatialFields = ['point'];   // TODO: only required when fetching, not saving

    public $timestamps = false;

    public function testrelatedmodels()
    {
        return $this->hasMany(TestRelatedModel::class);
    }

    public function testrelatedmodels2()
    {
        return $this->belongsToMany(TestRelatedModel::class);
    }
}