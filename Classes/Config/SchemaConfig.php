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
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use Symfony\Component\Yaml\Parser as YamlParser;

class SchemaConfig
{
    /**
     * @var string $schemasPath
     */
    protected $schemasPath;

    /**
     * @var array $types
     */
    protected $types;

    /**
     * @param string $schemasPath
     */
    public function __construct(string $schemasPath)
    {
        $this->schemasPath = $schemasPath;
        $this->types = [];
    }

    /**
     * return schema
     *
     * @param string $schemaPath
     * @return GraphQL\Type\Schema
     */
    public function get(): Schema
    {
        $rawConfiguration = $this->getRawConfiguration();
        foreach ($rawConfiguration as $name => $config) {
            if ($name !== 'Query') {
                $fields = [];
                if (isset($config['fields']) && is_array($config['fields'])) {
                    $fields = $this->getFieldsConfiguration($config);
                }
                if (isset($config['fields']) && $config['fields'] === 'TCA') {
                    $fields = $this->getFieldsFromTCA($config);
                }

                $this->types[$name] = new ObjectType([
                    'name' => $name,
                    'fields' => $fields,
                ]);
            }
        }

        $query = null;
        if (isset($rawConfiguration['Query'])) {
            $query = new ObjectType([
                'name' => 'Query',
                'fields' => $this->getFieldsConfiguration($rawConfiguration['Query']),
            ]);
        }

        return new Schema([
            'query' => $query,
            'types' => $this->types,
        ]);
    }

    /**
     * compile schema files content
     * @return string
     */
    protected function compileSchemasFiles(): string
    {
        $files = [];
        $absolutePath = GeneralUtility::getFileAbsFileName($this->schemasPath);
        if (file_exists($absolutePath) && is_file($absolutePath)) {
            $files[] = $absolutePath;
        } elseif (file_exists($absolutePath) && is_dir($absolutePath)) {
            foreach (glob(rtrim($absolutePath, '/') . '/' . '*.yaml') as $filename) {
                $files[] = $filename;
            }
        } else {
            throw new \Exception('Schema does not exist.');
        }

        $filecontent = '';
        foreach ($files as $file) {
            $filecontent .= file_get_contents($file) . PHP_EOL;
        }
        return $filecontent;
    }

    /**
     * return raw yaml configuration
     * @return array
     */
    protected function getRawConfiguration(): array
    {
        return GeneralUtility::makeInstance(YamlParser::class)->parse($this->compileSchemasFiles());
    }

    /**
     * return schema fields configuration
     * @param array $source
     * @return array
     */
    protected function getFieldsConfiguration(array $source): array
    {
        $declaredTypes = array_keys($this->types);
        $fields = [];
        if ($source['fields']) {
            foreach ($source['fields'] as $field => $config) {
                $fields[$field] = [];
                
                if (preg_match('/^\[(.*)\]$/', $config['type'], $matches)) {
                    $vartype = $matches[1];
                    $listof = true;
                } else {
                    $vartype = $config['type'];
                    $listof = false;
                }
                
                if (in_array($vartype, $declaredTypes)) {
                    $fields[$field]['type'] = $this->types[$vartype];
                } else {
                    switch ($vartype) {
                        case 'Int':
                            $fields[$field]['type'] = Type::int();
                            break;
                        case 'Int!':
                            $fields[$field]['type'] = Type::nonNull(Type::int());
                            break;
                        case 'String':
                            $fields[$field]['type'] = Type::string();
                            break;
                        case 'String!':
                            $fields[$field]['type'] = Type::nonNull(Type::string());
                            break;
                    }
                }

                if ($listof) {
                    $fields[$field]['type'] = Type::listof($fields[$field]['type']);
                }

                if ($config['args']) {
                    $fields[$field]['args'] = [];
                    foreach ($config['args'] as $arg => $argconfig) {
                        $fields[$field]['args'][$arg] = [];
                        switch ($argconfig['type']) {
                            case 'Int':
                                $fields[$field]['args'][$arg]['type'] = Type::int();
                                break;
                            case 'Int!':
                                $fields[$field]['args'][$arg]['type'] = Type::nonNull(Type::int());
                                break;
                            case 'String':
                                $fields[$field]['args'][$arg]['type'] = Type::string();
                                break;
                            case 'String!':
                                $fields[$field]['args'][$arg]['type'] = Type::nonNull(Type::string());
                                break;
                        }
                    }
                }

                if (isset($config['resolve'])) {
                    $resolver = ResolverConfig::get($config['resolve']);
                    $fields[$field]['resolve'] = function (
                        $source,
                        $arguments,
                        $context,
                        ResolveInfo $info
                    ) use ($resolver) {
                        return $resolver->resolve($source, $arguments, $context, $info);
                    };
                }
            }
        }
        return $fields;
    }

    /**
     * return field from TCA
     * @param array $source
     * @return array
     */
    protected function getFieldsFromTCA(array $source): array
    {
        $fields = [];
        $fields['uid'] = ['type' => Type::int()];
        $fields['pid'] = ['type' => Type::int()];
        $tca = $GLOBALS['TCA'][$source['tca']] ?? null;
        if ($tca) {
            foreach ($tca['columns'] as $name => $column) {
                $fields[$name] = ['type' => Type::string()];
            }
        }
        return $fields;
    }
}
