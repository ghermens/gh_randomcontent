<?php
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    [
        'label' => 'LLL:EXT:gh_randomcontent/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi1',
        'value' => 'gh_randomcontent_pi1',
        'icon' => 'tx-ghrandomcontent-plugin',
        'group' => 'special',
    ],
    'list_type',
    'gh_randomcontent',
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['gh_randomcontent_pi1']='pages,layout,select_key,recursive';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['gh_randomcontent_pi1']='pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'gh_randomcontent_pi1',
    'FILE:EXT:gh_randomcontent/Configuration/FlexForm/PluginRandomContent.xml'
);
