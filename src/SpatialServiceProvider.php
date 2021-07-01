<?php

namespace AngelSourceLabs\LaravelSpatial;

use AngelSourceLabs\LaravelSpatial\Schema\Grammars\MySqlGrammar;
use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use AngelSourceLabs\LaravelSpatial\Schema\PostgresBuilder;
use AngelSourceLabs\LaravelSpatial\Schema\SQLiteBuilder;
use AngelSourceLabs\LaravelSpatial\Schema\SqlServerBuilder;
use Doctrine\DBAL\Types\Type as DoctrineType;
use AngelSourceLabs\LaravelSpatial\Doctrine\Geometry;
use AngelSourceLabs\LaravelSpatial\Doctrine\GeometryCollection;
use AngelSourceLabs\LaravelSpatial\Doctrine\LineString;
use AngelSourceLabs\LaravelSpatial\Doctrine\MultiLineString;
use AngelSourceLabs\LaravelSpatial\Doctrine\MultiPoint;
use AngelSourceLabs\LaravelSpatial\Doctrine\MultiPolygon;
use AngelSourceLabs\LaravelSpatial\Doctrine\Point;
use AngelSourceLabs\LaravelSpatial\Doctrine\Polygon;
use Illuminate\Database\Connection;

/**
 * Class DatabaseServiceProvider.
 */
class SpatialServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\Illuminate\Database\Schema\MySqlBuilder::class, MySqlBuilder::class);
        $this->app->bind(\Illuminate\Database\Schema\PostgresBuilder::class, PostgresBuilder::class);
        $this->app->bind(\Illuminate\Database\Schema\SQLiteBuilder::class, SQLiteBuilder::class);
        $this->app->bind(\Illuminate\Database\Schema\SqlServerBuilder::class, SqlServerBuilder::class);

        if (class_exists(DoctrineType::class)) {
            // Prevent geometry type fields from throwing a 'type not found' error when changing them
            $geometries = [
                'geometry'           => Geometry::class,
                'point'              => Point::class,
                'linestring'         => LineString::class,
                'polygon'            => Polygon::class,
                'multipoint'         => MultiPoint::class,
                'multilinestring'    => MultiLineString::class,
                'multipolygon'       => MultiPolygon::class,
                'geometrycollection' => GeometryCollection::class,
            ];
            $typeNames = array_keys(DoctrineType::getTypesMap());
            foreach ($geometries as $type => $class) {
                if (!in_array($type, $typeNames)) {
                    DoctrineType::addType($type, $class);
                }
            }
        }
    }

    public function boot()
    {
        $this->resolveSpatialSchemaGrammar();
    }

    public function registerGeometryTypes($connection)
    {
        $dbPlatform = $connection
            ->getDoctrineSchemaManager()
            ->getDatabasePlatform();

        // Prevent geometry type fields from throwing a 'type not found' error when changing them
        $geometries = [
            'geometry',
            'point',
            'linestring',
            'polygon',
            'multipoint',
            'multilinestring',
            'multipolygon',
            'geometrycollection',
            'geomcollection',
        ];

        foreach ($geometries as $type) {
            $dbPlatform->registerDoctrineTypeMapping($type, 'string');
        }

    }

    protected function resolveSpatialSchemaGrammar()
    {
        $connections = [
            'mysql' => [
                'schemaGrammar' => MySqlGrammar::class,
            ],
            'pgsql' => [
                'schemaGrammar' => \Illuminate\Database\Schema\Grammars\PostgresGrammar::class,
            ],
            'sqlite' => [
                'schemaGrammar' => \Illuminate\Database\Schema\Grammars\SQLiteGrammar::class,
            ],
            'sqlsrv' => [
                'schemaGrammar' => \Illuminate\Database\Schema\Grammars\SqlServerGrammar::class,
            ],
        ];

        foreach($connections as $driver => $class) {

            $resolver = Connection::getResolver($driver);
            Connection::resolverFor($driver, function($pdo, $database = '', $tablePrefix = '', array $config = []) use ($driver, $resolver, $class) {
                /**
                 * @var Connection | null $connection
                 */
                $connection = $resolver($pdo, $database, $tablePrefix, $config);
                $connection->setSchemaGrammar(new $class['schemaGrammar']);
                $this->registerGeometryTypes($connection);

                return $connection;
            });
        }
    }
}
