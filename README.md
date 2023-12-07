# FluentAPI (module for Omeka-S)

This module provides a fluent API for Omeka-S resources. Currently, only items, item sets and media are supported.

__PHP API__

The PHP API in Omeka-S allows you to perform CRUD operations on resources. For each request, you can provide request parameters and options using the `$data` and `$options` method parameters.

```php
// Where $services is Omeka's service locator object.
$api = $services->get('Omeka\ApiManager');
$data = ['fulltext_search' => 'omeka', 'sort_by' => 'dcterms:title', 
            'sort_order' => 'asc', 'is_public' => 1];
$response = $api->search('items', $data);
$content = $response->getContent();
```

There are a few problems with this. First, a developer has to remember the different option keys and allowed values, there is no code completion for this. Second, IDEs can't help you with option validation. 

__Fluent API__

The FluentAPI module tries to solve some of these problems by allowing you to rewrite the above query like this:

```php
// Where $services is Omeka's service locator object.
// create a item repository instance
$repo = $services->get('item_repository');
// build the query
$content = $repo->qb()->fullText('omeka')->isPublic(True)
    ->sortBy('dcterms:title')->sortAsc()
    ->search();
```

Each Omeka request parameter or option is available as chainable method to a repository object.  

