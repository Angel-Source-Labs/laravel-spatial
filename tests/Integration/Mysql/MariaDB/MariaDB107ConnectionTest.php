<?php

namespace Tests\Integration\Mysql\MariaDB;

use Tests\Integration\Mysql\MysqlConnectionTest;

class MariaDB107ConnectionTest extends MysqlConnectionTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMariaDBConnection($app);
    }
}
