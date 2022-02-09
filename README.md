# Spatial: GIS for Laravel

[![license](https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square)](LICENSE)

Laravel package to easily work with GIS data types in PostGIS, MySQL 5.7, and MySQL 8.

[MySQL Spatial Data Types](https://dev.mysql.com/doc/refman/8.0/en/spatial-type-overview.html) and [MySQL Spatial Functions](https://dev.mysql.com/doc/refman/8.0/en/spatial-function-reference.html).

## Supported Compatibiltiy
### Laravel
This package is tested against the following Laravel versions:
* Laravel 6.x
* Laravel 7.x
* Laravel 8.x
* Laravel 9.x

### Databases
This package is tested against the following Databases
* PostGIS
* MySQL 5.7
* MySQL 8.x

#### Future
Support for these databases may be available in a future release.  This package has been designed to support these databases but the work is not complete.
* SQLServer
* SQLite

## History and Motivation
We really like the [grimzy/laravel-mysql-spatial](https://github.com/grimzy/laravel-mysql-spatial) Laravel Eloquent API and we wanted to also be able to use Postgis.  (See [issue 137](https://github.com/grimzy/laravel-mysql-spatial/issues/137)).
The goal of this package is to provide an API compatible with the [grimzy/laravel-mysql-spatial](https://github.com/grimzy/laravel-mysql-spatial) package that also supports postgis and additional database drivers.

This package is a fork and substantial refactoring of `grimzy/laravel-mysql-spatial`:
* refactored to use `laravel-expressions` to provide database compatibility across postgis, mysql 8, and mysql 5.7 
* refactored to use `orchestra/testbench` in PHPUnit tests
* PHPUnit tests updated to use PHPUnit 9.x versus PHPUnit 6.x

Historically, `grimzy/laravel-mysql-spatial` was itself a fork of `njbarrett/laravel-postgis`, which is now `mstaack/laravel-postgis`.  These `laravel-postgis` pacakges provide access to postgis but do not provide the Laravel Eloquent Spatial analysis functions which were added by `grimzy/laravel-mysql-spatial`.
* March 2015: [phaza/laravel-postgres](https://github.com/phaza/laravel-postgres) Peter Haza
  * May 2016: [njbarrett/laravel-postgis](njbarrett/laravel-postgis) Nick Barrett
    * March 2017: [grimzy/laravel-mysql-spatial](https://github.com/grimzy/laravel-mysql-spatial) Joseph Estefane
      * Feb 2022: (this package) [Angel-Source-Labs/laravel-spatial](https://github.com/Angel-Source-Labs/laravel-spatial) Brion Finlay
    * March 2020: [mstaack/laravel-postgis](https://github.com/mstaack/laravel-postgis) Max Staack

**TODO: fork?**
    
## Installation

Add the package using composer:

```sh
$ composer require angel-source-labs/laravel-spatial
```

## Quickstart

### Create a migration

From the command line:

```shell
php artisan make:migration create_places_table
```

Then edit the migration you just created by adding at least one spatial data field. 

**TODO: examples for postgis with SRID and geometry vs geography**

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

// For Laravel < 5.5
// use Grimzy\LaravelMysqlSpatial\Schema\Blueprint;

class CreatePlacesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name')->unique();
            // Add a Point spatial data field named location
            $table->point('location')->nullable();
            // Add a Polygon spatial data field named area
            $table->polygon('area')->nullable();
            $table->timestamps();
        });
  
        // Or create the spatial fields with an SRID (e.g. 4326 WGS84 spheroid)
  
        // Schema::create('places', function(Blueprint $table)
        // {
        //     $table->increments('id');
        //     $table->string('name')->unique();
        //     // Add a Point spatial data field named location with SRID 4326
        //     $table->point('location', 4326)->nullable();
        //     // Add a Polygon spatial data field named area with SRID 4326
        //     $table->polygon('area', 4326)->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('places');
    }
}
```

Run the migration:

```shell
php artisan migrate
```

### Create a model

From the command line:

```shell
php artisan make:model Place
```

Then edit the model you just created. It must use the `SpatialTrait` and define an array called `$spatialFields` with the name of the spatial data field(s) created in the migration:

```php
namespace App;

use AngelSourceLabs\LaravelSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \AngelSourceLabs\LaravelSpatial\Types\Point   $location
 * @property \AngelSourceLabs\LaravelSpatial\Types\Polygon $area
 */
class Place extends Model
{
    use SpatialTrait;

    protected $fillable = [
        'name'
    ];

    protected $spatialFields = [
        'location',
        'area'
    ];
}
```

### Saving a model

```php


use AngelSourceLabs\LaravelSpatial\Types\LineString;
use AngelSourceLabs\LaravelSpatial\Types\Point;
use AngelSourceLabs\LaravelSpatial\Types\Polygon;

$place1 = new Place();
$place1->name = 'Empire State Building';

// saving a point
$place1->location = new Point(40.7484404, -73.9878441);	// (lat, lng)
$place1->save();

// saving a polygon
$place1->area = new Polygon([new LineString([
    new Point(40.74894149554006, -73.98615270853043),
    new Point(40.74848633046773, -73.98648262023926),
    new Point(40.747925497790725, -73.9851602911949),
    new Point(40.74837050671544, -73.98482501506805),
    new Point(40.74894149554006, -73.98615270853043)
])]);
$place1->save();
```

Or if your database fields were created with a specific SRID:

```php
use AngelSourceLabs\LaravelSpatial\Types\LineString;
use AngelSourceLabs\LaravelSpatial\Types\Point;
use AngelSourceLabs\LaravelSpatial\Types\Polygon;

$place1 = new Place();
$place1->name = 'Empire State Building';

// saving a point with SRID 4326 (WGS84 spheroid)
$place1->location = new Point(40.7484404, -73.9878441, 4326);	// (lat, lng, srid)
$place1->save();

// saving a polygon with SRID 4326 (WGS84 spheroid)
$place1->area = new Polygon([new LineString([
    new Point(40.74894149554006, -73.98615270853043),
    new Point(40.74848633046773, -73.98648262023926),
    new Point(40.747925497790725, -73.9851602911949),
    new Point(40.74837050671544, -73.98482501506805),
    new Point(40.74894149554006, -73.98615270853043)
])], 4326);
$place1->save();
```

> **Note**: When saving collection Geometries (`LineString`, `Polygon`, `MultiPoint`, `MultiLineString`, and `GeometryCollection`), only the top-most geometry should have an SRID set in the constructor.
>
> In the example above, when creating a `new Polygon()`, we only set the SRID on the `Polygon` and use the default for the `LineString` and the `Point` objects.

### Retrieving a model

```php
$place2 = Place::first();
$lat = $place2->location->getLat();	// 40.7484404
$lng = $place2->location->getLng();	// -73.9878441
```

## Geometry classes

### Available Geometry classes

| Grimzy\LaravelMysqlSpatial\Types                             | OpenGIS Class                                                |
| ------------------------------------------------------------ | ------------------------------------------------------------ |
| `Point($lat, $lng, $srid = 0)`                               | [Point](https://dev.mysql.com/doc/refman/8.0/en/gis-class-point.html) |
| `MultiPoint(Point[], $srid = 0)`                             | [MultiPoint](https://dev.mysql.com/doc/refman/8.0/en/gis-class-multipoint.html) |
| `LineString(Point[], $srid = 0)`                             | [LineString](https://dev.mysql.com/doc/refman/8.0/en/gis-class-linestring.html) |
| `MultiLineString(LineString[], $srid = 0)`                   | [MultiLineString](https://dev.mysql.com/doc/refman/8.0/en/gis-class-multilinestring.html) |
| `Polygon(LineString[], $srid = 0)` *([exterior and interior boundaries](https://dev.mysql.com/doc/refman/8.0/en/gis-class-polygon.html))* | [Polygon](https://dev.mysql.com/doc/refman/8.0/en/gis-class-polygon.html) |
| `MultiPolygon(Polygon[], $srid = 0)`                         | [MultiPolygon](https://dev.mysql.com/doc/refman/8.0/en/gis-class-multipolygon.html) |
| `GeometryCollection(Geometry[], $srid = 0)`                  | [GeometryCollection](https://dev.mysql.com/doc/refman/8.0/en/gis-class-geometrycollection.html) |

Check out the [Class diagram](https://user-images.githubusercontent.com/1837678/30788608-a5afd894-a16c-11e7-9a51-0a08b331d4c4.png).

### Using Geometry classes

In order for your Eloquent Model to handle the Geometry classes, it must use the `AngelSourceLabs\LaravelSpatial\Eloquent\SpatialTrait` trait and define a `protected` property `$spatialFields`  as an array of spatial data type column names (example in [Quickstart](#user-content-create-a-model)).

#### IteratorAggregate and ArrayAccess

The collection Geometries (`LineString`, `Polygon`, `MultiPoint`, `MultiLineString`, and `GeometryCollection`) implement [`IteratorAggregate`](http://php.net/manual/en/class.iteratoraggregate.php) and [`ArrayAccess`](http://php.net/manual/en/class.arrayaccess.php); making it easy to perform Iterator and Array operations. For example:

```php
$polygon = $multipolygon[10];	// ArrayAccess

// IteratorAggregate
for($polygon as $i => $linestring) {
  echo (string) $linestring;
}

```

#### Helpers

##### From/To Well Known Text ([WKT](https://dev.mysql.com/doc/refman/8.0/en/gis-data-formats.html#gis-wkt-format))

```php
// fromWKT($wkt, $srid = 0)
$point = Point::fromWKT('POINT(2 1)');
$point->toWKT();	// POINT(2 1)

$polygon = Polygon::fromWKT('POLYGON((0 0,4 0,4 4,0 4,0 0),(1 1, 2 1, 2 2, 1 2,1 1))');
$polygon->toWKT();	// POLYGON((0 0,4 0,4 4,0 4,0 0),(1 1, 2 1, 2 2, 1 2,1 1))
```

##### From/To String

```php
// fromString($wkt, $srid = 0)
$point = new Point(1, 2);	// lat, lng
(string)$point			// lng, lat: 2 1

$polygon = Polygon::fromString('(0 0,4 0,4 4,0 4,0 0),(1 1, 2 1, 2 2, 1 2,1 1)');
(string)$polygon;	// (0 0,4 0,4 4,0 4,0 0),(1 1, 2 1, 2 2, 1 2,1 1)
```

##### From/To JSON ([GeoJSON](http://geojson.org/))

The Geometry classes implement [`JsonSerializable`](http://php.net/manual/en/class.jsonserializable.php) and `Illuminate\Contracts\Support\Jsonable` to help serialize into GeoJSON:

```php
$point = new Point(40.7484404, -73.9878441);

json_encode($point); // or $point->toJson();

// {
//   "type": "Feature",
//   "properties": {},
//   "geometry": {
//     "type": "Point",
//     "coordinates": [
//       -73.9878441,
//       40.7484404
//     ]
//   }
// }
```

To deserialize a GeoJSON string into a Geometry class, you can use `Geometry::fromJson($json_string)` :

```php
$location = Geometry::fromJson('{"type":"Point","coordinates":[3.4,1.2]}');
$location instanceof Point::class;  // true
$location->getLat();  // 1.2
$location->getLng()); // 3.4
```

## Scopes: Spatial analysis functions

Spatial analysis functions are implemented using [Eloquent Local Scopes](https://laravel.com/docs/5.4/eloquent#local-scopes).

Available scopes:

- `distance($geometryColumn, $geometry, $distance)`
- `distanceExcludingSelf($geometryColumn, $geometry, $distance)`
- `distanceSphere($geometryColumn, $geometry, $distance)`
- `distanceSphereExcludingSelf($geometryColumn, $geometry, $distance)`
- `comparison($geometryColumn, $geometry, $relationship)`
- `within($geometryColumn, $polygon)`
- `crosses($geometryColumn, $geometry)`
- `contains($geometryColumn, $geometry)`
- `disjoint($geometryColumn, $geometry)`
- `equals($geometryColumn, $geometry)`
- `intersects($geometryColumn, $geometry)`
- `overlaps($geometryColumn, $geometry)`
- `doesTouch($geometryColumn, $geometry)`
- `orderBySpatial($geometryColumn, $geometry, $orderFunction, $direction = 'asc')`
- `orderByDistance($geometryColumn, $geometry, $direction = 'asc')`
- `orderByDistanceSphere($geometryColumn, $geometry, $direction = 'asc')`

**TODO: update with postgis**

*Note that behavior and availability of spatial analysis functions differs in each database and database version (cf. [documentation](https://dev.mysql.com/doc/refman/8.0/en/spatial-function-reference.html)).*

## Migrations

For Laravel versions prior to 5.5, you can use the Blueprint provided with this package: `Grimzy\LaravelMysqlSpatial\Schema\Blueprint`.

```php
use AngelSourceLabs\LaravelSpatial\Schema\SpatialBlueprint;
use Illuminate\Database\Migrations\Migration;


class CreatePlacesTable extends Migration {
    // ...
}
```

### Columns

**TODO: update with postgis generic**

Available [MySQL Spatial Types](https://dev.mysql.com/doc/refman/8.0/en/spatial-type-overview.html) migration blueprints:

- `$table->geometry(string $column_name, int $srid = 0)`
- `$table->point(string $column_name, int $srid = 0)`
- `$table->lineString(string $column_name, int $srid = 0)`
- `$table->polygon(string $column_name, int $srid = 0)`
- `$table->multiPoint(string $column_name, int $srid = 0)`
- `$table->multiLineString(string $column_name, int $srid = 0)`
- `$table->multiPolygon(string $column_name, int $srid = 0)`
- `$table->geometryCollection(string $column_name, int $srid = 0)`

### Spatial indexes

You can add or drop spatial indexes in your migrations with the `spatialIndex` and `dropSpatialIndex` blueprints.

- `$table->spatialIndex('column_name')`
- `$table->dropSpatialIndex(['column_name'])` or `$table->dropSpatialIndex('index_name')`

Note about spatial indexes from the [MySQL documentation](https://dev.mysql.com/doc/refman/8.0/en/creating-spatial-indexes.html):

> For [`MyISAM`](https://dev.mysql.com/doc/refman/8.0/en/myisam-storage-engine.html) and (as of MySQL 5.7.5) `InnoDB` tables, MySQL can create spatial indexes using syntax similar to that for creating regular indexes, but using the `SPATIAL` keyword. Columns in spatial indexes must be declared `NOT NULL`.

Also please read this [**important note**](https://laravel.com/docs/5.5/migrations#indexes) regarding Index Lengths in the Laravel 5.6 documentation.

For example, as a follow up to the [Quickstart](#user-content-create-a-migration); from the command line, generate a new migration:

```shell
php artisan make:migration update_places_table
```

Then edit the migration file that you just created:

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // MySQL < 5.7.5: table has to be MyISAM
        // \DB::statement('ALTER TABLE places ENGINE = MyISAM');

        Schema::table('places', function (Blueprint $table) {
            // Make sure point is not nullable
            $table->point('location')->change();
          
            // Add a spatial index on the location field
            $table->spatialIndex('location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('places', function (Blueprint $table) {
            $table->dropSpatialIndex(['location']); // either an array of column names or the index name
        });

        // \DB::statement('ALTER TABLE places ENGINE = InnoDB');

        Schema::table('places', function (Blueprint $table) {
            $table->point('location')->nullable()->change();
        });
    }
}
```

## Tests

```shell
$ composer test
# or 
$ composer test:unit
$ composer test:integration
```

Integration tests require a running MySQL database. If you have Docker installed, you can start easily start one:

```shell
$ make start_db		# starts MySQL 8.0
# or
$ make start_db V=5.7	# starts MySQL 5.7
```

## Contributing

Recommendations and pull request are most welcome! Pull requests with tests are the best! There are still a lot of MySQL spatial functions to implement or creative ways to use spatial functions. 

## Credits

Originally inspired from [njbarrett's Laravel postgis package](https://github.com/njbarrett/laravel-postgis).

