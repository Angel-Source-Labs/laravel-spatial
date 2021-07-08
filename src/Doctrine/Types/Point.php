<?php

namespace AngelSourceLabs\LaravelSpatial\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class Point extends Type
{
    const POINT = 'point';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'point';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return \AngelSourceLabs\LaravelSpatial\Types\Point::fromWKB($value);
    }

    /**
     * @param \AngelSourceLabs\LaravelSpatial\Types\Point $value
     * @param AbstractPlatform $platform
     * @return mixed|void
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->getValue();
    }

    public function getName()
    {
        return self::POINT;
    }
}
