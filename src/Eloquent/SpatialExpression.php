<?php

namespace AngelSourceLabs\LaravelSpatial\Eloquent;

use AngelSourceLabs\LaravelExpressions\Database\Query\Expression\ExpressionGrammar;
use AngelSourceLabs\LaravelExpressions\Database\Query\Expression\ExpressionWithBindings;

use Illuminate\Database\Query\Expression;

class SpatialExpression extends Expression
{
    protected static function grammar($gisFunction, $geometryColumn, $suffix)
    {
        return ExpressionGrammar::make()
            ->mySql("$gisFunction(`$geometryColumn`, ST_GeomFromText(?, ?)) $suffix")
            ->mySql("$gisFunction(`$geometryColumn`, ST_GeomFromText(?, ?, 'axis-order=long-lat')) $suffix", "8.0")
            ->postgres("$gisFunction(\"$geometryColumn\", ST_GeomFromText(?, ?)) $suffix");
    }

    public static function make($gisFunction, $geometryColumn, $suffix='')
    {
        return new SpatialExpression(self::grammar($gisFunction, $geometryColumn, $suffix));
    }

    public static function distance($geometryColumn, $suffix='')
    {
        return self::make('st_distance', $geometryColumn, $suffix);
    }

    public static function distanceSphere($geometryColumn, $suffix='')
    {
        return new SpatialExpression(
            self::grammar('st_distance_sphere', $geometryColumn, $suffix)
                ->postgres("st_distanceSphere(\"$geometryColumn\", ST_GeomFromText(?, ?)) $suffix")
        );
    }

    public function withBindings(array $bindings)
    {
        return new SpatialExpressionWithBindings($this->value, $bindings);
    }
}