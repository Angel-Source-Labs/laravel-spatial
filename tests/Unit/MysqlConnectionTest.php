<?php

namespace Tests\Unit;

use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase;
use Tests\Integration\IntegrationBaseTestCase;
use Tests\Unit\Stubs\PDOStub;

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
