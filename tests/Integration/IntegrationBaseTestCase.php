<?php

namespace Tests\Integration;

use AngelSourceLabs\LaravelExpressions\ExpressionsServiceProvider;
use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use AngelSourceLabs\LaravelSpatial\SpatialServiceProvider;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase;

abstract class IntegrationBaseTestCase extends TestCase
{
    use DatabaseConnections;

//    public $expectedSchemaBuilder = null;
//    public $wrongSridExceptionMessage = null;

    protected $after_fix = false;
    protected $migrations = [];

//    public function test_rollback_migrations()
//    {
//        $this->assertTrue(true);
//    }

    /**
     * Setup DB before each test.
     *
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->after_fix = $this->isMySQL8AfterFix();

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

//        $this->onMigrations(function ($migrationClass) {
//            (new $migrationClass())->up();
//        });

        // Cannot declare class , because the name is already in use
    }

    protected function getPackageProviders($app)
    {
        return [
            ExpressionsServiceProvider::class,
            SpatialServiceProvider::class
        ];
    }

//    protected function defineEnvironment($app)
//    {
////        $this->useMySqlConnection($app);
//        $this->usePostgresConnection($app);
//    }

//    public function tearDown(): void
//    {
//        $this->onMigrations(function ($migrationClass) {
//            (new $migrationClass())->down();
//        }, true);
//
//        parent::tearDown();
//    }

    // MySQL 8.0.4 fixed bug #26941370 and bug #88031
    private function isMySQL8AfterFix()
    {
        $results = DB::select(DB::raw('select version()'));
        if ($this->dbDriver == "mysql") {
            $mysql_version = $results[0]->{'version()'}; // mysql
            return version_compare($mysql_version, '8.0.4', '>=');
        }
        elseif ($this->dbDriver == "pgsql") {
            $postgis_version = $results[0]->{'version'}; // postgis
            return true;
        }
    }

    protected function assertDatabaseHas($table, array $data, $connection = null)
    {
        if (method_exists($this, 'seeInDatabase')) {
            $this->seeInDatabase($table, $data, $connection);
        } else {
            parent::assertDatabaseHas($table, $data, $connection);
        }
    }

    protected function assertException($exceptionName, $exceptionMessage = null)
    {
        if (method_exists(parent::class, 'expectException')) {
            parent::expectException($exceptionName);
            if (!is_null($exceptionMessage)) {
                $this->expectExceptionMessage($exceptionMessage);
            }
        } else {
            $this->setExpectedException($exceptionName, $exceptionMessage);
        }
    }
}
