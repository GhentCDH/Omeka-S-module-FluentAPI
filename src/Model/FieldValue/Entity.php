<?php

namespace FluentAPI\Model\FieldValue;

use FluentAPI\Model\FieldValue;

class Entity extends FieldValue
{
    public const TYPE_RESOURCE = 'resource';
    public const TYPE_ITEM = 'resource:item';
    public const TYPE_ITEM_SET = 'resource:itemset';


    public function __construct(string $term, string $id, string $type = self::TYPE_RESOURCE, ?bool $isPublic = null)
    {
        parent::__construct(
            $term,
            null,
            $type,
            null,
            null,
            null,
            null,
            $id,
            $isPublic,
        );
    }
}
