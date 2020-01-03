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

class TestResolver implements ResolverInterface
{
    /**
     * Resolve query
     *
     * @param  array $arguments
     * @return array
     */
    public function resolve($source, $arguments, $context, $info): array
    {
        return ['id' => 5, 'name' => 'test'];
    }
}
