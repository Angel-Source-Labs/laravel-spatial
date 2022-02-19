<?php


namespace Tests\Integration\Mysql;


class MariaDB107Test extends Mysql57IntegrationBaseTestCase
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMariaDBConnection($app);
    }
}
