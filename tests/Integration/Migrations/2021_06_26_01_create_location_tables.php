<?php

use AngelSourceLabs\LaravelSpatial\Schema\SpatialBlueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateLocationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geometry_test', function (SpatialBlueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->increments('id');
            $table->geometry('geo')->default(null)->nullable();
            $table->point('location');  // required to be not null in order to add an index
            $table->lineString('line')->default(null)->nullable();
            $table->polygon('shape')->default(null)->nullable();
            $table->multiPoint('multi_locations')->default(null)->nullable();
            $table->multiLineString('multi_lines')->default(null)->nullable();
            $table->multiPolygon('multi_shapes')->default(null)->nullable();
            $table->geometryCollection('multi_geometries')->default(null)->nullable();
            $table->timestamps();
        });

        Schema::create('no_spatial_fields', function (SpatialBlueprint $table) {
            $table->increments('id');
            $table->geometry('geometry')->default(null)->nullable();
        });

        Schema::create('with_srid', function (SpatialBlueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->increments('id');
            $table->geometry('geo', 3857)->default(null)->nullable();
            $table->point('location', 3857)->default(null)->nullable();
            $table->lineString('line', 3857)->default(null)->nullable();
            $table->polygon('shape', 3857)->default(null)->nullable();
            $table->multiPoint('multi_locations', 3857)->default(null)->nullable();
            $table->multiLineString('multi_lines', 3857)->default(null)->nullable();
            $table->multiPolygon('multi_shapes', 3857)->default(null)->nullable();
            $table->geometryCollection('multi_geometries', 3857)->default(null)->nullable();
        });

        Schema::create('with_geography', function (SpatialBlueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->increments('id');
            $table->geometry('geo', 4326, SpatialBlueprint::GEOGRAPHY)->default(null)->nullable();
            $table->point('location', 4326, SpatialBlueprint::GEOGRAPHY)->default(null)->nullable();
            $table->lineString('line', 4326, SpatialBlueprint::GEOGRAPHY)->default(null)->nullable();
            $table->polygon('shape', 4326, SpatialBlueprint::GEOGRAPHY)->default(null)->nullable();
            $table->multiPoint('multi_locations', 4326, SpatialBlueprint::GEOGRAPHY)->default(null)->nullable();
            $table->multiLineString('multi_lines', 4326, SpatialBlueprint::GEOGRAPHY)->default(null)->nullable();
            $table->multiPolygon('multi_shapes', 4326, SpatialBlueprint::GEOGRAPHY)->default(null)->nullable();
            $table->geometryCollection('multi_geometries', 4326, SpatialBlueprint::GEOGRAPHY)->default(null)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('geometry_test');
        Schema::drop('no_spatial_fields');
        Schema::drop('with_srid');
        Schema::drop('with_geography');
    }
}
