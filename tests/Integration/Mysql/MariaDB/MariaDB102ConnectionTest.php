<?php

namespace Tests\Integration\Mysql\MariaDB;

use Tests\Integration\Mysql\MysqlConnectionTest;

class MariaDB102ConnectionTest extends MysqlConnectionTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMariaDB102Connection($app);
    }
}
