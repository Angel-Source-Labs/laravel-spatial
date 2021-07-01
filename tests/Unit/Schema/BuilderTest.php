<?php

namespace Tests\Unit\Schema;

use AngelSourceLabs\LaravelExpressions\Database\MySqlConnection;
use AngelSourceLabs\LaravelSpatial\Schema\SpatialBlueprint;
use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use Mockery;
use Tests\Unit\BaseTestCase;

class BuilderTest extends BaseTestCase
{
    public function testReturnsCorrectBlueprint()
    {
        $connection = Mockery::mock(MysqlConnection::class);
        $connection->shouldReceive('getSchemaGrammar')->once()->andReturn(null);

        $mock = Mockery::mock(MySqlBuilder::class, [$connection]);
        $mock->makePartial()->shouldAllowMockingProtectedMethods();
        $blueprint = $mock->createBlueprint('test', function () {
        });

        $this->assertInstanceOf(SpatialBlueprint::class, $blueprint);
    }
}
