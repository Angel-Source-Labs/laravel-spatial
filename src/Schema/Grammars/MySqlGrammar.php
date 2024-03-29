<?php

namespace AngelSourceLabs\LaravelSpatial\Schema\Grammars;

use AngelSourceLabs\LaravelSpatial\Schema\SpatialBlueprint;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\MySqlGrammar as IlluminateMySqlGrammar;
use Illuminate\Support\Fluent;
use PDO;

class MySqlGrammar extends IlluminateMySqlGrammar
{
    const COLUMN_MODIFIER_SRID = 'Srid';

    public function configureSridSupport(AbstractPlatform $databasePlatform)
    {
        $sridSupported = $databasePlatform instanceof MySQL80Platform;

        if ( $sridSupported ) {
            // Enable SRID as a column modifier
            if (!in_array(self::COLUMN_MODIFIER_SRID, $this->modifiers)) {
                $this->modifiers[] = self::COLUMN_MODIFIER_SRID;
            }
        }
        else {
            // Disable SRID as a column modifier
            if ($key = array_search(self::COLUMN_MODIFIER_SRID, $this->modifiers)) {
                unset($this->modifiers[$key]);
            }
        }

    }

    public function compileCreate(Blueprint $blueprint, Fluent $command, Connection $connection)
    {
        $databasePlatform = $connection->getDoctrineConnection()->getDatabasePlatform();
        $version = $connection->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION);
        $driverName = $connection->getDriverName();
        $this->configureSridSupport($databasePlatform);

        return parent::compileCreate($blueprint, $command, $connection);
    }

    /**
     * Adds a statement to add a geometry column.
     *
     * @param Fluent $column
     *
     * @return string
     */
    public function typeGeometry(Fluent $column)
    {
        return 'GEOMETRY';
    }

    /**
     * Adds a statement to add a point column.
     *
     * @param Fluent $column
     *
     * @return string
     */
    public function typePoint(Fluent $column)
    {
        return 'POINT';
    }

    /**
     * Adds a statement to add a linestring column.
     *
     * @param Fluent $column
     *
     * @return string
     */
    public function typeLinestring(Fluent $column)
    {
        return 'LINESTRING';
    }

    /**
     * Adds a statement to add a polygon column.
     *
     * @param Fluent $column
     *
     * @return string
     */
    public function typePolygon(Fluent $column)
    {
        return 'POLYGON';
    }

    /**
     * Adds a statement to add a multipoint column.
     *
     * @param Fluent $column
     *
     * @return string
     */
    public function typeMultipoint(Fluent $column)
    {
        return 'MULTIPOINT';
    }

    /**
     * Adds a statement to add a multilinestring column.
     *
     * @param Fluent $column
     *
     * @return string
     */
    public function typeMultilinestring(Fluent $column)
    {
        return 'MULTILINESTRING';
    }

    /**
     * Adds a statement to add a multipolygon column.
     *
     * @param Fluent $column
     *
     * @return string
     */
    public function typeMultipolygon(Fluent $column)
    {
        return 'MULTIPOLYGON';
    }

    /**
     * Adds a statement to add a geometrycollection column.
     *
     * @param Fluent $column
     *
     * @return string
     */
    public function typeGeometrycollection(Fluent $column)
    {
        return 'GEOMETRYCOLLECTION';
    }

    /**
     * Compile a spatial index key command.
     *
     * @param SpatialBlueprint $blueprint
     * @param Fluent    $command
     *
     * @return string
     */
    public function compileSpatial(SpatialBlueprint $blueprint, Fluent $command)
    {
        return $this->compileKey($blueprint, $command, 'spatial');
    }

    /**
     * Get the SQL for a SRID column modifier.
     *
     * @param \Illuminate\Database\Schema\Blueprint $blueprint
     * @param Fluent                                $column
     *
     * @return string|null
     */
    protected function modifySrid(\Illuminate\Database\Schema\Blueprint $blueprint, Fluent $column)
    {
        if (!is_null($column->srid) && is_int($column->srid) && $column->srid > 0) {
            return ' srid '.$column->srid;
        }
    }
}
