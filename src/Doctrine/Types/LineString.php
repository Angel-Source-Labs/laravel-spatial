<?php

namespace AngelSourceLabs\LaravelSpatial\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class LineString extends Type
{
    const LINESTRING = 'linestring';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'linestring';
    }

    public function getName()
    {
        return self::LINESTRING;
    }
}
