<?php


namespace Tests\Integration;


class SpatialPostgisTest extends SpatialTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->usePostgresConnection($app);
    }
}
