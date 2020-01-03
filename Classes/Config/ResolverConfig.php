<?php
declare(strict_types = 1);
namespace Ameos\Graphql\Config;

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
use Ameos\Graphql\Resolver\ResolverInterface;

class ResolverConfig
{
    /**
     * @var array $aliases
     */
    protected static $aliases = [
        'graphql.test' => \Ameos\Graphql\Resolver\TestResolver::class,
        'graphql.database.fetch' => \Ameos\Graphql\Resolver\Database\FetchResolver::class,
        'graphql.database.fetchAll' => \Ameos\Graphql\Resolver\Database\FetchAllResolver::class,
    ];

    /**
     * register resolvers
     *
     * @param string $alias
     * @param string $className
     */
    public static function register(string $alias, string $className): void
    {
        if (is_a($className, ResolverInterface::class)) {
            static::$resolvers[$alias] = $className;
        } else {
            throw new \Exception(
                sprintf(
                    'Resolver %s must implement interface %s',
                    $className,
                    ResolverInterface::class
                )
            );
        }
    }

    /**
     * return resolver instance
     *
     * @param string $identifier
     * @return Ameos\Graphql\Resolver\ResolverInterface
     */
    public static function get(string $identifier): ResolverInterface
    {
        $configuration = [];
        if (preg_match('/(.*)\((.*)\)$/', $identifier, $matches)) {
            $identifier = $matches[1];
            $configuration = json_decode($matches[2], true);
        }
        if (isset(static::$aliases[$identifier])) {
            return GeneralUtility::makeInstance(static::$aliases[$identifier], $configuration);
        }
        if (class_exists($identifier)) {
            return GeneralUtility::makeInstance($identifier, $configuration);
        }
        throw new \Exception($identifier . ' does not exists');
    }
}
