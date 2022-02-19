<?php


namespace Tests\Integration\Mysql;


class Percona57Test extends Mysql57IntegrationBaseTestCase
{
    public function getEnvironmentSetUp($app)
    {
        $this->usePercona57Connection($app);
    }
}
