<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "jpcarousel".
 *
 * Auto generated 11-03-2015 11:23
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] =  [
    'title' => 'jpCarousel',
    'description' => 'Carousel or slider based on carouFredSel. For images (photo gallery / slideshow), content elements and records of extensions like tt_news. Optional lightbox with PMKshadowbox, RZcolorbox, PerfectLightbox or SK_Fancybox. Optional T3jQuery integration.',
    'category' => 'plugin',
    'version' => '2.1.0',
    'state' => 'stable',
    'uploadfolder' => 1,
    'createDirs' => '',
    'clearcacheonload' => 0,
    'author' => 'Jacco van der Post',
    'author_email' => 'jacco@typo3-webdesign.nl',
    'author_company' => '',
    'constraints' =>
     [
        'depends' =>
         [
            'extbase' => '1.3-0.0.0',
            'fluid' => '1.3-0.0.0',
            'typo3' => '4.5.0-0.0.0',
        ],
        'conflicts' =>
         [
        ],
        'suggests' =>
         [
        ],
    ],
];
