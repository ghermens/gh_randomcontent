<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'GH Random Content',
    'description' => 'This frontend plugin shows random content elements from selected page(s).',
    'author' => 'Gregor Hermens',
    'author_email' => 'gregor.hermens@a-mazing.de',
    'author_company' => '@mazing',
    'category' => 'plugin',
    'state' => 'stable',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '8.7.0-9.5.99',
                ],
            'conflicts' =>
                [],
            'suggests' =>
                [],
        ],
    'version' => '0.9.1',
];
