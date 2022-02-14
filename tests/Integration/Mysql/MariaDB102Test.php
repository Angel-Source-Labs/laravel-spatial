<?php


namespace Tests\Integration\Mysql;


use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use Tests\Integration\IntegrationBaseTestCase;
use Tests\Integration\TestsSpatialMethods;

class MariaDB102Test extends IntegrationBaseTestCase
{
    use TestsSpatialMethods, TestsSchemaBuilder;

    public function getEnvironmentSetUp($app)
    {
        $this->useMariaDB102Connection($app);
    }

    public function schemaBuilder()
    {
        return MySqlBuilder::class;
    }
}
