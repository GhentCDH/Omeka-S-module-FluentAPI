<?php

namespace FluentAPI\Model\FieldValue;

use FluentAPI\Model\FieldValue;

class Url extends FieldValue
{
    public function __construct(string $term, string $label, string $url, string $language = null, ?bool $isPublic = null)
    {
        parent::__construct(
            $term,
            null,
            'uri',
            $url,
            $label,
            null,
            $language,
            null,
            $isPublic
        );
    }
}
