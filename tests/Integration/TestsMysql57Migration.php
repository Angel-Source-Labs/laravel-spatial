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

trait TestsMysql57Migration
{
    /**
     * @environment-setup useMySql57Connection
     */
    public function test_mysql57_TableWasCreatedWithRightTypes()
    {
        $result = DB::selectOne('SHOW CREATE TABLE geometry_test');

        $expected = 'CREATE TABLE `geometry_test` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `geo` geometry DEFAULT NULL,
  `location` point NOT NULL,
  `line` linestring DEFAULT NULL,
  `shape` polygon DEFAULT NULL,
  `multi_locations` multipoint DEFAULT NULL,
  `multi_lines` multilinestring DEFAULT NULL,
  `multi_shapes` multipolygon DEFAULT NULL,
  `multi_geometries` geometrycollection DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  SPATIAL KEY `geometry_test_location_spatialindex` (`location`)
) ENGINE=' . (config('database.connections.mysql.myisam') ? 'MyISAM' : 'InnoDB') . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

        $this->assertEquals('geometry_test', $result->Table);
        $this->assertEquals($expected, $result->{'Create Table'});
    }
}