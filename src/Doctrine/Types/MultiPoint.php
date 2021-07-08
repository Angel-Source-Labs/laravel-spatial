<?php

namespace AngelSourceLabs\LaravelSpatial\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class MultiPoint extends Type
{
    const MULTIPOINT = 'multipoint';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'multipoint';
    }

    public function getName()
    {
        return self::MULTIPOINT;
    }
}
