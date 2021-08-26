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
        if ($tableColumn['type'] != 'geography' && $tableColumn['type'] != 'geometry') return;

        if ($tableColumn['type'] == 'geography') {
            preg_match('/geography\((.*),(.*)\)/', $tableColumn['complete_type'], $matches, PREG_OFFSET_CAPTURE);
            $type = strtolower($matches[1][0]);
            $srid = (int)$matches[2][0];
        }
        elseif ($tableColumn['type'] == 'geometry') {
            preg_match('/geometry\(([^,]*),?(.+)?\)/', $tableColumn['complete_type'], $matches, PREG_OFFSET_CAPTURE);
            $type = empty($matches) ? 'geometry' : strtolower($matches[1][0]);
            $srid = isset($matches[2]) ? (int)$matches[2][0] : null;
        }

        $options = [
            'notnull'       => (bool) $tableColumn['isnotnull'],
            'default'       => $tableColumn['default'],
            'comment'       => isset($tableColumn['comment']) && $tableColumn['comment'] !== ''
                ? $tableColumn['comment']
                : null,
        ];


        $column = new Column($tableColumn['field'], Type::getType($type), $options);
        if (isset($srid) && $srid > 0)
            $column->setCustomSchemaOption('srid', $srid);
        $column->setCustomSchemaOption('geometryType', $tableColumn['type']);
        $args->setColumn($column);
        $args->preventDefault();
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [Events::onSchemaColumnDefinition];
    }
}