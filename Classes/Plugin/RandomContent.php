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
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;


/**
 * Plugin 'Random Content' for the 'gh_randomcontent' extension.
 *
 * @author Gregor Hermens <gregor.hermens@a-mazing.de>
 * @package TYPO3
 * @subpackage tx_ghrandomcontent
 */
class RandomContent
{
    public $prefixId = 'tx_ghrandomcontent_pi1'; // Same as class name
    public $scriptRelPath = 'Classes/Plugin/RandomContent.php'; // Path to this script relative to the extension dir.
    public $extKey = 'gh_randomcontent'; // The extension key.
    public $pi_checkCHash = true;

    /**
     * The back-reference to the mother cObj object set at call time
     */
    protected $cObj;

    /**
     * This setter is called when the plugin is called from UserContentObject (USER)
     * via ContentObjectRenderer->callUserFunction().
     *
     * @param ContentObjectRenderer $cObj
     */
    public function setContentObjectRenderer(ContentObjectRenderer $cObj): void
    {
        $this->cObj = $cObj;
    }


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
     * Converts $this->cObj->data['pi_flexform'] from XML string to flexForm array.
     *
     * @param string $field Field name to convert
     */
    public function pi_initPIflexForm($field = 'pi_flexform')
    {
        // Converting flexform data into array:
        if (!is_array($this->cObj->data[$field]) && $this->cObj->data[$field]) {
            $this->cObj->data[$field] = GeneralUtility::xml2array($this->cObj->data[$field]);
            if (!is_array($this->cObj->data[$field])) {
                $this->cObj->data[$field] = [];
            }
        }
    }

    /**
     * Return value from somewhere inside a FlexForm structure
     *
     * @param array $T3FlexForm_array FlexForm data
     * @param string $fieldName Field name to extract. Can be given like "test/el/2/test/el/field_templateObject" where each part will dig a level deeper in the FlexForm data.
     * @param string $sheet Sheet pointer, eg. "sDEF
     * @param string $lang Language pointer, eg. "lDEF
     * @param string $value Value pointer, eg. "vDEF
     * @return string|null The content.
     */
    public function pi_getFFvalue($T3FlexForm_array, $fieldName, $sheet = 'sDEF', $lang = 'lDEF', $value = 'vDEF')
    {
        $sheetArray = is_array($T3FlexForm_array) ? $T3FlexForm_array['data'][$sheet][$lang] : '';
        if (is_array($sheetArray)) {
            return $this->pi_getFFvalueFromSheetArray($sheetArray, explode('/', $fieldName), $value);
        }
        return null;
    }


    /**
     * Returns part of $sheetArray pointed to by the keys in $fieldNameArray
     *
     * @param array $sheetArray Multidimensional array, typically FlexForm contents
     * @param array $fieldNameArr Array where each value points to a key in the FlexForms content - the input array will have the value returned pointed to by these keys. All integer keys will not take their integer counterparts, but rather traverse the current position in the array and return element number X (whether this is right behavior is not settled yet...)
     * @param string $value Value for outermost key, typ. "vDEF" depending on language.
     * @return mixed The value, typ. string.
     * @internal
     * @see pi_getFFvalue()
     */
    public function pi_getFFvalueFromSheetArray($sheetArray, $fieldNameArr, $value)
    {
        $tempArr = $sheetArray;
        foreach ($fieldNameArr as $k => $v) {
            if (MathUtility::canBeInterpretedAsInteger($v)) {
                if (is_array($tempArr)) {
                    $c = 0;
                    foreach ($tempArr as $values) {
                        if ($c == $v) {
                            $tempArr = $values;
                            break;
                        }
                        $c++;
                    }
                }
            } else {
                $tempArr = $tempArr[$v];
            }
        }
        return $tempArr[$value];
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
