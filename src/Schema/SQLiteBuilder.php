<?php

namespace AngelSourceLabs\LaravelSpatial\Schema;


class SQLiteBuilder extends \Illuminate\Database\Schema\SQLiteBuilder
{
    use CreatesSpatialBlueprint;
}
