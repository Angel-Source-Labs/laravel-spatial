<?php


namespace Tests\Integration;


class SpatialPostgis12Test extends SpatialTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->usePostgresConnection($app);
    }
}
