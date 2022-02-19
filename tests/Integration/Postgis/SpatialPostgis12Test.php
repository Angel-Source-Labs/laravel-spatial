<?php


namespace Tests\Integration\Postgis;


use AngelSourceLabs\LaravelSpatial\Schema\PostgresBuilder;
use Tests\Integration\IntegrationBaseTestCase;
use Tests\Integration\TestsPostgisMigration;
use Tests\Integration\TestsSchemaBuilder;
use Tests\Integration\TestsSpatialMethods;
use Tests\Integration\TestsSrid;

class SpatialPostgis12Test extends IntegrationBaseTestCase
{
    use TestsSchemaBuilder, TestsPostgisMigration, TestsSpatialMethods, TestsSrid;

    public function getEnvironmentSetUp($app)
    {
        $this->usePostgresConnection($app);
        $this->setExpectedSchemaBuilder(PostgresBuilder::class);
        $this->setWrongSridExceptionMessage(
            'SQLSTATE[22023]: Invalid parameter value: 7 ERROR:  Geometry ' .
            'SRID (4326) does not match column SRID (3857) (SQL: insert into ' .
            '"with_srid" ("location") values (ST_GeomFromText(POINT(2 1), 4326)) ' .
            'returning "id")'
        );
    }
}
