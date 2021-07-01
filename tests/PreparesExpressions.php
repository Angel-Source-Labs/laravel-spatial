<?php


namespace Tests;


use AngelSourceLabs\LaravelExpressions\Database\Query\Expression\Grammar;
use Illuminate\Support\Facades\DB;

trait PreparesExpressions
{
    public function prepareExpressions($expression)
    {
        collect($expression)->each(function ($item) {
            $item->getValue() instanceof Grammar ? $item->getValue()->driver(DB::connection()->getDriverName()) : 0;
        });
    }
}