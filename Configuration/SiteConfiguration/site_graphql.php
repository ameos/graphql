<?php
/**
 * not implemented in TYPO3 9 :
 * (1/1) #1522494737 RuntimeException
 *
 * Inline relation to other tables not implemented
 */
return [
    'ctrl' => [
        'label' => 'route',
        'title' => 'GraphQL configuration',
    ],
    'type' => [
        '1' => ['showitem' => '--palette--;;general']
    ],
    'palettes' => [
        'general' => ['showitem' => 'route,schema,automatic_configuration']
    ],
    'columns' => [
        'route' => [
            'label' => 'GraphQL route',
            'config' => [
                'type' => 'input',
                'default' => '/graphql',
                'placeholder' => '/graphql',
                'size' => 50
            ],
        ],
        'schema' => [
            'label' => 'GraphQL schema',
            'config' => [
                'type' => 'input',
                'default' => '',
                'placeholder' => 'EXT:packagesite/Configuration/GraphQL/schema.yaml',
                'size' => 50
            ],
        ],
        'automatic_configuration' => [
            'label' => 'GraphQL TCA automatic configuration',
            'config' => [
                'type' => 'select',
                'default' => '',
                'items' => [
                    ['pages', 'pages'],
                    ['tt_content', 'tt_content'],
                ],
                'size' => 10
            ],
        ],
    ],
];
