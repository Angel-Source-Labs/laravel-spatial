<?php

namespace Tests\Unit\Schema\Grammars;

use AngelSourceLabs\LaravelExpressions\Database\MySqlConnection;
use AngelSourceLabs\LaravelSpatial\Schema\SpatialBlueprint;
use AngelSourceLabs\LaravelSpatial\Schema\Grammars\MySqlGrammar;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\Unit\BaseTestCase;

class MySqlGrammarTest extends BaseTestCase
{
    public function testAddingGeometry()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->geometry('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` GEOMETRY not null', $statements[0]);
    }

    public function testAddingPoint()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->point('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` POINT not null', $statements[0]);
    }

    public function testAddingLinestring()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->linestring('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` LINESTRING not null', $statements[0]);
    }

    public function testAddingPolygon()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->polygon('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` POLYGON not null', $statements[0]);
    }

    public function testAddingMultipoint()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->multipoint('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` MULTIPOINT not null', $statements[0]);
    }

    public function testAddingMultiLinestring()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->multilinestring('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` MULTILINESTRING not null', $statements[0]);
    }

    public function testAddingMultiPolygon()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->multipolygon('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` MULTIPOLYGON not null', $statements[0]);
    }

    public function testAddingGeometryCollection()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->geometrycollection('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` GEOMETRYCOLLECTION not null', $statements[0]);
    }

    public function testAddingGeometryWithSrid()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->geometry('foo', 4326);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` GEOMETRY not null srid 4326', $statements[0]);
    }

    public function testAddingPointWithSrid()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->point('foo', 4326);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` POINT not null srid 4326', $statements[0]);
    }

    public function testAddingLinestringWithSrid()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->linestring('foo', 4326);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` LINESTRING not null srid 4326', $statements[0]);
    }

    public function testAddingPolygonWithSrid()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->polygon('foo', 4326);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` POLYGON not null srid 4326', $statements[0]);
    }

    public function testAddingMultipointWithSrid()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->multipoint('foo', 4326);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` MULTIPOINT not null srid 4326', $statements[0]);
    }

    public function testAddingMultiLinestringWithSrid()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->multilinestring('foo', 4326);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` MULTILINESTRING not null srid 4326', $statements[0]);
    }

    public function testAddingMultiPolygonWithSrid()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->multipolygon('foo', 4326);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` MULTIPOLYGON not null srid 4326', $statements[0]);
    }

    public function testAddingGeometryCollectionWithSrid()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->geometrycollection('foo', 4326);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` GEOMETRYCOLLECTION not null srid 4326', $statements[0]);
    }

    public function testAddRemoveSpatialIndex()
    {
        $blueprint = new SpatialBlueprint('test');
        $blueprint->point('foo');
        $blueprint->spatialIndex('foo');
        $addStatements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(2, count($addStatements));
        $this->assertEquals('alter table `test` add spatial index `test_foo_spatialindex`(`foo`)', $addStatements[1]);

        $blueprint->dropSpatialIndex(['foo']);
        $blueprint->dropSpatialIndex('test_foo_spatialindex');
        $dropStatements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $expectedSql = 'alter table `test` drop index `test_foo_spatialindex`';
        $this->assertEquals(5, count($dropStatements));
        $this->assertEquals($expectedSql, $dropStatements[3]);
        $this->assertEquals($expectedSql, $dropStatements[4]);
    }

//    protected function getConnection()
//    {
////        return Mockery::mock(MysqlConnection::class);
//        return DB::connection();
//    }

    protected function getGrammar()
    {
//        return new MySqlGrammar();
        return DB::connection()->getSchemaGrammar();
    }
}
