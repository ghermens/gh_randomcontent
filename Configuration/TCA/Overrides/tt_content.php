<?php
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['gh_randomcontent_pi1']='layout,select_key';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    array(
        'LLL:EXT:gh_randomcontent/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi1',
        'gh_randomcontent_pi1'
    ),
    'list_type',
    'gh_randomcontent'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['gh_randomcontent_pi1']='pages,layout,select_key,recursive';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['gh_randomcontent_pi1']='pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'gh_randomcontent_pi1',
    'FILE:EXT:gh_randomcontent/Configuration/FlexForm/flexform_ds.xml'
);
