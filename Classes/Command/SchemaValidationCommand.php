<?php
declare(strict_types = 1);
namespace Ameos\Graphql\Command;

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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Ameos\Graphql\Config\SchemaConfig;

class SchemaValidationCommand extends Command
{
    /**
     * @var Site
     */
    protected $site;

    /**
     * Configure the command by defining the name, options and arguments
     */
    public function configure(): void
    {
        $this
            ->setDescription('Schema validation.')
            ->addOption('siteidentifier', 's', InputOption::VALUE_REQUIRED, 'Site identifier')
            ->setHelp('Call it like this: typo3/sysext/core/bin/typo3 graphql:validate-schema');
    }

    /**
     * Initializes the command after the input has been bound and before the input
     * is validated.
     *
     * This is mainly useful when a lot of commands extends one main command
     * where some things need to be initialized based on the input arguments and options.
     *
     * @see InputInterface::bind()
     * @see InputInterface::validate()
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->site = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(SiteFinder::class)
            ->getSiteByIdentifier($input->getOption('siteidentifier'));
    }

    /**
     * Execute scheduler tasks
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Schema validation');

        $configuration = $this->site->getConfiguration();
        $schemaConfig = GeneralUtility::makeInstance(SchemaConfig::class, $configuration['graphql_schema']);

        try {
            $schemaConfig->get()->assertValid();
            $io->success('Schema valid');
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }       
    }
}