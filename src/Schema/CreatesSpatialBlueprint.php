<?php


namespace AngelSourceLabs\LaravelSpatial\Schema;

use AngelSourceLabs\LaravelSpatial\Support\LaravelVersion;
use Closure;

trait CreatesSpatialBlueprint
{
    /**
     * Create a new command set with a Closure.
     *
     * @param string  $table
     * @param Closure $callback
     *
     * @return SpatialBlueprint
     */
    protected function createBlueprint($table, ?Closure $callback = null)
    {
        if (LaravelVersion::is12OrHigher()) {
            return new SpatialBlueprint($this->connection, $table, $callback);
        }

        return new SpatialBlueprint($table, $callback);
    }
}