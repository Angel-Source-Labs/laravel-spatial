<?php

namespace Tests\Unit\Eloquent;

use AngelSourceLabs\LaravelSpatial\Exceptions\SpatialFieldsNotDefinedException;
use AngelSourceLabs\LaravelSpatial\Exceptions\UnknownSpatialFunctionException;
use AngelSourceLabs\LaravelSpatial\Types\GeometryCollection;
use AngelSourceLabs\LaravelSpatial\Types\LineString;
use AngelSourceLabs\LaravelSpatial\Types\MultiLineString;
use AngelSourceLabs\LaravelSpatial\Types\MultiPoint;
use AngelSourceLabs\LaravelSpatial\Types\MultiPolygon;
use AngelSourceLabs\LaravelSpatial\Types\Point;
use AngelSourceLabs\LaravelSpatial\Types\Polygon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\Fixtures\TestModel;
use Tests\Fixtures\TestNoSpatialModel;
use Tests\Unit\BaseTestCase;

class SpatialTraitTest extends BaseTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var TestModel
     */
    protected $model;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->model = new TestModel();
    }

    public function tearDown(): void
    {
        $this->model->getConnection()->getPdo()->resetQueries();
    }

    public function testInsertUpdatePointHasCorrectSql()
    {
        $this->assertFalse($this->model->exists);

        $this->model->point = new Point(1, 2);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->pdo->queries[0]);
        $this->assertStringContainsString('insert into `test_models` (`point`) values (ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        // TODO: assert bindings in query
        $this->assertTrue($this->model->exists);

        $this->model->point = new Point(1, 2);
        $this->model->save();

        $this->assertStringStartsWith('update', $this->pdo->queries[1]);
        $this->assertStringContainsString('update `test_models` set `point` = ST_GeomFromText(?, ?, \'axis-order=long-lat\') where `id` = ?', $this->pdo->queries[1]);
        // TODO: assert bindings in query
    }

    public function testInsertUpdateLineStringHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);

        $this->assertFalse($this->model->exists);

        $this->model->linestring = new LineString([$point1, $point2]);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->pdo->queries[0]);
        $this->assertStringContainsString('insert into `test_models` (`linestring`) values (ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        // TODO: assert bindings in query
        $this->assertTrue($this->model->exists);

        $this->model->linestring = new LineString([$point1, $point2]);
        $this->model->save();

        $this->assertStringStartsWith('update', $this->pdo->queries[1]);
        $this->assertStringContainsString('update `test_models` set `linestring` = ST_GeomFromText(?, ?, \'axis-order=long-lat\') where `id` = ?', $this->pdo->queries[1]);
        // TODO: assert bindings in query
    }

    public function testInsertUpdatePolygonHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);
        $linestring1 = new LineString([$point1, $point2]);
        $point3 = new Point(3, 2);
        $point4 = new Point(2, 1);
        $linestring2 = new LineString([$point3, $point4]);

        $this->assertFalse($this->model->exists);

        $this->model->polygon = new Polygon([$linestring1, $linestring2]);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->pdo->queries[0]);
        $this->assertStringContainsString('insert into `test_models` (`polygon`) values (ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        // TODO: assert bindings in query
        $this->assertTrue($this->model->exists);

        $this->model->polygon = new Polygon([$linestring1, $linestring2]);
        $this->model->save();
        $this->assertStringStartsWith('update', $this->pdo->queries[1]);
        $this->assertStringContainsString('update `test_models` set `polygon` = ST_GeomFromText(?, ?, \'axis-order=long-lat\') where `id` = ?', $this->pdo->queries[1]);
        // TODO: assert bindings in query
    }

    public function testInsertUpdateMultiPointHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);

        $this->assertFalse($this->model->exists);

        $this->model->multipoint = new MultiPoint([$point1, $point2]);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->pdo->queries[0]);
        $this->assertStringContainsString('insert into `test_models` (`multipoint`) values (ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        // TODO: assert bindings in query
        $this->assertTrue($this->model->exists);

        $this->model->multipoint = new MultiPoint([$point1, $point2]);
        $this->model->save();

        $this->assertStringStartsWith('update', $this->pdo->queries[1]);
        $this->assertStringContainsString('update `test_models` set `multipoint` = ST_GeomFromText(?, ?, \'axis-order=long-lat\') where `id` = ?', $this->pdo->queries[1]);
        // TODO: assert bindings in query
    }

    public function testInsertUpdateMultiLineStringHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);
        $linestring1 = new LineString([$point1, $point2]);
        $point3 = new Point(3, 2);
        $point4 = new Point(2, 1);
        $linestring2 = new LineString([$point3, $point4]);

        $this->assertFalse($this->model->exists);

        $this->model->multilinestring = new MultiLineString([$linestring1, $linestring2]);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->pdo->queries[0]);
        $this->assertStringContainsString('insert into `test_models` (`multilinestring`) values (ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        // TODO: assert bindings in query
        $this->assertTrue($this->model->exists);

        $this->model->multilinestring = new MultiLineString([$linestring1, $linestring2]);
        $this->model->save();
        $this->assertStringStartsWith('update', $this->pdo->queries[1]);
        $this->assertStringContainsString('update `test_models` set `multilinestring` = ST_GeomFromText(?, ?, \'axis-order=long-lat\') where `id` = ?', $this->pdo->queries[1]);
        // TODO: assert bindings in query
    }

    public function testInsertUpdateMultiPolygonHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);
        $linestring1 = new LineString([$point1, $point2]);
        $point3 = new Point(3, 2);
        $point4 = new Point(2, 1);
        $linestring2 = new LineString([$point3, $point4]);
        $polygon1 = new Polygon([$linestring1, $linestring2]);

        $point5 = new Point(4, 5);
        $point6 = new Point(5, 6);
        $linestring3 = new LineString([$point5, $point6]);
        $point7 = new Point(6, 5);
        $point8 = new Point(5, 4);
        $linestring4 = new LineString([$point7, $point8]);
        $polygon2 = new Polygon([$linestring3, $linestring4]);

        $this->assertFalse($this->model->exists);

        $this->model->multipolygon = new MultiPolygon([$polygon1, $polygon2]);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->pdo->queries[0]);
        $this->assertStringContainsString('insert into `test_models` (`multipolygon`) values (ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        // TODO: assert bindings in query
        $this->assertTrue($this->model->exists);

        $this->model->multipolygon = new MultiPolygon([$polygon1, $polygon2]);
        $this->model->save();
        $this->assertStringStartsWith('update', $this->pdo->queries[1]);
        $this->assertStringContainsString('update `test_models` set `multipolygon` = ST_GeomFromText(?, ?, \'axis-order=long-lat\') where `id` = ?', $this->pdo->queries[1]);
        // TODO: assert bindings in query
    }

    public function testInsertUpdateGeometryCollectionHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);
        $point3 = new Point(3, 3);
        $linestring1 = new LineString([$point2, $point3]);

        $this->assertFalse($this->model->exists);

        $this->model->geometrycollection = new GeometryCollection([$point1, $linestring1]);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->pdo->queries[0]);
        $this->assertStringContainsString('insert into `test_models` (`geometrycollection`) values (ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        // TODO: assert bindings in query
        $this->assertTrue($this->model->exists);

        $this->model->geometrycollection = new GeometryCollection([$point1, $linestring1]);
        $this->model->save();
        $this->assertStringStartsWith('update', $this->pdo->queries[1]);
        $this->assertStringContainsString('update `test_models` set `geometrycollection` = ST_GeomFromText(?, ?, \'axis-order=long-lat\') where `id` = ?', $this->pdo->queries[1]);
        // TODO: assert bindings in query
    }

    public function testSettingRawAttributes()
    {
        $attributes['point'] = "\0\0\0\0".'0101000000000000000000f03f0000000000000040';

        $this->model->setRawAttributes($attributes);
        $this->assertInstanceOf(Point::class, ($this->model->point));
    }

    public function testSpatialFieldsNotDefinedException()
    {
        $model = new TestNoSpatialModel();
        $this->assertException(
            SpatialFieldsNotDefinedException::class,
            'TestNoSpatialModel has to define $spatialFields'
        );
        $model->getSpatialFields();
    }

    public function testScopeDistance()
    {
        $point = new Point(1, 2);
        $query = TestModel::distance('point', $point, 10);

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select * from `test_models` where st_distance(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) <= ?', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POINT(2 1)', 2 => 0, 3 => 10], $this->pdo->bindings[0]);
    }

    public function testScopeDistanceExcludingSelf()
    {
        $point = new Point(1, 2);
        $query = TestModel::distanceExcludingSelf('point', $point, 10);

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select * from `test_models` where st_distance(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) <= ? and st_distance(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) != 0', $this->pdo->queries[0]);
        $this->assertEquals([
            1 => 'POINT(2 1)',
            2 => 0,
            3 => 10,
            4 => 'POINT(2 1)',
            5 => 0
        ], $this->pdo->bindings[0]);
    }

    public function testScopeDistanceSphere()
    {
        $point = new Point(1, 2);
        $query = TestModel::distanceSphere('point', $point, 10);

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select * from `test_models` where st_distance_sphere(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) <= ?', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POINT(2 1)', 2 => 0, 3 => 10], $this->pdo->bindings[0]);
    }

    public function testScopeDistanceSphereExcludingSelf()
    {
        $point = new Point(1, 2);
        $query = TestModel::distanceSphereExcludingSelf('point', $point, 10);

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select * from `test_models` where st_distance_sphere(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) <= ? and st_distance_sphere(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) != 0', $this->pdo->queries[0]);
        $this->assertEquals([
            1 => 'POINT(2 1)',
            2 => 0,
            3 => 10,
            4 => 'POINT(2 1)',
            5 => 0
        ], $this->pdo->bindings[0]);
    }

    public function testScopeDistanceValue()
    {
        $point = new Point(1, 2);
        $query = TestModel::distanceValue('point', $point);

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select *, st_distance(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) as distance from `test_models`', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POINT(2 1)', 2 => 0], $this->pdo->bindings[0]);
    }

    public function testScopeDistanceValueWithSelect()
    {
        $point = new Point(1, 2);
        $query = TestModel::select('some_column')->distanceValue('point', $point);

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select `some_column`, st_distance(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) as distance from `test_models`', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POINT(2 1)', 2 => 0], $this->pdo->bindings[0]);
    }

    public function testScopeDistanceSphereValue()
    {
        $point = new Point(1, 2);
        $query = TestModel::distanceSphereValue('point', $point);

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();

        $this->assertEquals('select *, st_distance_sphere(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) as distance from `test_models`', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POINT(2 1)', 2 => 0], $this->pdo->bindings[0]);
    }

    public function testScopeDistanceSphereValueWithSelect()
    {
        $point = new Point(1, 2);
        $query = TestModel::select('some_column')->distanceSphereValue('point', $point);

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();

        $this->assertEquals('select `some_column`, st_distance_sphere(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) as distance from `test_models`', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POINT(2 1)', 2 => 0], $this->pdo->bindings[0]);
    }

    private function buildTestPolygon()
    {
        $point1 = new Point(1, 1);
        $point2 = new Point(1, 2);
        $linestring1 = new LineString([$point1, $point2]);
        $point3 = new Point(1, 2);
        $point4 = new Point(2, 2);
        $linestring2 = new LineString([$point3, $point4]);
        $point5 = new Point(2, 2);
        $point6 = new Point(1, 1);
        $linestring3 = new LineString([$point5, $point6]);

        return new Polygon([$linestring1, $linestring2, $linestring3]);
    }

    public function testScopeComparison()
    {
        $query = TestModel::comparison('point', $this->buildTestPolygon(), 'within');

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select * from `test_models` where st_within(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', 2 => 0], $this->pdo->bindings[0]);
    }

    public function testScopeWithin()
    {
        $query = TestModel::within('point', $this->buildTestPolygon());

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select * from `test_models` where st_within(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', 2 => 0], $this->pdo->bindings[0]);
    }

    public function testScopeCrosses()
    {
        $query = TestModel::crosses('point', $this->buildTestPolygon());

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select * from `test_models` where st_crosses(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', 2 => 0], $this->pdo->bindings[0]);
    }

    public function testScopeContains()
    {
        $query = TestModel::contains('point', $this->buildTestPolygon());

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select * from `test_models` where st_contains(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', 2 => 0], $this->pdo->bindings[0]);
    }

    public function testScopeDisjoint()
    {
        $query = TestModel::disjoint('point', $this->buildTestPolygon());

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select * from `test_models` where st_disjoint(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', 2 => 0], $this->pdo->bindings[0]);
    }

    public function testScopeEquals()
    {
        $query = TestModel::equals('point', $this->buildTestPolygon());

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select * from `test_models` where st_equals(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', 2 => 0], $this->pdo->bindings[0]);
    }

    public function testScopeIntersects()
    {
        $query = TestModel::intersects('point', $this->buildTestPolygon());

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select * from `test_models` where st_intersects(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', 2 => 0], $this->pdo->bindings[0]);
    }

    public function testScopeOverlaps()
    {
        $query = TestModel::overlaps('point', $this->buildTestPolygon());

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select * from `test_models` where st_overlaps(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', 2 => 0], $this->pdo->bindings[0]);
    }

    public function testScopeDoesTouch()
    {
        $query = TestModel::doesTouch('point', $this->buildTestPolygon());

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select * from `test_models` where st_touches(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', 2 => 0], $this->pdo->bindings[0]);
    }

    public function testScopeOrderBySpatialThrowsExceptionWhenFunctionNotRegistered()
    {
        $point = new Point(1, 2);
        $this->assertException(
            UnknownSpatialFunctionException::class,
            'does-not-exist'
        );
        TestModel::orderBySpatial('point', $point, 'does-not-exist');
    }

    public function testScopeOrderByDistance()
    {
        $point = new Point(1, 2);
        $query = TestModel::orderByDistance('point', $point);

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select * from `test_models` order by st_distance(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) asc', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POINT(2 1)', 2 => 0], $this->pdo->bindings[0]);
    }

    public function testScopeOrderByDistanceSphere()
    {
        $point = new Point(1, 2);
        $query = TestModel::orderByDistanceSphere('point', $point);

        $this->assertInstanceOf(Builder::class, $query);
        $query->get();
        $this->assertEquals('select * from `test_models` order by st_distance_sphere(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) asc', $this->pdo->queries[0]);
        $this->assertEquals([1 => 'POINT(2 1)', 2 => 0], $this->pdo->bindings[0]);
    }
}







