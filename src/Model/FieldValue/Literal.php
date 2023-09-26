<?php

namespace FluentAPI\Model\FieldValue;

use FluentAPI\Model\FieldValue;

class Literal extends FieldValue
{
    public function __construct(string $term, string $label, string $value, string $language = null, ?bool $isPublic = null)
    {
        parent::__construct(
            $term,
            null,
            'literal',
            null,
            $label,
            $value,
            $language,
            null,
            $isPublic
        );
    }
}
