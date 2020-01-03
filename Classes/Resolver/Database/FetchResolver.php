<?php
declare(strict_types = 1);
namespace Ameos\Graphql\Resolver\Database;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use Ameos\Graphql\Resolver\AbstractResolver;

class FetchResolver extends AbstractResolver
{
    /**
     * @var string $table
     */
    protected $table;

    /**
     * Resolve query
     *
     * @param  array $arguments
     * @return array
     */
    public function resolve($source, $arguments, $context, $info): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->table);
        return $queryBuilder
            ->select('*', 'uid as id')
            ->from($this->table)
            ->where($queryBuilder->expr()->eq('uid', (int)$arguments['id']))
            ->execute()
            ->fetch();
    }
}
