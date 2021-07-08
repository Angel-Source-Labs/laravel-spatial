<?php

namespace AngelSourceLabs\LaravelSpatial\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class GeometryCollection extends Type
{
    const GEOMETRYCOLLECTION = 'geometrycollection';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'geometrycollection';
    }

    public function getName()
    {
        return self::GEOMETRYCOLLECTION;
    }
}
