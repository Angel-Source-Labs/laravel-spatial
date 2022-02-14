<?php

namespace Tests\Integration\Mysql;

use AngelSourceLabs\LaravelSpatial\Schema\MySqlBuilder;
use Illuminate\Support\Facades\DB;
use Tests\Integration\IntegrationBaseTestCase;

trait TestsSchemaBuilder
{
    public function schemaBuilder()
    {
        return null;
    }

    public function testGetSchemaBuilder()
    {
        $builder = DB::connection()->getSchemaBuilder();

        $this->assertNotNull($this->schemaBuilder(),
            '$this->schemaBuilder() is null.  Implement $this->schemaBuilder() to return classname of expected subclass of Illuminate\Database\Schema\Builder');

        $this->assertInstanceOf($this->schemaBuilder(), $builder);
    }
}
