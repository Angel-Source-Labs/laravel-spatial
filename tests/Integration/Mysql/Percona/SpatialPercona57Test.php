<?php


namespace Tests\Integration\Mysql\Percona;


use Tests\Integration\SpatialTest;

class SpatialPercona57Test extends SpatialTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->usePercona57Connection($app);
    }
}
