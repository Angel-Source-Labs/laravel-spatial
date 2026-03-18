<?php

namespace AngelSourceLabs\LaravelSpatial\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class Point extends Type
{
    const POINT = 'point';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return 'point';
    }

    public function getName()
    {
        return self::POINT;
    }
}
