<?php
declare(strict_types = 1);
namespace Ameos\Graphql\Controller;

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
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Messaging\AbstractMessage;

class GraphiqlController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var SiteFinder
     */
    protected $siteFinder;

    /**
     * @param SiteFinder $siteFinder
     */
    public function __construct(SiteFinder $siteFinder)
    {
        $this->siteFinder = $siteFinder;
    }

    /**
     * index action
     *
     * @return void
     */
    protected function indexAction(): void
    {
        $site = $this->siteFinder->getSiteByPageId((int)GeneralUtility::_GP('id'));
        $this->view->assign('route', $site->getConfiguration()['graphql_route']);

        echo $this->view->render();
        exit;
    }

    /**
     * container action
     *
     * @return void
     */
    protected function containerAction(): void
    {
        $error = false;
        try {
            $site = $this->siteFinder->getSiteByPageId((int)GeneralUtility::_GP('id'));
        } catch (\Exception $e) {
            $this->addFlashMessage('Site configuration not accessible', '', AbstractMessage::ERROR);
            $error = true;
        }
        $this->view->assign('error', $error);
    }
}
