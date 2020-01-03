<?php
return [
    'frontend' => [
        'ameos/graphql/graphql' => [
            'target' => \Ameos\Graphql\Middleware\GraphQLHandler::class,
            'before' => [
                'typo3/cms-frontend/site-resolver',
            ]
        ],
    ]
];
