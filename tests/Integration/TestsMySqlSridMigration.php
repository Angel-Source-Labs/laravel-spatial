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

trait TestsMySqlSridMigration
{
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