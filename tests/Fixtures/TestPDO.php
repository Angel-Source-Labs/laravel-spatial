<?php


namespace Tests\Fixtures;


use PDO;
use Mockery as m;

class TestPDO extends PDO
{
    public $queries = [];
    public $bindings = [];

    public $counter = 1;

    protected $exists = true;

    public function prepare($statement, $driver_options = [])
    {
        $this->queries[] = $statement;
        $key = array_key_last($this->queries);
        $bindings = &$this->bindings;

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('bindValue')->zeroOrMoreTimes()->withArgs(function($param, $value, $type = null)
        use (&$bindings, $key)
        {
            $bindings[$key][$param] = $value;

            return true;
        });
        $stmt->shouldReceive('execute');
        $stmt->shouldReceive('setFetchMode')->andReturnTrue();
//        $stmt->shouldReceive('fetchAll')->andReturn($this->mockResults());
        $stmt->shouldReceive('fetchAll')->andReturnUsing(function() {return $this->mockResults();});
        $stmt->shouldReceive('rowCount')->andReturn(1);

        return $stmt;
    }

    public function mockExists($exists = true)
    {
        $this->exists = $exists;
    }

    protected function mockResults() {
        if (preg_match('/as .?exists.?/', end($this->queries))) {
            return [['exists' => $this->exists]];
        }
        else {
            return [['id' => $this->counter, 'point' => 'POINT(1 2)']];
        }
    }

    public function lastInsertId($name = null)
    {
        return $this->counter++;
    }

    public function resetQueries()
    {
        $this->queries = [];
    }
}