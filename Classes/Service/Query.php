<?php
declare(strict_types = 1);
namespace Ameos\Graphql\Service;

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
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Ameos\Graphql\Config\SchemaConfig;

class Query
{
    /**
     * @var SchemaConfig $schema
     */
    protected $schema;

    /**
     * @var string $query
     */
    protected $query;

    /**
     * @var string $variables
     */
    protected $variables;

    /**
     * @var string $rootValue
     */
    protected $rootValue;

    /**
     * constructor
     *
     * @param SchemaConfig $schema
     * @param string $query
     * @param string $variables
     * @param string $rootValue
     */
    public function __construct(
        SchemaConfig $schema,
        ?string $query,
        string $variables = null,
        string $rootValue = null
    ) {
        $this->schema = $schema;
        $this->query = $query;
        $this->variables = $variables;
        $this->rootValue = $rootValue;
    }

    /**
     * execute graphql query
     *
     * @return array
     */
    public function run(): array
    {
        return
            GraphQL::executeQuery(
                $this->schema->get(),
                $this->query,
                $this->rootValue,
                null,
                $this->variables
            )
            ->toArray();
    }
}
