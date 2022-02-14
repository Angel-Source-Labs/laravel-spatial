<?php

namespace Tests\Integration\Mysql\Mysql;

use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use Illuminate\Support\Facades\DB;
use Tests\Integration\IntegrationBaseTestCase;
use Tests\Integration\Mysql\MysqlConnectionTest;

class Mysql57ConnectionTest extends MysqlConnectionTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMySql57Connection($app);
    }
}
