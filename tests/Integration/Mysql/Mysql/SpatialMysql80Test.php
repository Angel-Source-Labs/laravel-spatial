<?php


namespace Tests\Integration\Mysql\Mysql;


use Tests\Integration\SpatialTest;

class SpatialMysql80Test extends SpatialTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMySqlConnection($app);
    }
}
