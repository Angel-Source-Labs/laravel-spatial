<?php


namespace Tests\Integration\Mysql;


use Illuminate\Database\QueryException;
use Tests\Integration\SridSpatialTest;

abstract class SridSpatialMysqlTest extends SridSpatialTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMySqlConnection($app);
    }

    public function testInsertPointWithWrongSrid()
    {
        $this->assertException(
            QueryException::class,
            'SQLSTATE[HY000]: General error: 3643 The SRID of the geometry '.
            'does not match the SRID of the column \'location\'. The SRID '.
            'of the geometry is 4326, but the SRID of the column is 3857. '.
            'Consider changing the SRID of the geometry or the SRID property '.
            'of the column. (SQL: insert into `with_srid` (`location`) values '.
            '(ST_GeomFromText(POINT(2 1), 4326, \'axis-order=long-lat\')))'
        );

        parent::testInsertPointWithWrongSrid();
    }
}
