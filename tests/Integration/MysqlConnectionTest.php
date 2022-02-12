<?php

namespace Tests\Integration;

use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use Illuminate\Support\Facades\DB;

class MysqlConnectionTest extends IntegrationBaseTestCase
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
