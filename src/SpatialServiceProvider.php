<?php

namespace AngelSourceLabs\LaravelSpatial;

use AngelSourceLabs\LaravelSpatial\Doctrine\Event\Listeners\PostgisSchemaColumnDefinitionEventSubscriber;
use AngelSourceLabs\LaravelSpatial\Schema\Grammars\MySqlGrammar;
use AngelSourceLabs\LaravelSpatial\Schema\Grammars\PostgisGrammar;
use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use AngelSourceLabs\LaravelSpatial\Schema\PostgresBuilder;
use AngelSourceLabs\LaravelSpatial\Schema\SQLiteBuilder;
use AngelSourceLabs\LaravelSpatial\Schema\SqlServerBuilder;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Types\Type as DoctrineType;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\Geometry;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\GeometryCollection;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\LineString;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\MultiLineString;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\MultiPoint;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\MultiPolygon;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\Point;
use AngelSourceLabs\LaravelSpatial\Doctrine\Types\Polygon;
use Illuminate\Database\Connection;

use Illuminate\Support\Facades\Log;

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

    /**
     * @param Connection $connection
     */
    public function registerGeometryTypes($connection)
    {
        $dbPlatform = $connection
            ->getDoctrineSchemaManager()
            ->getDatabasePlatform();

        // Prevent geometry type fields from throwing a 'type not found' error when changing them
        $geometries = [
            'geometry',
            'geography',
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

        $eventManager = $dbPlatform->getEventManager();
        if ($eventManager instanceof EventManager)
            $eventManager->addEventSubscriber(new PostgisSchemaColumnDefinitionEventSubscriber);

    }

    protected function isDbal($connection)
    {
        static $isDbal = null;
        return $isDbal ?? ($isDbal = method_exists($connection, 'getDoctrineSchemaManager'));
    }

    protected function resolveSpatialSchemaGrammar()
    {
        $connections = [
            'mysql' => [
                'schemaGrammar' => MySqlGrammar::class,
            ],
            'pgsql' => [
                'schemaGrammar' => PostgisGrammar::class,
                'schemaColumnDefinition' => PostgisSchemaColumnDefinitionEventSubscriber::class,
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
                if ($this->isDbal($connection)) {
                    try {
                        $this->registerGeometryTypes($connection);
                    } catch (\Throwable $e) {
                        Log::error("SpatialServiceProvider: $e");
                    }
                }

                return $connection;
            });
        }
    }
}
