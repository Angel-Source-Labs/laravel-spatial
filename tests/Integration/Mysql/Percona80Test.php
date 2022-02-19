<?php


namespace Tests\Integration\Mysql;


class Percona80Test extends Mysql80IntegrationBaseTestCase
{
    public function getEnvironmentSetUp($app)
    {
        $this->usePerconaConnection($app);
    }
}
