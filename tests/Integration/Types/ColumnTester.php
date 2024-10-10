<?php

namespace Tests\Integration\Types;

use AngelSourceLabs\LaravelSpatial\Doctrine\Types\Geometry;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\GeometryCollection;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\LineString;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\MultiLineString;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\MultiPoint;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\MultiPolygon;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\Point;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\Polygon;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\IntegerType;
use Illuminate\Support\Facades\DB;

class ColumnTester
{
    public $name;
    /**
     * @var mixed|null
     */
    protected $table = null;
    protected $unsigned = false;
    protected $nullable = false;
    protected $autoIncrement = false;
    protected $default = null;
    protected $srid = null;
    protected $type = null;
    protected $dbalType = null;
    protected $geometrySubType = false;
    protected $fail;
    /**
     * @var mixed
     */
    protected $comparedColumn;
    /**
     * @var array
     */
    protected $columnAsCompared;
    /**
     * @var false
     */
    protected $isGeographyType;

    public function __construct($name)
    {
        $this->name = $name;
        $this->fail = [];
    }

    public static function create($name): ColumnTester
    {
        return new ColumnTester($name);
    }

    public function table($value = null): ColumnTester
    {
        $this->table = $value;
        return $this;
    }
    public function unsigned($value = true): ColumnTester
    {
        $this->unsigned = $value;
        return $this;
    }

    public function nullable($value = true): ColumnTester
    {
        $this->nullable = $value;
        return $this;
    }

    public function autoIncrement($value = true): ColumnTester
    {
        $this->autoIncrement = $value;
        return $this;
    }

    public function default($value = null): ColumnTester
    {
        $this->default = $value;
        return $this;
    }

    public function srid($value = null): ColumnTester
    {
        $this->srid = $value;
        return $this;
    }

    public function integer(): ColumnTester
    {
        $this->type = "integer";
        $this->dbalType = IntegerType::class;
        return $this;
    }

    public function geometry(): ColumnTester
    {
        $this->type = "Geometry";
        $this->isGeographyType = false;
        $this->geometrySubType = true;
        $this->dbalType = Geometry::class;
        return $this;
    }

    public function geography(): ColumnTester
    {
        $this->isGeographyType = true;
        return $this;
    }

    protected function geometrySubType($type, $dbalType): ColumnTester
    {
        $this->type = $type;
        $this->geometrySubType = true;
        $this->dbalType = $dbalType;
        return $this;
    }
    
    public function point(): ColumnTester
    {
        return $this->geometrySubType("Point", Point::class);
    }

    public function dateTimeType(): ColumnTester
    {
        $this->type = "timestamp(0) without time zone";
        $this->dbalType = DateTimeType::class;
        return $this;
    }

    public function lineString(): ColumnTester
    {
        return $this->geometrySubType("LineString", LineString::class);
    }

    public function polygon(): ColumnTester
    {
        return $this->geometrySubType("Polygon", Polygon::class);
    }

    public function multiPoint(): ColumnTester
    {
        return $this->geometrySubType("MultiPoint", MultiPoint::class);
    }

    public function multiLineString(): ColumnTester
    {
        return $this->geometrySubType("MultiLineString", MultiLineString::class);
    }

    public function multiPolygon(): ColumnTester
    {
        return $this->geometrySubType("MultiPolygon", MultiPolygon::class);
    }

    public function geometryCollection(): ColumnTester
    {
        return $this->geometrySubType("GeometryCollection", GeometryCollection::class);
    }

    public function idPrototype($table = null): ColumnTester
    {
        return $this->table($table)
            ->integer()
            ->unsigned(false)
            ->nullable(false)
            ->autoIncrement(true)
            ->default(null);
    }
    public function geoProtoype($srid = null): ColumnTester
    {
        return $this->geometry()
            ->unsigned(false)
            ->nullable(true)
            ->autoIncrement(false)
            ->srid($srid)
            ->default(null);
    }

