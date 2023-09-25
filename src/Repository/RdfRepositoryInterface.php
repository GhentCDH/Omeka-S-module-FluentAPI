<?php
namespace FluentAPI\Repository;

interface RdfRepositoryInterface extends RepositoryInterface
{
    public static function getResourceType(): string;

    public function any(string $value): static;
    public function fullText(string $value): static;

    public function property(string $property, string $value, string $searchType, string $joiner) : static;
    public function resourceTemplateId(int $id): static;
    public function resourceClassId(int $id): static;
    public function resourceClassLabel(string $label): static;
    public function ownerId(int $id): static;
    public function isPublic(bool $state): static;
}