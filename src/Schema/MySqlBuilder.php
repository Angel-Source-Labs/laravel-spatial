<?php

namespace AngelSourceLabs\LaravelSpatial\Schema;


class MySqlBuilder extends \Illuminate\Database\Schema\MySqlBuilder
{
    use CreatesSpatialBlueprint;
}
