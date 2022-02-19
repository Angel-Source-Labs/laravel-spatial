<?php

namespace Tests\Integration\Mysql;

use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use Tests\Integration\IntegrationBaseTestCase;
use Tests\Integration\TestsMysql57Migration;
use Tests\Integration\TestsSchemaBuilder;
use Tests\Integration\TestsSpatialMethods;

abstract class Mysql57IntegrationBaseTestCase extends IntegrationBaseTestCase
{
    use TestsSchemaBuilder,
        TestsSpatialMethods,
        TestsMysql57Migration;

    public function setUp(): void
    {
        parent::setUp();
        $this->setExpectedSchemaBuilder(MySqlBuilder::class);
    }
}