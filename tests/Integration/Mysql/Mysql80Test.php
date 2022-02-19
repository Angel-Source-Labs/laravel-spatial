<?php


namespace Tests\Integration\Mysql;


class Mysql80Test extends Mysql80IntegrationBaseTestCase
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMySqlConnection($app);
    }
}
