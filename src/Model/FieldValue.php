<?php

namespace FluentAPI\Model;

use FluentAPI\Model\FieldValue\Literal;

class FieldValue implements ValueInterface
{
    private string $term;
    private string|int|null $propertyId;
    private ?string $type;
    private ?string $id;
    private ?string $label;
    private ?string $value;
    private ?string $language;
    private ?string $resourceId;
    private ?bool $isPublic;
    /**
     * @var array<string,FieldValue[]> $valueAnnotations
     */
    private array $valueAnnotations = [];

    public function __construct(
        string $term,
        string|int|null $propertyId = null,
        ?string $type = null,
        ?string $id = null,
        ?string $label = null,
        ?string $value = null,
        ?string $language = null,
        ?string $resourceId = null,
        ?bool $isPublic = null,
    ) {
        $this->term = $term;
        $this->propertyId = $propertyId;
        $this->type = $type;
        $this->id = $id;
        $this->label = $label;
        $this->value = $value;
        $this->language = $language;
        $this->resourceId = $resourceId;
        $this->isPublic = $isPublic;
    }

    public static function fromPost(string $property, array $value): static
    {
        $fieldValue = new static(
            $property,
            $value['property_id'],
            $value['type'],
            $value['@id'] ?? null,
            $value['property_label'] ?? null,
            $value['@value'] ?? null,
            $value['@language'] ?? null,
            $value['value_resource_id'] ?? null,
            $value['is_public'] ?? null
        );

        foreach($value['@annotation'] ?? [] as $annotationTerm => $annotationValues) {
            foreach($annotationValues as $annotationValue) {
                if (is_array($annotationValue) && isset($annotationValue['property_id'])) {
                    $fieldValue->addValueAnnotation($annotationTerm, FieldValue::fromPost($annotationTerm, $annotationValue) );
                }
            }
        }
        return $fieldValue;
    }

    public static function literalsFromRdf(string $term, string $label, $rdf): array
    {
        if (!$rdf) {
            return [];
        }

        if (is_string($rdf)) {
            return [new Literal($term, $label, $rdf)];
        }

        if (isset($rdf['@language']) && isset($rdf['@value'])) {
            return [
                new Literal(
                    $term,
                    $label,
                    $rdf['@value'],
                    $rdf['@language']
                )
            ];
        }

        $literals = [];
        foreach ($rdf as $value) {
            if (isset($value['@language']) && isset($value['@value'])) {
                $literals[] = new Literal(
                    $term,
                    $label,
                    $value['@value'],
                    $value['@language']
                );
            }
        }

        return $literals;
    }

    public function setPropertyId(int|string $id): void
    {
        $this->propertyId = $id;
    }

    public function getTerm(): string
    {
        return $this->term;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value ?? '';
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getValueAnnotations(): array
    {
        return $this->valueAnnotations;
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function addValueAnnotation(string $term, ValueInterface $annotation): static
    {
        $this->valueAnnotations[$term] = $this->valueAnnotations[$term] ?? [];
        $this->valueAnnotations[$term][] = $annotation;

        return $this;
    }

    public function export(): array
    {
        $data = [];

        if ($this->propertyId) {
            $data['property_id'] = $this->propertyId === 'auto' ? 'auto' : (int) $this->propertyId;
        }

        if ($this->type) {
            $data['type'] = $this->type;
        }

        if ($this->id) {
            $data['@id'] = $this->id;
        }

        if ($this->resourceId) {
            $data['value_resource_id'] = (int)$this->resourceId;
        }

        if ($this->language || $this->type === 'literal') {
            $data['@language'] = $this->language ?? '';
        }

        if ($this->value) {
            $data['@value'] = $this->value;
        }

        if ($this->isPublic !== null) {
            $data['is_public'] = $this->isPublic ? '1' : '0';
        }

        if ($this->label) {
            $data['property_label'] = $this->label;
        }

        if ($this->valueAnnotations) {
            foreach($this->valueAnnotations as $term => $values) {
                $data['@annotation'][$term] = array_map(fn($value) => $value->export(), $values);
            }
        }

        return $data;
    }
}
