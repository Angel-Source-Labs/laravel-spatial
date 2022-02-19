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
use Illuminate\Support\Facades\DB;

trait TestsPostgisMigration
{
    protected function getExpectedColumns($srid = null)
    {
        $expectedColumnPrototypes = [
            "id" => [
                "type" => IntegerType::class,
                "unsigned" => false,
                "notnull" => true,
                "autoincrement" => true,
                "default" => null,
            ],
            "geo" => [
                "type" => Geometry::class,
                "unsigned" => false,
                "notnull" => false,
                "autoincrement" => false,
                "default" => null,
                "srid" => $srid
            ],
            "location" => [
                "type" => Point::class,
                "unsigned" => false,
                "notnull" => true,
                "autoincrement" => false,
                "default" => false,
                "srid" => $srid
            ],
            "created_at" => [
                "type" => DateTimeType::class,
                "unsigned" => false,
                "notnull" => false,
                "autoincrement" => false,
                "default" => null,
            ]
        ];

        if ($srid == null) {
            unset($expectedColumnPrototypes['geo']['srid']);
            unset($expectedColumnPrototypes['location']['srid']);
        }

        $expectedColumns = [
            "id" => $expectedColumnPrototypes["id"],
            "geo" => $expectedColumnPrototypes["geo"],
            "location" => $expectedColumnPrototypes["location"],
            "line" => $expectedColumnPrototypes["geo"],
            "shape" => $expectedColumnPrototypes["geo"],
            "multi_locations" => $expectedColumnPrototypes["geo"],
            "multi_lines" => $expectedColumnPrototypes["geo"],
            "multi_shapes" => $expectedColumnPrototypes["geo"],
            "multi_geometries" => $expectedColumnPrototypes["geo"],
            "created_at" => $expectedColumnPrototypes["created_at"],
            "updated_at" => $expectedColumnPrototypes["created_at"]
        ];

        foreach ([
                     'line' => LineString::class,
                     'shape' => Polygon::class,
                     'multi_locations' => MultiPoint::class,
                     'multi_lines' => MultiLineString::class,
                     'multi_shapes' => MultiPolygon::class,
                     'multi_geometries' => GeometryCollection::class,
                 ] as $key => $type) {
            $expectedColumns[$key]['type'] = $type;
        }

        if ($this->dbDriver == "mysql") $expectedColumns["id"]["unsigned"] = true;
        if ($this->dbDriver == "pgsql") $expectedColumns["id"]["unsigned"] = false;

        return $expectedColumns;
    }

    protected function assertPostgisTable($table, $expectedColumns = null)
    {
        /**
         * @var MySqlConnection | PostgresConnection $connection
         */
        $connection = DB::connection();

        /**
         * @var Column $details
         */
        $actualColumns = $connection->getDoctrineSchemaManager()->listTableColumns($table);

        $expectedColumns = $expectedColumns ?? $this->getExpectedColumns();

        foreach ($expectedColumns as $columnName => $columnProperty) {
            $this->assertArrayHasKey($columnName, $actualColumns, $columnName . " is not in table " . $table);
            $actualColumn = $actualColumns[$columnName]->toArray();
            foreach ($columnProperty as $propName => $propValue) {
                $actualValue = $actualColumn[$propName];
                $actualValue = is_object($actualValue) ? get_class($actualValue) : $actualValue;
                $this->assertEquals($propValue, $actualValue, "Mismatch " . $columnName . "->" . $propName);
            }
        }
    }

    /**
     * @environment-setup usePostgresConnection
     */
    public function test_postgis_TableWasCreatedWithRightTypes()
    {
        $expectedColumns = array_merge($this->getExpectedColumns(), [
            "geo" => [
                "geometryType" => "geometry"
            ],
            "location" => [
                "geometryType" => "geometry"
            ]
        ]);
        $this->assertPostgisTable('geometry_test', $expectedColumns);
    }

    /**
     * @environment-setup usePostgresConnection
     */
    public function test_postgis_TableWasCreatedWithSrid()
    {
        $expectedColumns = array_merge($this->getExpectedColumns(3857), [
            "geo" => [
                "geometryType" => "geometry"
            ],
            "location" => [
                "notnull" => false,
                "default" => null,
                "geometryType" => "geometry"
            ]
        ]);
        unset($expectedColumns['created_at']);
        unset($expectedColumns['updated_at']);

        $this->assertPostgisTable('with_srid', $expectedColumns);
    }

    /**
     * @environment-setup usePostgresConnection
     */
    public function test_postgis_TableWasCreatedWithGeography()
    {
        $expectedColumns = array_merge($this->getExpectedColumns(4326), [
            "geo" => [
                "geometryType" => "geography"
            ],
            "location" => [
                "notnull" => false,
                "default" => null,
                "geometryType" => "geography"
            ]
        ]);
        unset($expectedColumns['created_at']);
        unset($expectedColumns['updated_at']);

        $this->assertPostgisTable('with_geography', $expectedColumns);
    }
}