<?php
declare(strict_types=1);

namespace Amazing\GhRandomcontent\Plugin;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2019 Gregor Hermens (gregor.hermens@a-mazing.de)
 *  based on onet_randomcontent (c) 2005 Semyon Vyskubov (poizon@onet.ru)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Plugin\AbstractPlugin;


/**
 * Plugin 'Random Content' for the 'gh_randomcontent' extension.
 *
 * @author Gregor Hermens <gregor.hermens@a-mazing.de>
 * @package TYPO3
 * @subpackage tx_ghrandomcontent
 */
class RandomContent extends AbstractPlugin
{
    public $prefixId = 'tx_ghrandomcontent_pi1'; // Same as class name
    public $scriptRelPath = 'Classes/Plugin/RandomContent.php'; // Path to this script relative to the extension dir.
    public $extKey = 'gh_randomcontent'; // The extension key.
    public $pi_checkCHash = true;

    /**
     * The main method of the PlugIn
     *
     * @param string $content The PlugIn content
     * @param array $conf The PlugIn configuration
     * @return string The content that is displayed on the website
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function main(string $content, array $conf) : string
    {
        $this->init($conf);

        $content_ids = $this->getContentUids();

        if (!count($content_ids)) { // no content available at all
            return '';
        }

        if ($this->conf['count'] > count($content_ids)) {
            $this->conf['count'] = count($content_ids);
        }

        $content_shown = $this->selectContentUIDs($content_ids);

        return $this->renderContent($content_shown, $content_ids);
    }


    /**
     * Initialise this class
     *
     * @param array $conf The PlugIn configuration
     * @return void
     */
    protected function init(array $conf) : void
    {
        $this->conf = $conf;

        $this->pi_initPIflexForm(); // Init FlexForm configuration for plugin
        if ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'which_pages', 'sDEF')) {
            $this->conf['pages'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'which_pages', 'sDEF');
        }

        $this->conf['count'] = (int)$this->conf['count'];
        if ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'count', 'sDEF')) {
            $this->conf['count'] = (int)$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'count', 'sDEF');
        }
        if (empty($this->conf['count'])) {
            $this->conf['count'] = 1;
        }

        if ($this->cObj->data['list_type'] === $this->extKey . '_pi1') { // Override $conf with flexform checkboxes
            if ((int)$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'honor_language', 'sDEF') !== -1) {
                $this->conf['honorLanguage'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'honor_language', 'sDEF');
            }
            if ((int)$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'honor_colpos', 'sDEF') !== -1) {
                $this->conf['honorColPos'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'honor_colpos', 'sDEF');
            }
        }

        if ('' === $this->cObj->data['colPos']) {
            $this->conf['colPos'] = $this->conf['defaultColPos'];
        } else {
            $this->conf['colPos'] = $this->cObj->data['colPos'];
        }
    }

    /**
     * Fetch UID of all available content elements from database
     *
     * @return array List of UIDs and their PIDs
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    protected function getContentUids() : array
    {
        /** @var Context $context */
        $context = GeneralUtility::makeInstance(Context::class);
        $langId = $context->getPropertyFromAspect('language', 'contentId');

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');

        $queryBuilder->select('uid', 'pid')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->in(
                    'pid',
                    $queryBuilder->createNamedParameter(
                        GeneralUtility::intExplode(',', $this->conf['pages'], true),
                        Connection::PARAM_INT_ARRAY
                    )
                )
            );

        if ($this->conf['honorLanguage']) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(
                    'sys_language_uid',
                    $queryBuilder->createNamedParameter(
                        [
                            $langId,
                            -1
                        ],
                        Connection::PARAM_INT_ARRAY
                    )
                )
            );
        }

        if ($this->conf['honorColPos']) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq(
                    'colPos',
                    $queryBuilder->createNamedParameter(
                        $this->conf['colPos'],
                        Connection::PARAM_INT
                    )
                )
            );
        }

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Select the content elements to be shown by random
     *
     * @param array $content_ids List of content element UIDs and their PIDs to select from
     * @return array List of content element UIDs and their PIDs
     */
    protected function selectContentUIDs(array $content_ids = []) : array
    {
        $content_shown = array_rand($content_ids, $this->conf['count']); // choose random content element
        if (1 == $this->conf['count']) {
            $content_shown = [$content_shown];
        } else {
            shuffle($content_shown);
        }

        return $content_shown;
    }

    /**
     * Render selected content elements
     *
     * @param array $content_shown List of content element UIDs to show
     * @param array $content_ids List of all available content element UIDs and their PIDs
     * @return string HTML
     */
    protected function renderContent(array $content_shown = [], array $content_ids = []) : string
    {
        $content = '';
        foreach ($content_shown as $content_uid) {
            // render content element
            $content_conf = [
                'table' => 'tt_content',
                'select.' => [
                    'uidInList' => $content_ids[$content_uid]['uid'],
                    'pidInList' => $content_ids[$content_uid]['pid'],
                    'languageField' => 0,
                ],
            ];

            $element = $this->cObj->cObjGetSingle('CONTENT', $content_conf);

            if (!empty($this->conf['elementWrap.'])) {
                $element = $this->cObj->stdWrap($element, $this->conf['elementWrap.']);
            }
            if (!empty($this->conf['elementWrap'])) {
                $element = $this->cObj->wrap($element, $this->conf['elementWrap']);
            }

            $content .= $element;
        }

        if (!empty($this->conf['allWrap.'])) {
            $content = $this->cObj->stdWrap($content, $this->conf['allWrap.']);
        }
        if (!empty($this->conf['allWrap'])) {
            $content = $this->cObj->wrap($content, $this->conf['allWrap']);
        }

        return $content;
    }
}
