<?php

namespace Tests\Integration;

use Illuminate\Support\Facades\DB;

trait TestsSchemaBuilder
{
    public $expectedSchemaBuilder = null;
    public function setExpectedSchemaBuilder($classname)
    {
        $this->expectedSchemaBuilder = $classname;
    }

    public function testGetSchemaBuilder()
    {
        $builder = DB::connection()->getSchemaBuilder();

        $this->assertNotNull($this->expectedSchemaBuilder ?? null,
            '$this->expectedSchemaBuilder is null.  Set $this->setExpectedSchemaBuilder to set the expected subclass of Illuminate\Database\Schema\Builder');

        $this->assertInstanceOf($this->expectedSchemaBuilder, $builder);
    }
}
