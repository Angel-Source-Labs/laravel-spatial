<?php

namespace Tests\Integration\Mysql;

use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use Composer\Semver\Semver;
use Tests\Integration\IntegrationBaseTestCase;
use Tests\Integration\TestsMySql80Migration;
use Tests\Integration\TestsSchemaBuilder;
use Tests\Integration\TestsSpatialMethods;
use Tests\Integration\TestsSrid;

abstract class Mysql80IntegrationBaseTestCase extends IntegrationBaseTestCase
{
    use TestsSchemaBuilder,
        TestsSpatialMethods,
        TestsMySql80Migration,
        TestsSrid;

    public function setUp(): void
    {
        parent::setUp();
        $this->setExpectedSchemaBuilder(MySqlBuilder::class);

        $connectionString = "";
        if (Semver::satisfies(app()->version(), "^10.0")) {
            $connectionString = "Connection: mysql, ";
        }
        $this->setWrongSridExceptionMessage(
            'SQLSTATE[HY000]: General error: 3643 The SRID of the geometry '.
            'does not match the SRID of the column \'location\'. '.
            'The SRID of the geometry is 4326, but the SRID of the column is 3857. '.
            'Consider changing the SRID of the geometry or the SRID property of the column. ('.
            $connectionString.
            'SQL: insert into `with_srid` (`location`) values (ST_GeomFromText(POINT(2 1), 4326, \'axis-order=long-lat\')))'
        );
    }
}