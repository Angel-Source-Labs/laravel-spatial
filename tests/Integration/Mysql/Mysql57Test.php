<?php


namespace Tests\Integration\Mysql;


use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use Tests\Integration\IntegrationBaseTestCase;
use Tests\Integration\TestsSpatialMethods;

class Mysql57Test extends IntegrationBaseTestCase
{
    use TestsSpatialMethods, TestsSchemaBuilder;

    public function getEnvironmentSetUp($app)
    {
        $this->useMySql57Connection($app);
    }

    public function schemaBuilder()
    {
        return MySqlBuilder::class;
    }
}
