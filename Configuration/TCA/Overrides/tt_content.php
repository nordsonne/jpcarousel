<?php
defined('TYPO3_MODE') or die();

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['jpcarousel_pi1']='layout,select_key';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    [
        'LLL:EXT:jpcarousel/locallang_db.xml:tt_content.list_type_pi1',
        'jpcarousel_pi1',
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('jpcarousel') . 'ext_icon.gif'
    ],
    'list_type',
    'jpcarousel'
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'jpcarousel',
    'static/jpcarousel/',
    'jpcarousel'
);

// remove Layout, code en starting point in flexform
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['jpcarousel_pi1']='layout,select_key,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['jpcarousel_pi1']='pi_flexform';

// de flexform xml gebruiken voor layout.
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'jpcarousel_pi1',
    'FILE:EXT:jpcarousel/flexform_ds_pi1.xml'
);
