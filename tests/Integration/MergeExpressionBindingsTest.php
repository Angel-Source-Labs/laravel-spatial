<?php

namespace Tests\Integration;

use AngelSourceLabs\LaravelSpatial\Types\Point;
use Tests\Fixtures\TestModel;
use Illuminate\Support\Facades\Schema;
use AngelSourceLabs\LaravelSpatial\Schema\SpatialBlueprint;

class MergeExpressionBindingsTest extends IntegrationBaseTestCase
{
    public function getEnvironmentSetUp($app)
    {
        $this->usePostgresConnection($app);
    }

    public function setUp(): void
    {
        parent::setUp();
        
        Schema::create('test_models', function (SpatialBlueprint $table) {
            $table->increments('id');
            $table->point('point');
            $table->timestamps();
        });
    }

    protected function isMySQL8AfterFix()
    {
        return true;
    }

    public function test_it_can_save_a_model_with_a_point_without_type_error()
    {
        $model = new TestModel();
        $model->point = new Point(1.23, 4.56);
        
        // This should not throw TypeError: AngelSourceLabs\LaravelExpressions\Database\Query\Builder::mergeExpressionBindings(): Argument #2 ($bindings) must be of type array, string given
        $model->save();
        
        $this->assertDatabaseHas('test_models', [
            'id' => $model->id
        ]);
    }
    
    public function test_it_can_handle_string_bindings_on_where_raw()
    {
        // Explicitly passing a string as bindings to whereRaw
        // This should no longer throw TypeError because mergeExpressionBindings now allows strings
        $query = \Illuminate\Support\Facades\DB::table('test_models')->whereRaw('1 = 1', 'string-bindings');
        
        $this->assertNotNull($query);
    }
}
