<?php


namespace AngelSourceLabs\LaravelSpatial\Schema;

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
    protected function createBlueprint($table, Closure $callback = null)
    {
        return new SpatialBlueprint($table, $callback);
    }
}