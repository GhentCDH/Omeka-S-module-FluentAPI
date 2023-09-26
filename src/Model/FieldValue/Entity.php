<?php

namespace FluentAPI\Model\FieldValue;

use FluentAPI\Model\FieldValue;

class Entity extends FieldValue
{
    public function __construct(string $term, string $id, string $type = 'resource', ?bool $isPublic = null)
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
