<?php

namespace AngelSourceLabs\LaravelSpatial\Schema;

use Illuminate\Database\Schema\Blueprint;

class SpatialBlueprint extends Blueprint
{
    const GEOMETRY = 0;
    const GEOGRAPHY = 1;

    public function spatialColumn($type, $column, $srid = null, $mode = self::GEOMETRY)
    {
        $projection = $srid;
        $isGeometry = $mode == self::GEOMETRY ? true : null; // note: Laravel Illuminate\Database\Schema\Grammars\PostgresGrammar checks for null instead of false
        return $this->addColumn($type, $column, compact('srid', 'projection', 'isGeometry'));
    }

    /**
     * Add a geometry column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function geometry($column, $srid = null, $mode = self::GEOMETRY)
    {
        return $this->spatialColumn('geometry', $column, $srid, $mode);
    }

    /**
     * Add a point column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function point($column, $srid = null, $mode = self::GEOMETRY)
    {
        return $this->spatialColumn('point', $column, $srid, $mode);
    }

    /**
     * Add a linestring column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function lineString($column, $srid = null, $mode = self::GEOMETRY)
    {
        return $this->spatialColumn('linestring', $column, $srid, $mode);
    }

    /**
     * Add a polygon column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function polygon($column, $srid = null, $mode = self::GEOMETRY)
    {
        return $this->spatialColumn('polygon', $column, $srid, $mode);
    }

    /**
     * Add a multipoint column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function multiPoint($column, $srid = null, $mode = self::GEOMETRY)
    {
        return $this->spatialColumn('multipoint', $column, $srid, $mode);
    }

    /**
     * Add a multilinestring column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function multiLineString($column, $srid = null, $mode = self::GEOMETRY)
    {
        return $this->spatialColumn('multilinestring', $column, $srid, $mode);
    }

    /**
     * Add a multipolygon column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function multiPolygon($column, $srid = null, $mode = self::GEOMETRY)
    {
        return $this->spatialColumn('multipolygon', $column, $srid, $mode);
    }

    /**
     * Add a geometrycollection column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function geometryCollection($column, $srid = null, $mode = self::GEOMETRY)
    {
        return $this->spatialColumn('geometrycollection', $column, $srid, $mode);
    }

//    /**
//     * Specify a spatial index for the table.
//     *
//     * @param string|array $columns
//     * @param string       $name
//     *
//     * @return \Illuminate\Support\Fluent
//     */
//    public function spatialIndex($columns, $name = null)
//    {
//        return $this->indexCommand('spatial', $columns, $name);
//    }
//
//    /**
//     * Indicate that the given index should be dropped.
//     *
//     * @param string|array $index
//     *
//     * @return \Illuminate\Support\Fluent
//     */
//    public function dropSpatialIndex($index)
//    {
//        return $this->dropIndexCommand('dropIndex', 'spatial', $index);
//    }
}
