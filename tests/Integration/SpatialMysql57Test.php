<?php


namespace Tests\Integration;


class SpatialMysql57Test extends SpatialTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMySql57Connection($app);
    }
}
