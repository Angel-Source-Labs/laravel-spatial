<?php

namespace Tests\Unit;

use AngelSourceLabs\LaravelExpressions\ExpressionsServiceProvider;
use Illuminate\Support\Facades\DB;
use Mockery as m;
use Orchestra\Testbench\TestCase;
use Tests\Fixtures\TestPDO;
use Tests\Fixtures\TestSpatialServiceProvider;

abstract class BaseTestCase extends TestCase
{
    /**
     * @var m\Mock | TestPDO
     */
    protected $pdo;

    protected function getPackageProviders($app)
    {
        return [
            ExpressionsServiceProvider::class,
            TestSpatialServiceProvider::class,
        ];
    }

    public function setUp() : void
    {
        parent::setUp();
        $this->pdo = m::mock(TestPDO::class)->makePartial();
        $connection = DB::connection();
        $connection->setPdo($this->pdo);
    }

    public function tearDown(): void
    {
        m::close();
    }

    protected function assertException($exceptionName, $exceptionMessage = '', $exceptionCode = 0)
    {
        if (method_exists(parent::class, 'expectException')) {
            parent::expectException($exceptionName);
            parent::expectExceptionMessage($exceptionMessage);
            parent::expectExceptionCode($exceptionCode);
        } else {
            $this->setExpectedException($exceptionName, $exceptionMessage, $exceptionCode);
        }
    }
}
