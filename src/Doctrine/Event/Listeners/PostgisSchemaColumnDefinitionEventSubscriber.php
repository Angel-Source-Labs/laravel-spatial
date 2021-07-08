<?php


namespace AngelSourceLabs\LaravelSpatial\Doctrine\Event\Listeners;


use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\SchemaColumnDefinitionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;

class PostgisSchemaColumnDefinitionEventSubscriber implements EventSubscriber
{

    public function onSchemaColumnDefinition(SchemaColumnDefinitionEventArgs $args)
    {
        $platform = $args->getConnection()->getDatabasePlatform();
        if (! ($platform instanceof PostgreSQL100Platform)) return;

        $tableColumn = $args->getTableColumn();
        if ($tableColumn['type'] != 'geography') return;

        preg_match('/geography\((.*),(.*)\)/', $tableColumn['complete_type'], $matches, PREG_OFFSET_CAPTURE);
        $type = strtolower($matches[1][0]);

        $options = [
            'notnull'       => (bool) $tableColumn['isnotnull'],
            'default'       => $tableColumn['default'],
            'comment'       => isset($tableColumn['comment']) && $tableColumn['comment'] !== ''
                ? $tableColumn['comment']
                : null,
        ];

        $column = new Column($tableColumn['field'], Type::getType($type), $options);
        $args->setColumn($column);
        $args->preventDefault();
        $srid = $matches[2][0];
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [Events::onSchemaColumnDefinition];
    }
}