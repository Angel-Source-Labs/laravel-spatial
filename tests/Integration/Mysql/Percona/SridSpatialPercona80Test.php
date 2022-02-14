<?php


namespace Tests\Integration\Mysql\Percona;


use Tests\Integration\Mysql\SridSpatialMysqlTest;

class SridSpatialPercona80Test extends SridSpatialMysqlTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->usePerconaConnection($app);
    }
}
