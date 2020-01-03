<?php
declare(strict_types = 1);
namespace Ameos\Graphql\Resolver;

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

abstract class AbstractResolver implements ResolverInterface
{
    /**
     * constructor
     * @param array $configuration
     */
    public function __construct(?array $configuration)
    {
        foreach ($configuration as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Resolve query
     *
     * @param  array $arguments
     * @return array
     */
    abstract public function resolve($source, $arguments, $context, $info): array;
}
