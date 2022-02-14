<?php


namespace Tests\Integration\Mysql\Mysql;


use Illuminate\Database\QueryException;
use Tests\Integration\Mysql\SridSpatialMysqlTest;

class SridSpatialMysql80Test extends SridSpatialMysqlTest
{
    public function getEnvironmentSetUp($app)
    {
        $this->useMySqlConnection($app);
    }
}
