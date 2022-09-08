<?php
$EM_CONF['gh_randomcontent'] = [
    'title' => 'GH Random Content',
    'description' => 'This frontend plugin shows random content elements from selected page(s).',
    'author' => 'Gregor Hermens',
    'author_email' => 'gregor.hermens@a-mazing.de',
    'author_company' => '@mazing',
    'category' => 'plugin',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '11.0.0-11.99.99',
                ],
            'conflicts' =>
                [],
            'suggests' =>
                [],
        ],
    'autoload' => [
        'psr-4' => [
            'Amazing\\GhRandomcontent\\' => 'Classes',
        ],
    ],
    'version' => '1.1.1',
];
