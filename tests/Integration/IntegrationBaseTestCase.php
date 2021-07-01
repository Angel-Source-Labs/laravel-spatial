<?php

namespace Tests\Integration;

use AngelSourceLabs\LaravelExpressions\ExpressionsServiceProvider;
use AngelSourceLabs\LaravelSpatial\SpatialServiceProvider;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase;

abstract class IntegrationBaseTestCase extends TestCase
{
    use DatabaseConnections;

    protected $after_fix = false;
    protected $migrations = [];

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

    protected function defineEnvironment($app)
    {
        $this->useMySqlConnection($app);
    }

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
        $mysql_version = $results[0]->{'version()'};

        return version_compare($mysql_version, '8.0.4', '>=');
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

    private function onMigrations(\Closure $closure, $reverse_sort = false)
    {
        $migrations = $this->migrations;
        $reverse_sort ? rsort($migrations, SORT_STRING) : sort($migrations, SORT_STRING);

        foreach ($migrations as $migrationClass) {
            $closure($migrationClass);
        }
    }
}
