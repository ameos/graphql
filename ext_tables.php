<?php
defined('TYPO3_MODE') || die();

call_user_func(
    function () use ($_EXTKEY) {
        if (TYPO3_MODE == 'BE' && !\TYPO3\CMS\Core\Core\Environment::getContext()->isProduction()) {
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'Ameos.' . $_EXTKEY,
                'web',
                'graphiql',
                'bottom',
                ['Graphiql' => 'container, index'],
                [
                    'access' => 'user, group',
                    'icon'   => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/graphiql.png',
                    'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_graphiql.xlf'
                ]
            );
        }
    }
);
