<?php

namespace AngelSourceLabs\LaravelSpatial\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class MultiLineString extends Type
{
    const MULTILINESTRING = 'multilinestring';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'multilinestring';
    }

    public function getName()
    {
        return self::MULTILINESTRING;
    }
}
