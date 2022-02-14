<?php


namespace Tests\Integration\Mysql;


use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use Tests\Integration\IntegrationBaseTestCase;
use Tests\Integration\TestsSpatialMethods;

class MariaDB107Test extends IntegrationBaseTestCase
{
    use TestsSpatialMethods, TestsSchemaBuilder;

    public function getEnvironmentSetUp($app)
    {
        $this->useMariaDBConnection($app);
    }

    public function schemaBuilder()
    {
        return MySqlBuilder::class;
    }
}
