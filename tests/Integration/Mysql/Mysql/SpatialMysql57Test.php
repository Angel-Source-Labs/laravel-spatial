<?php


namespace Tests\Integration\Mysql\Mysql;


use Tests\Integration\SpatialTest;

class SpatialMysql57Test extends SpatialTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMySql57Connection($app);
    }
}
