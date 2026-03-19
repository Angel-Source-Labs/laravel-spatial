<?php

namespace AngelSourceLabs\LaravelSpatial\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class MultiPolygon extends Type
{
    const MULTIPOLYGON = 'multipolygon';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'multipolygon';
    }

    public function getName()
    {
        return self::MULTIPOLYGON;
    }
}
