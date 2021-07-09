<?php


namespace Tests\Integration;


class SpatialMysqlTest extends SpatialTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMySqlConnection($app);
    }
}
