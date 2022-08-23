<?php

namespace AngelSourceLabs\LaravelSpatial\Types;

use AngelSourceLabs\LaravelExpressions\Database\Query\Expression\ExpressionGrammar;
use GeoIO\WKB\Parser\Parser;
use GeoJson\GeoJson;
use AngelSourceLabs\LaravelSpatial\Exceptions\UnknownWKTTypeException;
use Illuminate\Contracts\Support\Jsonable;

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
    
    /**
     * @var ExpressionGrammar
     */
    protected $grammar;

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
        return $this;
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

    /**
     * MySQL stores geometry values using 4 bytes to indicate the SRID followed by the WKB representation of the value.
     * For a description of WKB format, see Well-Known Binary (WKB) Format.
     * https://dev.mysql.com/doc/refman/8.0/en/gis-data-formats.html
     *
     * @param $wkb
     * @return mixed
     */
    public static function fromWKB($wkb)
    {
        $format = preg_match('/^[0-9a-fA-F]*$/', $wkb) ? "EWKB" : "MYSQL";

        if ($format == "MYSQL") {
            $srid = substr($wkb, 0, 4);
            $srid = unpack('L', $srid)[1];
            $wkb = substr($wkb, 4);
        }

        /** @var  $parser */
        $parser = new Parser(new Factory());

        /** @var Geometry $parsed */
        $parsed = $parser->parse($wkb);

        if (isset($srid) && $srid > 0) {
            $parsed->setSrid($srid);
        }

        return $parsed;
    }

    /**
     * @param $wkt
     * @param null $srid
     * @return Geometry
     */
    public static function fromWKT($wkt, $srid = null)
    {
        $wktArgument = static::getWKTArgument($wkt);

        return static::fromString($wktArgument, $srid);
    }

    /**
     * @param $wkt
     * @param null $srid
     * @return Geometry
     */
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

        /**
         * @var $type GeometryInterface
         */
        $type = '\AngelSourceLabs\LaravelSpatial\Types\\'.$geoJson->getType();

        return $type::fromJson($geoJson);
    }

    public function toJson($options = 0)
    {
        return json_encode($this, $options);
    }

    public function hasBindings(): bool
    {
        return true;
    }

    public function getBindings(): array
    {
        return [$this->toWKT(), $this->getSrid()];
    }

    public function getValue()
    {
        return $this->grammar = $this->grammar ?? ExpressionGrammar::make()
                    ->mySql("ST_GeomFromText(?, ?)")
                    ->mySql("ST_GeomFromText(?, ?, 'axis-order=long-lat')", "8.0")
                    // Temporary fix for MariaDB
                    ->mySql("ST_GeomFromText(?, ?)", "10.0")
                    ->postgres("ST_GeomFromText(?, ?)");
    }

    public function __toString()
    {
        return (string) $this->getValue();
    }
}
