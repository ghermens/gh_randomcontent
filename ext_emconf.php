<?php
$EM_CONF['gh_randomcontent'] = [
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
                    'typo3' => '8.7.0-10.99.99',
                ],
            'conflicts' =>
                [],
            'suggests' =>
                [],
        ],
    'version' => '0.9.1',
];