    public function locationPrototype($srid = null): ColumnTester
    {
        return $this->point()
            ->unsigned(false)
            ->nullable(false)
            ->autoIncrement(false)
            ->default(false)
            ->srid($srid);
    }

    public function createdAtPrototype() : ColumnTester
    {
        return $this->dateTimeType()
            ->unsigned(false)
            ->nullable(true)
            ->autoIncrement(false)
            ->default(null);
    }

    //todo when it is just geometry witih no SRID (and no geography?) then it is just 'geometry' and not 'geometry(Geometry)'
    protected function laravelGeometryType() : string
    {
        $hasSrid = isset($this->srid);

        if ($hasSrid) {
            if ($this->isGeographyType) return "geography($this->type,$this->srid)";
            return "geometry($this->type,$this->srid)";
        }

        if ($this->type == 'Geometry') return "geometry";

        return "geometry($this->type)";
    }

    protected function laravelType() : string
    {
        if ($this->geometrySubType)
            return $this->laravelGeometryType();

        return $this->type;
    }

    protected function laravelDefault()
    {
        if ($this->autoIncrement && $this->isPostgres() && is_null($this->default))
            return "nextval('{$this->table}_id_seq'::regclass)";
        return $this->default;
    }

    /**
     * DBAL column:
     * $column = [
     *      'type' => "Doctrine\DBAL\Types\IntegerType"
     *      'unsigned' => false
     *      'notnull' => true
     *      'autoincrement' => true
     *      'default' => null
     *  ];
     */
    protected function compareDbalColumn(Column $column) : void
    {
        $columnAsArray = $column->toArray();
        $columnAsArray["type"] = get_class($column->getType());
        $this->comparedColumn = $columnAsArray;

        $this->columnAsCompared = [
            'name' => $this->name,
            'type' => $this->dbalType,
            'notnull' => ! $this->nullable,
            'default' => $this->default,
            'autoincrement' => $this->autoIncrement,
        ];

        foreach ($this->columnAsCompared as $key => $value) {
            if ($value != $columnAsArray[$key]) $this->compareFail($key);
        }
    }

    /**
     * Laravel column:
     * $column = [
     *      'name' => "id"
     *      'type_name' => "int4"
     *      'type' => "integer"
     *      'collation' => null
     *      'nullable' => false
     *      'default' => "nextval('geometry_test_id_seq':regclass)"
     *      'auto_increment' => true
     *      'comment' => null
     *      'generation' => null
     * ];
     */
    protected function compareLaravelColumn($column) : void
    {
        $this->columnAsCompared = [
            'name' => $this->name,
            'type' => $this->laravelType(),
            'nullable' => $this->nullable,
            'default' => $this->laravelDefault(),
            'auto_increment' => $this->autoIncrement,
        ];

        foreach ($this->columnAsCompared as $key => $value) {
            if ($value != $column[$key]) $this->compareFail($key);
        }
    }
    
    protected function compareFail($string = null) : void
    {
        if (isset($string))
            $this->fail[] = $string;
    }

    protected function isDbal() : bool
    {
        static $isDbal = null;
        return $isDbal ?? ($isDbal = method_exists(DB::connection(), 'getDoctrineSchemaManager'));
    }

    protected function isPostgres() : bool
    {
        return DB::connection()->getDriverName() == "pgsql";
    }

    /**
     * @param $column
     * @return bool | mixed
     */
    public function compare($column)
    {
        $this->fail = [];

        $this->comparedColumn = $column;
        if ($this->isDbal()) {
            $this->compareDbalColumn($column);
        }
        else {
            $this->compareLaravelColumn($column);
        }
        return count($this->fail) == 0;
    }

    public function comparisonFailureMessage() : string
    {
        if (count($this->fail) > 0) {
            return "$this->name: The following column properties failed comparison: " .
                implode(', ', $this->fail) . "\n" .
                "\nExpected:\n" .
                var_export($this->columnAsCompared, true) . "\n" .
                "\nActual:\n" .
                var_export($this->comparedColumn, true) . "\n";
        }
        return "";
    }
}