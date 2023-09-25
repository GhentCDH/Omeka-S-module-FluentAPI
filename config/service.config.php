<?php
namespace FluentAPI;

use FluentAPI\Utility\PropertyIdSaturator;
use FluentAPI\Repository\ItemRepository;
use FluentAPI\Repository\ItemSetRepository;
use Psr\Container\ContainerInterface;

return [
    'service_manager' => [
        'factories' => [
            /* utilities */
            PropertyIdSaturator::class => function(ContainerInterface $c) {
                return new PropertyIdSaturator($c->get('Omeka\ApiManager'));
            },
            /* repositories */
            ItemSetRepository::class => function (ContainerInterface $c) {
                return new ItemSetRepository(
                    $c->get('Omeka\ApiManager'),
                    $c->get(PropertyIdSaturator::class)
                );
            },
            ItemRepository::class => function (ContainerInterface $c) {
                return new ItemRepository(
                    $c->get('Omeka\ApiManager'),
                    $c->get(PropertyIdSaturator::class)
                );
            },
        ],
    ],
];
