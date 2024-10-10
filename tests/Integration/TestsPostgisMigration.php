<?php

namespace Tests\Integration;

use AngelSourceLabs\LaravelExpressions\Database\MySqlConnection;
use AngelSourceLabs\LaravelExpressions\Database\PostgresConnection;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\Geometry;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\GeometryCollection;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\LineString;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\MultiLineString;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\MultiPoint;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\MultiPolygon;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\Point;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\Polygon;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Integration\Types\ColumnTester;

trait TestsPostgisMigration
{
    protected function isDbal()
    {
        static $isDbal = null;
        return $isDbal ?? ($isDbal = method_exists(DB::connection(), 'getDoctrineSchemaManager'));
    }

    protected function getExpectedColumns($table, $srid = null)
    {
        /**
         * @var $expectedColumns ColumnTester[]
         */
        $expectedColumns = [
            "id" => ColumnTester::create("id")->idPrototype($table),
            "geo" => ColumnTester::create("geo")->geoProtoype($srid),
            "location" => ColumnTester::create("location")->locationPrototype($srid),
            "line" => ColumnTester::create("line")->geoProtoype($srid)->lineString(),
            "shape" => ColumnTester::create("shape")->geoProtoype($srid)->polygon(),
            "multi_locations" => ColumnTester::create("multi_locations")->geoProtoype($srid)->multiPoint(),
            "multi_lines" => ColumnTester::create("multi_lines")->geoProtoype($srid)->multiLineString(),
            "multi_shapes" => ColumnTester::create("multi_shapes")->geoProtoype($srid)->multiPolygon(),
            "multi_geometries" => ColumnTester::create("multi_geometries")->geoProtoype($srid)->geometryCollection(),
            "created_at" => ColumnTester::create("created_at")->createdAtPrototype(),
            "updated_at" => ColumnTester::create("updated_at")->createdAtPrototype(),
        ];

        if ($this->dbDriver == "mysql") $expectedColumns["id"]->unsigned();
        if ($this->dbDriver == "pgsql") $expectedColumns["id"]->unsigned(false);

        return $expectedColumns;
    }

    protected function getActualColumns($table)
    {
        /**
         * @var MySqlConnection | PostgresConnection $connection
         */
        $connection = DB::connection();

        if ($this->isDbal())
            return $connection->getDoctrineSchemaManager()->listTableColumns($table);
        else
            return Arr::keyBy(Schema::getColumns($table), 'name');
    }

    /**
     * @param $table string
     * @param $expectedColumns ColumnTester[]
     * @return void
     */
    protected function assertPostgisTable($table, $expectedColumns = null)
    {
        $expectedColumns = $expectedColumns ?? $this->getExpectedColumns($table);
        $actualColumns = $this->getActualColumns($table);

        foreach ($expectedColumns as $column) {
            $this->assertTrue(
                $column->compare($actualColumns[$column->name]),
                $column->comparisonFailureMessage());
        }


        // todo the column listing array is formatted differently between doctrine and laravel 11.  Have to address.  Maybe compare to how laravel-excel-seeder handles it.

//        Schema::getColumnType($table, $expectedColumns['id']);
//        Schema::hasColumn($table, $expectedColumns['geo']);
//        Schema::getColumnListing($table);

        // todo - can't get SRID of column using Laravel Schema
        // option 1. query for SRID separately
        // option 2. modify Postgres grammar compileColumns to retrieve SRID.  Note that we already have a custom postgres grammar class that has been loaded.
        //  I need to see a migrated table so that I can verify that we read the SRID.  The migration has multiple tables with and without SRID.  Maybe I am looking at one without SRID.  need to verify.

        // this whole thing is a mess right now.   This assertion is actually such a simple thing, just creating an array of all the pieces that will be listed.   So I should have a separate one for dbal and laravel b/c their keys are so different.   Get rid of the type functions.
    }

//    public function test_postgis_srid()
//    {
//        /**
//         * DBAL: Point::class
//         * Laravel 11, no ssrid: "geometry(Point)"
//         * Laravel 11, with srid: "geometry(Point,3857)"
//         * Laraevl 11, geography: "geography(Point,4326)"
//         */
////        $table = 'with_srid';
//        $table = 'with_geography';
//        $actualColumns = Arr::keyBy(Schema::getColumns($table), 'name');
//        $ac2 = Schema::getColumnListing($table);
//        $tables = Schema::getTables();
//
//        $this->assertTrue(false);
//    }

    /**
     * @environment-setup usePostgresConnection
     */
    public function test_postgis_TableWasCreatedWithRightTypes()
    {
        $expectedColumns = $this->getExpectedColumns('geometry_test');

//        $expectedColumns = array_merge($this->getExpectedColumns(), [
//            "geo" => [
//                "geometryType" => "geometry"
//            ],
//            "location" => [
//                "geometryType" => "geometry"
//            ]
//        ]);
        $this->assertPostgisTable('geometry_test', $expectedColumns);
    }

    /**
     * @environment-setup usePostgresConnection
     */
    public function test_postgis_TableWasCreatedWithSrid()
    {
        $expectedColumns = $this->getExpectedColumns('with_srid', 3857);
        $expectedColumns["geo"]->geometry();
        $expectedColumns["location"]->nullable();
//        $expectedColumns = array_merge($this->getExpectedColumns(3857), [
//            "geo" => [
//                "geometryType" => "geometry"
//            ],
//            "location" => [
//                "notnull" => false,
//                "default" => null,
//                "geometryType" => "geometry"
//            ]
//        ]);
        unset($expectedColumns['created_at']);
        unset($expectedColumns['updated_at']);

        $this->assertPostgisTable('with_srid', $expectedColumns);
    }

    /**
     * @environment-setup usePostgresConnection
     */
    public function test_postgis_TableWasCreatedWithGeography()
    {
        $expectedColumns = $this->getExpectedColumns('with_geography', 4326);
        $columns = [
            "geo",
            "location",
            "line",
            "shape",
            "multi_locations",
            "multi_lines",
            "multi_shapes",
            "multi_geometries",
            ];
        foreach ($columns as $column) {
            $expectedColumns[$column]->geography()->nullable()->default(null);
        }

        unset($expectedColumns['created_at']);
        unset($expectedColumns['updated_at']);

        $this->assertPostgisTable('with_geography', $expectedColumns);
    }
}