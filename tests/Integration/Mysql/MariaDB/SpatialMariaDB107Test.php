<?php


namespace Tests\Integration\Mysql\MariaDB;


use Tests\Integration\SpatialTest;

class SpatialMariaDB107Test extends SpatialTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMariaDBConnection($app);
    }
}
