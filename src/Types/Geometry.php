<?php

namespace AngelSourceLabs\LaravelSpatial\Types;

use GeoIO\WKB\Parser\Parser;
use GeoJson\GeoJson;
use AngelSourceLabs\LaravelSpatial\Eloquent\SpatialExpression;
use AngelSourceLabs\LaravelSpatial\Exceptions\UnknownWKTTypeException;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Query\Expression;

abstract class Geometry implements GeometryInterface, Jsonable, \JsonSerializable
{
    protected static $wkb_types = [
        1 => Point::class,
        2 => LineString::class,
        3 => Polygon::class,
        4 => MultiPoint::class,
        5 => MultiLineString::class,
        6 => MultiPolygon::class,
        7 => GeometryCollection::class,
    ];

    protected $srid;
    
    protected $expression;

    public function __construct($srid = 0)
    {
        $this->srid = (int) $srid;
    }

    public function getSrid()
    {
        return $this->srid;
    }

    public function setSrid($srid)
    {
        $this->srid = (int) $srid;
    }

    public static function getWKTArgument($value)
    {
        $left = strpos($value, '(');
        $right = strrpos($value, ')');

        return substr($value, $left + 1, $right - $left - 1);
    }

    public static function getWKTClass($value)
    {
        $left = strpos($value, '(');
        $type = trim(substr($value, 0, $left));

        switch (strtoupper($type)) {
            case 'POINT':
                return Point::class;
            case 'LINESTRING':
                return LineString::class;
            case 'POLYGON':
                return Polygon::class;
            case 'MULTIPOINT':
                return MultiPoint::class;
            case 'MULTILINESTRING':
                return MultiLineString::class;
            case 'MULTIPOLYGON':
                return MultiPolygon::class;
            case 'GEOMETRYCOLLECTION':
                return GeometryCollection::class;
            default:
                throw new UnknownWKTTypeException('Type was '.$type);
        }
    }

    public static function fromWKB($wkb)
    {
        $srid = substr($wkb, 0, 4);
        $srid = unpack('L', $srid)[1];

        /**
         * The reason for this change is because there are 4 null bytes pre-pended in grimzy which are the srid
         */
        $wkb = substr($wkb, 4);
        /** @var Added by me $wkb */
//        $wkb = substr($wkb, 0);
        /** @var  $parser */

        $parser = new Parser(new Factory());

        /** @var Geometry $parsed */

        /**Added by me
        echo($wkb);
        echo("\n");
         * */

        $parsed = $parser->parse($wkb);

//        $parsed->getExpression();

        if ($srid > 0) {
            $parsed->setSrid($srid);
        }

        return $parsed;
    }

    public static function fromWKT($wkt, $srid = null)
    {
        $wktArgument = static::getWKTArgument($wkt);

        return static::fromString($wktArgument, $srid);
    }

    public static function fromJson($geoJson)
    {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if ($geoJson->getType() === 'FeatureCollection') {
            return GeometryCollection::fromJson($geoJson);
        }

        if ($geoJson->getType() === 'Feature') {
            $geoJson = $geoJson->getGeometry();
        }

        $type = '\AngelSourceLabs\LaravelSpatial\Types\\'.$geoJson->getType();

        return $type::fromJson($geoJson);
    }

    public function toJson($options = 0)
    {
        return json_encode($this, $options);
    }

    public function getExpression()
    {
        return $this->expression ?? $this->expression = new SpatialExpression($this);
    }

    public function getBindings(): array
    {
        return $this->getExpression()->getBindings();
    }

    public function getValue()
    {
        return $this->getExpression()->getValue();
    }

    public function __toString()
    {
        return (string) $this->getValue();
    }
}
