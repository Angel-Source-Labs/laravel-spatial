<?php

namespace Tests\Integration;

use AngelSourceLabs\LaravelExpressions\Database\MySqlConnection;
use AngelSourceLabs\LaravelExpressions\Database\PostgresConnection;
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
  SPATIAL KEY `geometry_location_spatial` (`location`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

        $this->assertEquals('geometry_test', $result->Table);
        $this->assertEquals($expected, $result->{'Create Table'});
    }

    public function test_postgis_TableWasCreatedWithRightTypes()
    {
        /*
         * TODO: types appear to be registered properly now.
         *  - finish migration test using dbal.
         *  - see if we can run the new migration test against mysql
         */


        $table = 'geometry_test';

//        $result = DB::selectOne('SHOW CREATE TABLE geometry');
//        $result = DB::select(
//        'select
//            column_name, data_type, character_maximum_length, column_default, is_nullable
//        from
//             INFORMATION_SCHEMA.COLUMNS where table_name = \'' . $table . '\';');

//        $columns1 = DB::getSchemaBuilder()->getColumnListing( $table );
//        $columnTypes1 = collect($columns1)->map(function($column) use ($table) {
//            return [$column => DB::getSchemaBuilder()->getColumnType($table, $column)];
//        });

        /**
         * @var MySqlConnection | PostgresConnection $connection
         */
        $connection = DB::connection();

        /**
         * @var Column $details
         */
        $actualColumns = $connection->getDoctrineSchemaManager()->listTableColumns($table);

        $expectedColumnPrototypes = [
            "id" => [
                "type" => IntegerType::class,
                "unsigned" => false,
                "notnull" => true,
                "autoincrement" => true,
                "default" => null,
            ],
            "geo" => [
                "type" => StringType::class,
                "unsigned" => false,
                "notnull" => false,
                "autoincrement" => false,
                "default" => null,
            ],
            "location" => [
                "type" => StringType::class,
                "unsigned" => false,
                "notnull" => true,
                "autoincrement" => false,
                "default" => false,
            ],
            "created_at" => [
                "type" => DateTimeType::class,
                "unsigned" => false,
                "notnull" => false,
                "autoincrement" => false,
                "default" => null,
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

        if ($this->dbDriver == "mysql") $expectedColumns["id"]["unsigned"] = true;
        if ($this->dbDriver == "pgsql") $expectedColumns["id"]["unsigned"] = false;

        foreach ($expectedColumns as $columnName => $columnProprety) {
            $this->assertArrayHasKey($columnName, $actualColumns, $columnName . " is not in table" . $table);
            $actualColumn = $actualColumns[$columnName]->toArray();
            foreach ($columnProprety as $propName => $propValue) {
                $actualValue = $actualColumn[$propName];
                $actualValue = is_object($actualValue) ? get_class($actualValue) : $actualValue;
                $this->assertEquals($propValue, $actualValue, "Mismatch " . $columnName . "->" . $propName);
            }
        }
    }

    public function testTableWasCreatedWithSrid()
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
  `geo` geometry /*!80003 SRID 3857 */ DEFAULT NULL,
  `location` point /*!80003 SRID 3857 */ DEFAULT NULL,
  `line` linestring /*!80003 SRID 3857 */ DEFAULT NULL,
  `shape` polygon /*!80003 SRID 3857 */ DEFAULT NULL,
  `multi_locations` multipoint /*!80003 SRID 3857 */ DEFAULT NULL,
  `multi_lines` multilinestring /*!80003 SRID 3857 */ DEFAULT NULL,
  `multi_shapes` multipolygon /*!80003 SRID 3857 */ DEFAULT NULL,
  `multi_geometries` geomcollection /*!80003 SRID 3857 */ DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

        $this->assertEquals('with_srid', $result->Table);
        $this->assertEquals($expected, $result->{'Create Table'});
    }
}
