<?php

namespace Tests\Integration\Mysql\Mysql;

use Tests\Integration\Mysql\MysqlConnectionTest;

class Mysql80ConnectionTest extends MysqlConnectionTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMySqlConnection($app);
    }
}
