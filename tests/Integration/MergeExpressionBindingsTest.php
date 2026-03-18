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
    
    public function test_it_reproduces_type_error_on_where_raw_with_string_bindings()
    {
        // Explicitly passing a string as bindings to whereRaw
        // PHP 8.3 should throw TypeError because mergeExpressionBindings(..., array $bindings)
        try {
            \Illuminate\Support\Facades\DB::table('test_models')->whereRaw('1 = 1', 'invalid-bindings');
        } catch (\TypeError $e) {
            $this->assertStringContainsString('must be of type array, string given', $e->getMessage());
            return;
        }
        
        $this->fail('TypeError was not thrown despite passing string as bindings to whereRaw');
    }
}
