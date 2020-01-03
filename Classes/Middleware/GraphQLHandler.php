<?php
declare(strict_types = 1);
namespace Ameos\Graphql\Middleware;

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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Routing\SiteMatcher;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ameos\Graphql\Service\Query;
use Ameos\Graphql\Config\SchemaConfig;

class GraphQLHandler implements MiddlewareInterface
{
    /**
     * @var SiteMatcher
     */
    protected $matcher;

    public function __construct(SiteMatcher $matcher = null)
    {
        $this->matcher = $matcher ?? GeneralUtility::makeInstance(
            SiteMatcher::class,
            GeneralUtility::makeInstance(SiteFinder::class)
        );
    }

    /**
     * Intercept graphql request
     *
     * @param  ServerRequestInterface  $request
     * @param  RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $configuration = $this->matcher->matchRequest($request)->getSite()->getConfiguration();
        if ($request->getAttribute('normalizedParams')->getRequestUri() === $configuration['graphql_route']) {
            $input = json_decode(file_get_contents('php://input'), true);

            $schemaConfig = GeneralUtility::makeInstance(
                SchemaConfig::class,
                $configuration['graphql_schema']
            );

            $queryHandler = GeneralUtility::makeInstance(
                Query::class,
                $schemaConfig,
                $input['query'],
                $input['variables'] ?? null,
                null // ?
            );
            $queryHandler->run();
            return new JsonResponse($queryHandler->run());
        }
        
        return $handler->handle($request);
    }
}
