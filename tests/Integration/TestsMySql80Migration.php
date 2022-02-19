<?php

namespace Tests\Integration;

use Illuminate\Support\Facades\DB;

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
}