<?php


namespace Tests\Integration\Mysql;


use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use Tests\Integration\IntegrationBaseTestCase;
use Tests\Integration\TestsSpatialMethods;
use Tests\Integration\TestsSrid;

class Percona80Test extends IntegrationBaseTestCase
{
    use TestsSpatialMethods, TestsSchemaBuilder, TestsSrid;

    public function getEnvironmentSetUp($app)
    {
        $this->usePerconaConnection($app);
    }

    public function schemaBuilder()
    {
        return MySqlBuilder::class;
    }
}
