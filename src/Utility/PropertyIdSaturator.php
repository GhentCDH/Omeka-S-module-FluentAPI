<?php

namespace FluentAPI\Utility;

use FluentAPI\Model\FieldValue;
use FluentAPI\Model\ItemRequest;
use LogicException;
use Omeka\Api\Manager;
use Omeka\Api\Representation\ResourceClassRepresentation;
use Omeka\Api\Representation\ResourceTemplateRepresentation;

class PropertyIdSaturator
{

    /**
     * @var Manager
     */
    private Manager $api;

    /**
     * @var array
     */
    private array $propertyIds = [];
    private array $resourceTemplateNames = [];
    private array $resourceClassNames = [];

    /**
     * @var ResourceTemplateRepresentation[]
     */
    private array $resourceTemplateByName = [];

    /**
     * @var ResourceClassRepresentation[]
     */
    private array $resourceClassByName = [];

    /**
     * PropertyIdSaturator constructor.
     * @param Manager $api
     */
    public function __construct(Manager $api)
    {
        $this->api = $api;
    }

    /**
     * @param ItemRequest $request
     */
    public function addResourceIds(ItemRequest $request): void
    {
        // set resource template ID
        $resourceTemplateId = $request->getResourceTemplate();
        if ($resourceTemplateId) {
            $request->setResourceTemplateName(
                $this->loadResourceTemplateName($resourceTemplateId)
            );
        }

        $resourceClassId = $request->getResourceClass();
        if ($resourceClassId) {
            $request->setResourceClassName(
                $this->loadResourceClassName($resourceClassId)
            );
        }
    }

    /**
     * @param string $term
     * @return mixed|ResourceClassRepresentation
     */
    public function getResourceClassByTerm(string $term): ?ResourceClassRepresentation
    {
        if (!isset($this->resourceClassByName[$term])) {
            $response = $this->api
                ->search('resource_classes', [
                    'term' => $term
                ])
                ->getContent();

            if (empty($response)) {
                throw new LogicException("Invalid resource class term {$term}");
            }
            $this->resourceClassByName[$term] = array_pop($response);
        }
        return $this->resourceClassByName[$term];
    }

    /**
     * @param string $name
     * @return mixed|ResourceTemplateRepresentation
     */
    public function getResourceTemplateByName(string $name): ?ResourceTemplateRepresentation
    {
        if (!isset($this->resourceTemplateByName[$name])) {
            $response = $this->api
                ->search('resource_templates', [
                    'label' => $name
                ])
                ->getContent();

            if (empty($response)) {
                throw new LogicException("Invalid resource template name {$name}");
            }
            $this->resourceTemplateByName[$name] = array_pop($response);
        }
        return $this->resourceTemplateByName[$name];
    }

    /**
     * @param string $name
     * @param ItemRequest $itemRequest
     * @return ItemRequest
     */
    public function addResourceTemplateByName(string $name, ItemRequest $itemRequest): ItemRequest
    {
        $resourceTemplate = $this->getResourceTemplateByName($name);
        $itemRequest->setResourceTemplate($resourceTemplate->id());
        $itemRequest->setResourceTemplateName($resourceTemplate->label());
        if ($resourceClass = $resourceTemplate->resourceClass()) {
            $itemRequest->setResourceClass($resourceClass->id());
            $itemRequest->setResourceClassName($resourceClass->label());
        }

        return $itemRequest;
    }

    public function addPropertyIds(ItemRequest $request): void
    {
        $request->eachFieldValues([$this, 'addPropertyId']);
    }

    public function addPropertyId(FieldValue $value): void
    {
        $value->setPropertyId($this->loadPropertyId($value->getTerm()));
    }

    /**
     * @param $id
     * @return string
     */
    public function loadResourceTemplateName($id): string
    {
        if (!isset($this->resourceTemplateNames[$id])) {
            /** @var ResourceTemplateRepresentation $resourceTemplate */
            $resourceTemplate = $this->api
                ->read('resource_templates', $id)
                ->getContent();

            if (empty($resourceTemplate)) {
                throw new LogicException("Invalid resource template {$id}");
            }

            $this->resourceTemplateNames[$id] = $resourceTemplate->label();
            $resourceClass = $resourceTemplate->resourceClass();

            if ($resourceClass) {
                $this->resourceClassNames[$resourceClass->id()] = $resourceClass->label();
            }
        }
        return $this->resourceTemplateNames[$id];
    }

    /**
     * @param $id
     * @return mixed|string
     */
    public function loadResourceClassName($id): string
    {
        if (!isset($this->resourceClassNames[$id])) {
            /** @var ResourceClassRepresentation $resourceClass */
            $resourceClass = $this->api
                ->read('resource_classes', $id)
                ->getContent();

            if (empty($resourceClass)) {
                throw new LogicException("Invalid resource class {$id}");
            }

            $this->resourceClassNames[$id] = $resourceClass->label();
        }
        return $this->resourceClassNames[$id];
    }

    /**
     * @param $term
     * @return mixed
     */
    public function loadPropertyId($term)
    {
        if (!isset($this->propertyIds[$term])) {
            $propertyRepresentationResponse = $this->api
                ->search('properties', [
                    'term' => $term
                ])
                ->getContent();

            if (empty($propertyRepresentationResponse)) {
                throw new LogicException("Invalid term {$term}, you may be missing a vocabulary");
            }

            $propertyRepresentation = array_pop($propertyRepresentationResponse);
            $this->propertyIds[$term] = $propertyRepresentation->id();
        }
        return $this->propertyIds[$term];
    }

}
