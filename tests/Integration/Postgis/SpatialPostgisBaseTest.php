<?php


namespace Tests\Integration\Postgis;


use AngelSourceLabs\LaravelSpatial\Schema\PostgresBuilder;
use Composer\Semver\Semver;
use Tests\Integration\IntegrationBaseTestCase;
use Tests\Integration\TestsPostgisMigration;
use Tests\Integration\TestsSchemaBuilder;
use Tests\Integration\TestsSpatialMethods;
use Tests\Integration\TestsSrid;

class SpatialPostgisBaseTest extends IntegrationBaseTestCase
{
    use TestsSchemaBuilder, TestsPostgisMigration, TestsSpatialMethods, TestsSrid;

    public function getEnvironmentSetUp($app)
    {
        $this->usePostgresConnection($app);
        $this->setExpectedSchemaBuilder(PostgresBuilder::class);

        $connectionString = "";
        if (Semver::satisfies(app()->version(), ">=10.0")) {
            $connectionString = "Connection: pgsql, ";
        }
        $this->setWrongSridExceptionMessage(
            'SQLSTATE[22023]: Invalid parameter value: 7 ERROR:  Geometry ' .
            'SRID (4326) does not match column SRID (3857) ('.
            $connectionString.
            'SQL: insert into ' .
            '"with_srid" ("location") values (ST_GeomFromText(POINT(2 1), 4326)) ' .
            'returning "id")'
        );
    }
}
