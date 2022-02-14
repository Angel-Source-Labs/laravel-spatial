<?php


namespace Tests\Integration\Mysql\Percona;


use Tests\Integration\SpatialTest;

class SpatialPercona80Test extends SpatialTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->usePerconaConnection($app);
    }
}
