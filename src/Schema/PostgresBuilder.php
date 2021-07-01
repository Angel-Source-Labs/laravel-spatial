<?php

namespace AngelSourceLabs\LaravelSpatial\Schema;


class PostgresBuilder extends \Illuminate\Database\Schema\PostgresBuilder
{
    use CreatesSpatialBlueprint;
}
