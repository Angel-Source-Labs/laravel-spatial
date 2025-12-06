<?php

namespace Tests\Unit\Schema\Grammars;

use AngelSourceLabs\LaravelExpressions\Database\MySqlConnection;
use AngelSourceLabs\LaravelSpatial\Schema\CreatesSpatialBlueprint;
use AngelSourceLabs\LaravelSpatial\Schema\SpatialBlueprint;
use AngelSourceLabs\LaravelSpatial\Schema\Grammars\MySqlGrammar;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\Unit\BaseTestCase;

class MySqlGrammarTest extends BaseTestCase
{
    use CreatesSpatialBlueprint;

    public function setUp(): void
    {
        parent::setUp();
        $this->connection = DB::connection();
    }

    public function testAddingGeometry()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->geometry('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` GEOMETRY not null', $statements[0]);
    }

    public function testAddingPoint()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->point('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` POINT not null', $statements[0]);
    }

    public function testAddingLinestring()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->linestring('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` LINESTRING not null', $statements[0]);
    }

    public function testAddingPolygon()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->polygon('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` POLYGON not null', $statements[0]);
    }

    public function testAddingMultipoint()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->multipoint('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` MULTIPOINT not null', $statements[0]);
    }

    public function testAddingMultiLinestring()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->multilinestring('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` MULTILINESTRING not null', $statements[0]);
    }

    public function testAddingMultiPolygon()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->multipolygon('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` MULTIPOLYGON not null', $statements[0]);
    }

    public function testAddingGeometryCollection()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->geometrycollection('foo');
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` GEOMETRYCOLLECTION not null', $statements[0]);
    }

    public function testAddingGeometryWithSrid()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->geometry('foo', 4326);

        $connection = $this->getConnection();
        $schemaGrammar = $connection->getSchemaGrammar();
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` GEOMETRY not null srid 4326', $statements[0]);
    }

    public function testAddingPointWithSrid()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->point('foo', 4326);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` POINT not null srid 4326', $statements[0]);
    }

    public function testAddingLinestringWithSrid()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->linestring('foo', 4326);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` LINESTRING not null srid 4326', $statements[0]);
    }

    public function testAddingPolygonWithSrid()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->polygon('foo', 4326);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` POLYGON not null srid 4326', $statements[0]);
    }

    public function testAddingMultipointWithSrid()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->multipoint('foo', 4326);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` MULTIPOINT not null srid 4326', $statements[0]);
    }

    public function testAddingMultiLinestringWithSrid()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->multilinestring('foo', 4326);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` MULTILINESTRING not null srid 4326', $statements[0]);
    }

    public function testAddingMultiPolygonWithSrid()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->multipolygon('foo', 4326);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` MULTIPOLYGON not null srid 4326', $statements[0]);
    }

    public function testAddingGeometryCollectionWithSrid()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->geometrycollection('foo', 4326);
        $statements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(1, count($statements));
        $this->assertEquals('alter table `test` add `foo` GEOMETRYCOLLECTION not null srid 4326', $statements[0]);
    }

    public function testAddRemoveSpatialIndex()
    {
        $blueprint = $this->createBlueprint('test');
        $blueprint->point('foo');
        $blueprint->spatialIndex('foo');
        $addStatements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        $this->assertEquals(2, count($addStatements));
        $this->assertEquals('alter table `test` add spatial index `test_foo_spatialindex`(`foo`)', $addStatements[1]);

        $blueprint = $this->createBlueprint('test');
        $blueprint->dropSpatialIndex(['foo']);
        $blueprint->dropSpatialIndex('test_foo_spatialindex');
        $dropStatements = $blueprint->toSql($this->getConnection(), $this->getGrammar());

        // the difference here is that there are two "alter table `test` add `foo` POINT not null" statements in <= laravel 10.x and only one in laravel 11.x
        // above when the add statements are checked there is only 1.
        // when blueprint->toSql is rerun there are 2.
        //
        // two possible solutions:
        // 1: create a new blueprint for the second check instead of reusing the original.
        // 2: filter the output to get only the drop statements and verify that those are correct.

        $expectedSql = 'alter table `test` drop index `test_foo_spatialindex`';
        $this->assertEquals(2, count($dropStatements));
        $this->assertEquals($expectedSql, $dropStatements[0]);
        $this->assertEquals($expectedSql, $dropStatements[1]);
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
