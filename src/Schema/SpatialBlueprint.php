<?php

namespace AngelSourceLabs\LaravelSpatial\Schema;

use Illuminate\Database\Schema\Blueprint;

class SpatialBlueprint extends Blueprint
{
    /**
     * Add a geometry column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function geometry($column, $srid = null)
    {
        $projection = $srid;
        return $this->addColumn('geometry', $column, compact('srid', 'projection'));
    }

    /**
     * Add a point column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function point($column, $srid = null)
    {
        $projection = $srid;
        return $this->addColumn('point', $column, compact('srid', 'projection'));
    }

    /**
     * Add a linestring column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function lineString($column, $srid = null)
    {
        $projection = $srid;
        return $this->addColumn('linestring', $column, compact('srid', 'projection'));
    }

    /**
     * Add a polygon column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function polygon($column, $srid = null)
    {
        $projection = $srid;
        return $this->addColumn('polygon', $column, compact('srid', 'projection'));
    }

    /**
     * Add a multipoint column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function multiPoint($column, $srid = null)
    {
        $projection = $srid;
        return $this->addColumn('multipoint', $column, compact('srid', 'projection'));
    }

    /**
     * Add a multilinestring column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function multiLineString($column, $srid = null)
    {
        $projection = $srid;
        return $this->addColumn('multilinestring', $column, compact('srid', 'projection'));
    }

    /**
     * Add a multipolygon column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function multiPolygon($column, $srid = null)
    {
        $projection = $srid;
        return $this->addColumn('multipolygon', $column, compact('srid', 'projection'));
    }

    /**
     * Add a geometrycollection column on the table.
     *
     * @param string   $column
     * @param null|int $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function geometryCollection($column, $srid = null)
    {
        $projection = $srid;
        return $this->addColumn('geometrycollection', $column, compact('srid', 'projection'));
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
