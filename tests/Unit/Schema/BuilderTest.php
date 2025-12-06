<?php

namespace Tests\Unit\Schema;

use AngelSourceLabs\LaravelExpressions\Database\MySqlConnection;
use AngelSourceLabs\LaravelSpatial\Schema\Grammars\MySqlGrammar;
use AngelSourceLabs\LaravelSpatial\Schema\SpatialBlueprint;
use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\Unit\BaseTestCase;

class BuilderTest extends BaseTestCase
{
    public function testReturnsCorrectBlueprint()
    {
        $grammar = Mockery::mock(Grammar::class);

        $connection = Mockery::mock(MysqlConnection::class);
        $connection->shouldReceive('getSchemaGrammar')
            ->atLeast()->once()
            ->andReturn($grammar);

        $mock = Mockery::mock(MySqlBuilder::class, [$connection]);
        $mock->makePartial()->shouldAllowMockingProtectedMethods();
        $blueprint = $mock->createBlueprint('test', function () {
        });

        $this->assertInstanceOf(SpatialBlueprint::class, $blueprint);
    }
}
