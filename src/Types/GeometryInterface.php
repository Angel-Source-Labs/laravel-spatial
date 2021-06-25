<?php

namespace AngelSourceLabs\LaravelSpatial\Types;

use AngelSourceLabs\LaravelExpressions\Database\Query\Expression\HasBindings;
use AngelSourceLabs\LaravelExpressions\Database\Query\Expression\IsExpression;

interface GeometryInterface extends IsExpression, HasBindings
{
    public function toWKT();

    public static function fromWKT($wkt, $srid = 0);

    public function getSrid();

    public function __toString();

    public static function fromString($wktArgument, $srid = 0);

    public static function fromJson($geoJson);
}
