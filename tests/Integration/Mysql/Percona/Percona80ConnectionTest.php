<?php

namespace Tests\Integration\Mysql\Percona;

use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use Illuminate\Support\Facades\DB;
use Tests\Integration\IntegrationBaseTestCase;
use Tests\Integration\Mysql\MysqlConnectionTest;

class Percona80ConnectionTest extends MysqlConnectionTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->usePerconaConnection($app);
    }
}
