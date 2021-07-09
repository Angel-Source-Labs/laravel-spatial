<?php

namespace Tests\Integration;

use AngelSourceLabs\LaravelSpatial\Types\GeometryCollection;
use AngelSourceLabs\LaravelSpatial\Types\LineString;
use AngelSourceLabs\LaravelSpatial\Types\MultiPoint;
use AngelSourceLabs\LaravelSpatial\Types\MultiPolygon;
use AngelSourceLabs\LaravelSpatial\Types\Point;
use AngelSourceLabs\LaravelSpatial\Types\Polygon;
use Illuminate\Database\QueryException;
use Tests\Integration\Models\WithSridModel;

abstract class SridSpatialTest extends IntegrationBaseTestCase
{
//    protected $migrations = [
//        CreateLocationTable::class,
//        UpdateLocationTable::class,
//    ];

    public function testInsertPointWithSrid()
    {
        $geo = new WithSridModel();
        $geo->location = new Point(1, 2, 3857);
        $geo->save();
        $this->assertDatabaseHas('with_srid', ['id' => $geo->id]);
    }

    public function testInsertLineStringWithSrid()
    {
        $geo = new WithSridModel();

        $geo->location = new Point(1, 2, 3857);
        $geo->line = new LineString([new Point(1, 1), new Point(2, 2)], 3857);
        $geo->save();
        $this->assertDatabaseHas('with_srid', ['id' => $geo->id]);
    }

    public function testInsertPolygonWithSrid()
    {
        $geo = new WithSridModel();

        $geo->location = new Point(1, 2, 3857);
        $geo->shape = Polygon::fromWKT('POLYGON((0 10,10 10,10 0,0 0,0 10))', 3857);
        $geo->save();
        $this->assertDatabaseHas('with_srid', ['id' => $geo->id]);
    }

    public function testInsertMultiPointWithSrid()
    {
        $geo = new WithSridModel();

        $geo->location = new Point(1, 2, 3857);
        $geo->multi_locations = new MultiPoint([new Point(1, 1), new Point(2, 2)], 3857);
        $geo->save();
        $this->assertDatabaseHas('with_srid', ['id' => $geo->id]);
    }

    public function testInsertMultiPolygonWithSrid()
    {
        $geo = new WithSridModel();

        $geo->location = new Point(1, 2, 3857);

        $geo->multi_shapes = new MultiPolygon([
            Polygon::fromWKT('POLYGON((0 10,10 10,10 0,0 0,0 10))'),
            Polygon::fromWKT('POLYGON((0 0,0 5,5 5,5 0,0 0))'),
        ], 3857);
        $geo->save();
        $this->assertDatabaseHas('with_srid', ['id' => $geo->id]);
    }

    public function testInsertGeometryCollectionWithSrid()
    {
        $geo = new WithSridModel();

        $geo->location = new Point(1, 2, 3857);

        $geo->multi_geometries = new GeometryCollection([
            Polygon::fromWKT('POLYGON((0 10,10 10,10 0,0 0,0 10))'),
            Polygon::fromWKT('POLYGON((0 0,0 5,5 5,5 0,0 0))'),
            new Point(0, 0),
        ], 3857);
        $geo->save();
        $this->assertDatabaseHas('with_srid', ['id' => $geo->id]);
    }

    public function testUpdateWithSrid()
    {
        $geo = new WithSridModel();
        $geo->location = new Point(1, 2, 3857);
        $geo->save();

        $to_update = WithSridModel::all()->first();
        $to_update->location = new Point(2, 3, 3857);
        $to_update->save();

        $this->assertDatabaseHas('with_srid', ['id' => $to_update->id]);

        $all = WithSridModel::all();
        $this->assertCount(1, $all);

        $updated = $all->first();
        $this->assertInstanceOf(Point::class, $updated->location);
        $this->assertEquals(2, $updated->location->getLat());
        $this->assertEquals(3, $updated->location->getLng());
    }

    public function testInsertPointWithWrongSrid()
    {
        $geo = new WithSridModel();
        $geo->location = new Point(1, 2, 4326);

        $this->assertException(QueryException::class);
        $geo->save();

        // SQLSTATE[22023]: Invalid parameter value: 7 ERROR:  Geometry SRID (4326) does not match column SRID (3857) (SQL: insert into "with_srid" ("location") values (ST_GeomFromText(POINT(2 1), 4326)) returning "id")
    }

    public function testGeometryInsertedHasRightSrid()
    {
        $geo = new WithSridModel();
        $geo->location = new Point(1, 2, 3857);
        $geo->save();

        $srid = \DB::selectOne('select ST_SRID(location) as srid from with_srid');
        $this->assertEquals(3857, $srid->srid);

        $result = WithSridModel::first();

        $this->assertEquals($geo->location->getSrid(), $result->location->getSrid());
        $a = 1;
    }
}
