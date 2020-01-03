<?php

/**
 * Global Solr Connection Settings
 */
$GLOBALS['SiteConfiguration']['site']['columns']['graphql_route'] = [
    'label' => 'GraphQL route',
    'config' => [
        'type' => 'input',
        'default' => '/graphql',
        'placeholder' => '/graphql',
        'size' => 50
    ],
];

$GLOBALS['SiteConfiguration']['site']['columns']['graphql_schema'] = [
    'label' => 'GraphQL schema',
    'config' => [
        'type' => 'input',
        'default' => '',
        'placeholder' => 'EXT:packagesite/Configuration/GraphQL/schema.yaml',
        'size' => 50
    ],
];

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= ',--div--;GraphQL,graphql_route,graphql_schema';
