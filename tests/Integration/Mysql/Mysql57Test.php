<?php


namespace Tests\Integration\Mysql;


class Mysql57Test extends Mysql57IntegrationBaseTestCase
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMySql57Connection($app);
    }
}
