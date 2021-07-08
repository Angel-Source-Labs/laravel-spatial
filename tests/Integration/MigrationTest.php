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

class MigrationTest extends IntegrationBaseTestCase
{
    /**
     * @environment-setup useMySqlConnection
     */
    public function test_mysql_TableWasCreatedWithRightTypes()
    {
        $result = DB::selectOne('SHOW CREATE TABLE geometry_test');

        $expected = 'CREATE TABLE `geometry_test` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `geo` geometry DEFAULT NULL,
  `location` point NOT NULL,
  `line` linestring DEFAULT NULL,
  `shape` polygon DEFAULT NULL,
  `multi_locations` multipoint DEFAULT NULL,
  `multi_lines` multilinestring DEFAULT NULL,
  `multi_shapes` multipolygon DEFAULT NULL,
  `multi_geometries` geomcollection DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  SPATIAL KEY `geometry_test_location_spatialindex` (`location`)
) ENGINE=' . (config('database.connections.mysql.myisam') ? 'MyISAM' : 'InnoDB') . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

        $this->assertEquals('geometry_test', $result->Table);
        $this->assertEquals($expected, $result->{'Create Table'});
    }

    protected function getExpectedColumns($srid = 4326)
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
                "srid" => $srid
            ]
        ];

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

        foreach ($expectedColumns as $columnName => $columnProprety) {
            $this->assertArrayHasKey($columnName, $actualColumns, $columnName . " is not in table " . $table);
            $actualColumn = $actualColumns[$columnName]->toArray();
            foreach ($columnProprety as $propName => $propValue) {
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
        $this->assertPostgisTable('geometry_test');
    }

    /**
     * @environment-setup useMySqlConnection
     */
    public function test_mysql_TableWasCreatedWithSrid()
    {
        $table = 'with_srid';

        /**
         * @var MySqlConnection | PostgresConnection $connection
         */
        $connection = DB::connection();

        /**
         * @var Column $details
         */
        $actualColumns = $connection->getDoctrineSchemaManager()->listTableColumns($table);


        $result = DB::selectOne('SHOW CREATE TABLE with_srid');

        $expected = 'CREATE TABLE `with_srid` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `geo` geometry /*!80003 SRID 4322 */ DEFAULT NULL,
  `location` point /*!80003 SRID 4322 */ DEFAULT NULL,
  `line` linestring /*!80003 SRID 4322 */ DEFAULT NULL,
  `shape` polygon /*!80003 SRID 4322 */ DEFAULT NULL,
  `multi_locations` multipoint /*!80003 SRID 4322 */ DEFAULT NULL,
  `multi_lines` multilinestring /*!80003 SRID 4322 */ DEFAULT NULL,
  `multi_shapes` multipolygon /*!80003 SRID 4322 */ DEFAULT NULL,
  `multi_geometries` geomcollection /*!80003 SRID 4322 */ DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

        $this->assertEquals('with_srid', $result->Table);
        $this->assertEquals($expected, $result->{'Create Table'});
    }

    /**
     * @environment-setup usePostgresConnection
     */
    public function test_postgis_TableWasCreatedWithSrid()
    {
        $expectedColumns = array_merge($this->getExpectedColumns(4322), [
            "location" => [
                "notnull" => false,
                "default" => null,
            ]
        ]);
        unset($expectedColumns['created_at']);
        unset($expectedColumns['updated_at']);

        $this->assertPostgisTable('with_srid', $expectedColumns);

        $dog = 1;
    }
}