<?php


namespace Tests\Integration;


use Illuminate\Database\QueryException;

class SridSpatialPostgis12Test extends SridSpatialTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->usePostgresConnection($app);
    }

    public function testInsertPointWithWrongSrid()
    {
        $this->assertException(
            QueryException::class,
            'SQLSTATE[22023]: Invalid parameter value: 7 ERROR:  Geometry '.
            'SRID (4326) does not match column SRID (3857) (SQL: insert into '.
            '"with_srid" ("location") values (ST_GeomFromText(POINT(2 1), 4326)) '.
            'returning "id")'
        );

        parent::testInsertPointWithWrongSrid();
    }
}
