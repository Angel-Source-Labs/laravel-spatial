<?php

namespace Tests\Integration\Mysql;

use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use Illuminate\Support\Facades\DB;
use Tests\Integration\IntegrationBaseTestCase;

abstract class MysqlConnectionTest extends IntegrationBaseTestCase
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMySqlConnection($app);
    }

    public function testGetSchemaBuilder()
    {
        $builder = DB::connection()->getSchemaBuilder();

        $this->assertInstanceOf(MySqlBuilder::class, $builder);
    }
}
