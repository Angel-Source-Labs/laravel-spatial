<?php


namespace Tests\Integration\Mysql\MariaDB;


use Tests\Integration\SpatialTest;

class SpatialMariaDB102Test extends SpatialTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMariaDB102Connection($app);
    }
}
