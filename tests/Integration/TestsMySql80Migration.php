<?php

namespace Tests\Integration;

use AngelSourceLabs\LaravelExpressions\Database\MySqlConnection;
use AngelSourceLabs\LaravelExpressions\Database\PostgresConnection;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait TestsMySql80Migration
{
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
//        if (method_exists($connection, 'getDoctrineSchemaManager')) {
//            $actualColumns = $connection->getDoctrineSchemaManager()->listTableColumns($table);
//        }
//        else {
//            $actualColumns = Schema::getColumns($table);
//        }



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