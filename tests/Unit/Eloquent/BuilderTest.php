<?php

namespace Tests\Unit\Eloquent;

use AngelSourceLabs\LaravelExpressions\Database\MySqlConnection;
use AngelSourceLabs\LaravelSpatial\Eloquent\Builder;
use AngelSourceLabs\LaravelSpatial\Eloquent\SpatialTrait;
use AngelSourceLabs\LaravelSpatial\Types\LineString;
use AngelSourceLabs\LaravelSpatial\Types\Point;
use AngelSourceLabs\LaravelSpatial\Types\Polygon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Mockery;
use Tests\Unit\BaseTestCase;

class BuilderTest extends BaseTestCase
{
    protected $builder;
    protected $queryBuilder;

    public function setUp(): void
    {
        $connection = Mockery::mock(MysqlConnection::class)->makePartial();
        $grammar = Mockery::mock(MySqlGrammar::class)->makePartial();
        $this->queryBuilder = Mockery::mock(QueryBuilder::class, [$connection, $grammar]);

        $this->queryBuilder
            ->shouldReceive('from')
            ->once()
            ->andReturn($this->queryBuilder);

        $this->builder = new Builder($this->queryBuilder);
        $this->builder->setModel(new TestBuilderModel());
    }

    public function testUpdatePoint()
    {
        $point = new Point(1, 2);
        $this->queryBuilder
            ->shouldReceive('update')
            ->with(['point' => $point])
            ->once()
            ->andReturn(1);

        $result = $this->builder->update(['point' => $point]);

        $this->assertSame(1, $result);
    }

    public function testUpdateLinestring()
    {
        $linestring = new LineString([new Point(0, 0), new Point(1, 1), new Point(2, 2)]);

        $this->queryBuilder
            ->shouldReceive('update')
            ->with(['linestring' => $linestring])
            ->once()
            ->andReturn(1);

        $result = $this->builder->update(['linestring' => $linestring]);

        $this->assertSame(1, $result);
    }

    public function testUpdatePolygon()
    {
        $linestrings[] = new LineString([new Point(0, 0), new Point(0, 1)]);
        $linestrings[] = new LineString([new Point(0, 1), new Point(1, 1)]);
        $linestrings[] = new LineString([new Point(1, 1), new Point(0, 0)]);
        $polygon = new Polygon($linestrings);

        $this->queryBuilder
            ->shouldReceive('update')
            ->with(['polygon' => $polygon])
            ->once()
            ->andReturn(1);

        $result = $this->builder->update(['polygon' => $polygon]);

        $this->assertSame(1, $result);
    }

    public function testUpdatePointWithSrid()
    {
        $point = new Point(1, 2, 4326);
        $this->queryBuilder
            ->shouldReceive('update')
            ->with(['point' => $point])
            ->once()
            ->andReturn(1);

        $result = $this->builder->update(['point' => $point]);

        $this->assertSame(1, $result);
    }

    public function testUpdateLinestringWithSrid()
    {
        $linestring = new LineString([new Point(0, 0), new Point(1, 1), new Point(2, 2)], 4326);

        $this->queryBuilder
            ->shouldReceive('update')
            ->with(['linestring' => $linestring])
            ->once()
            ->andReturn(1);

        $result = $this->builder->update(['linestring' => $linestring]);

        $this->assertSame(1, $result);
    }

    public function testUpdatePolygonWithSrid()
    {
        $linestrings[] = new LineString([new Point(0, 0), new Point(0, 1)]);
        $linestrings[] = new LineString([new Point(0, 1), new Point(1, 1)]);
        $linestrings[] = new LineString([new Point(1, 1), new Point(0, 0)]);
        $polygon = new Polygon($linestrings, 4326);

        $this->queryBuilder
            ->shouldReceive('update')
            ->with(['polygon' => $polygon])
            ->once()
            ->andReturn(1);

        $result = $this->builder->update(['polygon' => $polygon]);

        $this->assertSame(1, $result);
    }
}

class TestBuilderModel extends Model
{
    use SpatialTrait;

    public $timestamps = false;
    protected $spatialFields = ['point', 'linestring', 'polygon'];
}
