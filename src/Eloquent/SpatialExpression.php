<?php

namespace AngelSourceLabs\LaravelSpatial\Eloquent;


use AngelSourceLabs\LaravelExpressions\Database\Query\Expression\ExpressionWithBindings;
use AngelSourceLabs\LaravelExpressions\Database\Query\Expression\Grammar;
use AngelSourceLabs\LaravelSpatial\Types\GeometryInterface;


class SpatialExpression extends ExpressionWithBindings
{
    /**
     * @var GeometryInterface
     */
    protected $geometry;

    public function __construct(GeometryInterface $geometry)
    {
        $geomFromText = Grammar::make()
            ->mySql("ST_GeomFromText(?, ?, 'axis-order=long-lat')")
            ->postgres("ST_GeomFromText(?, ?)");

        parent::__construct($geomFromText, []);
        $this->geometry = $geometry;
    }

    public function getBindings(): array
    {
        return [
            $this->geometry->toWkt(),
            $this->geometry->getSrid()
        ];
    }
}
