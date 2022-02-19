<?php


namespace Tests\Integration\Mysql;


class MariaDB102Test extends Mysql57IntegrationBaseTestCase
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMariaDB102Connection($app);
    }
}
