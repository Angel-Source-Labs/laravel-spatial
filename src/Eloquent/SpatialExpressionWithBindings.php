<?php

namespace AngelSourceLabs\LaravelSpatial\Eloquent;

use AngelSourceLabs\LaravelExpressions\Database\Query\Expression\HasBindings;
use AngelSourceLabs\LaravelExpressions\Database\Query\Expression\ProvidesBindings;

class SpatialExpressionWithBindings extends SpatialExpression implements HasBindings
{
    use ProvidesBindings;
}
