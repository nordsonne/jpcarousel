<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Jacco van der Post <jacco@id-internetservices.com>
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
 * ************************************************************* */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */
//require_once(PATH_tslib . 'class.tslib_pibase.php');

/**
 * Plugin 'jpCarousel' for the 'jpcarousel' extension.
 *
 * @author    Jacco van der Post <jacco@id-internetservices.com>
 */
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Plugin\AbstractPlugin;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class tx_jpcarousel_pi1 extends AbstractPlugin
{
    public $prefixId = 'tx_jpcarousel_pi1'; // Same as class name
    public $scriptRelPath = 'pi1/class.tx_jpcarousel_pi1.php'; // Path to this script relative to the extension dir.
    public $extKey = 'jpcarousel'; // The extension key.
    public $pi_checkCHash = true;

    protected $lConf = '';

    protected $ulStart = '';

    protected $ulEnd = '';

    protected $header = '';

    protected $contentItem = '';

    protected $currentCeID = '';

    protected $carouselID = '';

    /**
     * The main method of the PlugIn
     *
     * @param    string        $content: The PlugIn content
     * @param    array        $conf: The PlugIn configuration
     * @return   string The content that is displayed on the website
     */
    public function main($content, $conf)
    {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        $this->pi_initPIflexForm();

        // Check if extension t3jQuery is loaded
        if (ExtensionManagementUtility::isLoaded('t3jquery')) {
            require_once(ExtensionManagementUtility::extPath('t3jquery') . 'class.tx_t3jquery.php');
        }
        // If t3jQuery is loaded and the custom Library has been created
        if (T3JQUERY === true) {
            tx_t3jquery::addJqJS();
        } else {
            // if none of the previous is true, and path is filled in include own library
            $includeOwnJqueryLib = $this->conf['pathToJquery'] ? true : false;
            //$header = '<script src="'.$GLOBALS['TSFE']->tmpl->getFileName($this->conf['pathToJquery']).'" type="text/javascript"></script>';
        }

        //Create a new ID voor this carousel content element, to allow multiple carousels on a page
        $currentCeID = 'c' . $this->cObj->data['uid'];
        //$carouselID = "jpcarousel".$currentCeID;
        $carouselID = 'jpcarousel';

        // Read the template
        $pathToJPcarouselHTML = $this->conf['templateFile'];
        if (isset($this->lConf['pathToJPcarouselHTML']) && !empty($this->lConf['pathToJPcarouselHTML'])) {
            $pathToJPcarouselHTML = $this->lConf['pathToJPcarouselHTML'];
        }
        $templateHtml = $this->cObj->fileResource($pathToJPcarouselHTML);

        // Extract subparts from the template
        $subparts['template'] = $this->cObj->getSubpart($templateHtml, '###TEMPLATE###');
        $subparts['item'] = $this->cObj->getSubpart($subparts['template'], '###ITEM###');
        $subparts['control'] = $this->cObj->getSubpart($subparts['template'], '###CONTROL###');
        $subparts['containerprops'] = $this->cObj->getSubpart($subparts['template'], '###CONTAINERPROPERTIES###');

        $piFlexForm = $this->cObj->data['pi_flexform'];

        // Read the flexform and throw the data in array lconf
        foreach ($piFlexForm['data'] as $sheet => $data) {
            // echo "<br /><b>".$sheet."</b><br />";
            foreach ($data as $lang => $value) {
                foreach ($value as $key => $val) {
                    $this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
                    //echo $key.":".$this->lConf[$key]."<br />";
                }
            }
        }

        // Set the configuration variables for the carouselscript
        $visible = $this->conf['visible'];
        if ($this->lConf['visible']) {
            $visible = $this->lConf['visible'];
        }

        $direction = $this->conf['direction'];
        if ($this->lConf['direction']) {
            $direction = $this->lConf['direction'];
        }

        $circular = 0; //because checkbox
        if ($this->lConf['circular']) {
            $circular = 1;
        }

        $infinite = 0; //because checkbox
        if ($this->lConf['infinite']) {
            $infinite = 1;
        }

        //echo $this->lConf['circular'].'<br/>';

        $autoPlay = json_encode(false); //because checkbox
        if ($this->lConf['autoPlay']) {
            $autoPlay = json_encode(true);
        }

        $scroll = $this->conf['scroll'];
        if ($this->lConf['scroll']) {
            $scroll = $this->lConf['scroll'];
        }
        //echo 'lconf scroll:'.$this->lConf['scroll'].'<br/>';

        $fx = $this->conf['fx'];
        if ($this->lConf['fx']) {
            $fx = $this->lConf['fx'];
        }

        $easing = $this->conf['easing'];
        if ($this->lConf['easing']) {
            $easing = $this->lConf['easing'];
        }

        $speed = $this->conf['speed'];
        if ($this->lConf['speed']) {
            $speed = $this->lConf['speed'];
        }

        $pauseOnHover = 0; //because checkbox
        if ($this->lConf['pauseOnHover']) {
            $pauseOnHover = $this->lConf['pauseOnHover'];
        }

        $beforeStart = $this->conf['beforeStart'];
        if ($this->lConf['beforeStart']) {
            $beforeStart = $this->lConf['beforeStart'];
        }

        $afterEnd = $this->conf['afterEnd'];
        if ($this->lConf['afterEnd']) {
            $afterEnd = $this->lConf['afterEnd'];
        }

        $pauseDuration = $this->conf['pauseDuration'];
        if ($this->lConf['pauseDuration']) {
            $pauseDuration = $this->lConf['pauseDuration'];
        }

        $buttonsScroll = $this->conf['buttonsScroll'];
        if ($this->lConf['buttonsScroll']) {
            $buttonsScroll = $this->lConf['buttonsScroll'];
        }
        $buttonsEasing = $this->conf['buttonsEasing'];
        if ($this->lConf['buttonsEasing']) {
            $buttonsEasing = $this->lConf['buttonsEasing'];
        }

        $buttonsSpeed = $this->conf['buttonsSpeed'];
        if ($this->lConf['buttonsSpeed']) {
            $buttonsSpeed = $this->lConf['buttonsSpeed'];
        }

        $extraConfig = $this->conf['extraConfig'];
        if ($this->lConf['extraConfig']) {
            $extraConfig = $this->lConf['extraConfig'];
        }

        $extraJs = $this->conf['extraJs'];
        if ($this->lConf['extraJs']) {
            $extraJs = $this->lConf['extraJs'];
        }

        // configure Fred's JS
        $carouFredSelConfig = <<<HEREDOC
         jQuery(document).ready(function() {
            jQuery('#$currentCeID .$carouselID').carouFredSel({
                circular    : $circular,
                infinite    : $infinite,
                direction	: '$direction',
                items       : {
                    visible : $visible
                },
                scroll      : {
                    items           : $scroll,
                    fx              : '$fx',
                    easing          : '$easing',
                    duration	    : $speed,
                    pauseOnHover    : $pauseOnHover,
                    onBefore        : $beforeStart,
                    onAfter         : $afterEnd
                },
                auto : {
                     play           : $autoPlay,
                     pauseDuration  : $pauseDuration
                },
                next : {
                    button          : jQuery('#next$currentCeID'),
                    key             : 'right',
                    items           : $buttonsScroll,
                    easing          : '$buttonsEasing',
                    duration        : $buttonsSpeed
                },
                prev : {
                    button          : jQuery('#prev$currentCeID'),
                    key             : 'left',
                    items           : $buttonsScroll,
                    easing          : '$buttonsEasing',
                    duration        : $buttonsSpeed
                },
                pagination : {
                    container       : jQuery('#pagination$currentCeID'),
                    items           : $buttonsScroll,
                    easing          : '$buttonsEasing',
                    duration        : $buttonsSpeed
                }$extraConfig
            })$extraJs;
         });
HEREDOC;

        // determine width, height of li items. Needs to get included in head CSS, cuz of unordered list of unknown content elements
        $li_width = $this->conf['pic.']['file.']['maxW'];
        if ($this->lConf['itemMaxWidth']) {
            $li_width = $this->lConf['itemMaxWidth'];
        }

        $li_height = $this->conf['pic.']['file.']['maxH'];
        if ($this->lConf['itemMaxHeight']) {
            $li_height = $this->lConf['itemMaxHeight'];
        }

        // Include js + CSS, take the path of the constant editor unless value in the flexform
        $pathToJPcarouselJS = $this->conf['pathToJPcarouselJS'];
        if ($this->lConf['pathToJPcarouselJS']) {
            $pathToJPcarouselJS = $this->lConf['pathToJPcarouselJS'];
        }

        $pathToJPcarouselCSS = $this->conf['pathToJPcarouselCSS'];
        if ($this->lConf['pathToJPcarouselCSS']) {
            $pathToJPcarouselCSS = $this->lConf['pathToJPcarouselCSS'];
        }

        // if image links in flexform, fill link array
        if ($this->lConf['imageLinks']) { //if there are links defined
            //needed to use typolink function
            $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);

            //explode $imageLinks = explode(',',$this->lConf['imageLinks']);all different links and parameters divided by ,  and put in array imagelinks
            //$imageLinks = explode(',',$this->lConf['imageLinks']);
            $imageLinks = explode(chr(10), $this->lConf['imageLinks']);
        }

        //echo"<br/>";
        //echo"<br/>";
        // Determine the item records array on choosen "Type of list item" in flexform
        if ($this->lConf['myType'] == 'ce') {
            $selectItemRecords = explode(',', $this->lConf['selectCeRecords']);
        }
        if ($this->lConf['myType'] == 'list') {
            $selectItemRecords = explode(',', $this->lConf['selectCeListRecord']);
        }
        //echo $this->lConf['myType'];
        // === Fill marker array items, for content elements and list element ===
        if (($this->lConf['myType'] == 'ce') || ($this->lConf['myType'] == 'list')) {
            foreach ($selectItemRecords as $key => $value) {
                //echo "Key: $key; Pid-nummer: $value<br />\n";
                //echo "Content: <br />".$this->getCE($value);
                // Put content element in submarker with getCE function to get the content from the pid
                if ($this->lConf['myType'] == 'ce') {
                    $markerArrayItem['###FIELD_ITEM###'] = '<li>' . $this->getCE($value) . '</li>'; //
                }
                if ($this->lConf['myType'] == 'list') {
                    $markerArrayItem['###FIELD_ITEM###'] = $this->getCE($value); // don't put <li></li> marks when "Type of list item" is a list, that content should itself provide these marks
                }

                // Compose the content
                $contentItem .= $this->cObj->substituteMarkerArrayCached($subparts['item'], $markerArrayItem);
            } // end foreach
        } // end ce / list
        // === Fill marker array items, for images ===
        if ($this->lConf['myType'] == 'images') {
            $images = explode(',', $this->lConf['selectImages']);
            $numberOfImages = count($images);
            $altText = explode(chr(10), $this->lConf['altText']);
            $titleText = explode(chr(10), $this->lConf['titleText']);
            $captionText = explode(chr(10), $this->lConf['captionText']);
            // for every image determine the properties
            for ($row = 0; $row < $numberOfImages; $row++) {
                $file = 'uploads/tx_jpcarousel/' . $images[$row];

                // thumbnails
                $imgTSConfig = [];
                $imgTSConfig['file'] = $file;

                // get variables out of typoscript unless defined in flexform
                $ifImgTSConfig = [];

                $imgTSConfig['file.']['maxW'] = $this->conf['pic.']['file.']['maxW'];
                if ($this->lConf['itemMaxWidth']) {
                    $imgTSConfig['file.']['maxW'] = $this->lConf['itemMaxWidth'];
                }
                $captionWidth = $imgTSConfig['file.']['maxW'];

                $imgTSConfig['file.']['maxH'] = $this->conf['pic.']['file.']['maxH'];
                if ($this->lConf['itemMaxHeight']) {
                    $imgTSConfig['file.']['maxH'] = $this->lConf['itemMaxHeight'];
                }
                $imgTSConfig['altText'] = $altText[$row];
                $imgTSConfig['titleText'] = $titleText[$row];
                if ($captionText[$row]) {
                    $caption[$row] = '<div class="carouselcaption" style="width:' . ($captionWidth) . 'px;"><span>' . $captionText[$row] . '</span></div>';
                }

                $popup = $this->lConf['popup'];
                if (($popup == 1) and (!$imageLinks)) { //if popup is checked in Flexform and there are no links defined
                    // load configuration for lightboxes
                    if ((ExtensionManagementUtility::isLoaded('rzcolorbox')) or (ExtensionManagementUtility::isLoaded('pmkshadowbox')) or (ExtensionManagementUtility::isLoaded('sk_fancybox')) or (ExtensionManagementUtility::isLoaded('perfectlightbox'))) {
                        $imgTSConfig['imageLinkWrap'] = 1;
                        $imgTSConfig['imageLinkWrap.']['enable'] = 1;
                        $imgTSConfig['imageLinkWrap.']['typolink.']['parameter.']['cObject'] = 'IMG_RESOURCE';
                        $imgTSConfig['imageLinkWrap.']['typolink.']['parameter.']['cObject.']['file.']['import.']['data'] = 'TSFE:lastImageInfo|origFile';
                        $imgTSConfig['imageLinkWrap.']['typolink.']['parameter.']['cObject.']['file.']['maxW'] = $this->conf['pic.']['file.']['popup_maxW'];
                        $imgTSConfig['imageLinkWrap.']['typolink.']['parameter.']['cObject.']['file.']['maxH'] = $this->conf['pic.']['file.']['popup_maxH'];
                        //$imgTSConfig['imageLinkWrap.']['typolink.']['parameter.']['override.']['listNum.']['stdWrap.']['data'] = 'register : IMAGE_NUM_CURRENT';
                        $imgTSConfig['imageLinkWrap.']['typolink.']['alt'] = $altText[$row];
                        $imgTSConfig['imageLinkWrap.']['typolink.']['title'] = $titleText[$row];
                        //echo 'alt:'.$altText[$row].'<br/>';
                        //echo 'title:'.$titleText[$row].'<br/>';
                        //echo 'caption:'.$captionText[$row].'<br/>';
                    }

                    // if extension rzcolorbox is loaded, use config
                    if (ExtensionManagementUtility::isLoaded('rzcolorbox')) {
                        $imgTSConfig['imageLinkWrap.']['typolink.']['ATagParams'] = 'rel="rzcolorbox[' . $this->cObj->data['uid'] . ']" class="rzcolorbox cboxElement"';
                    }
                    // if extension pmk shadowbox is loaded, use config
                    if (ExtensionManagementUtility::isLoaded('pmkshadowbox')) {
                        $imgTSConfig['imageLinkWrap.']['typolink.']['ATagParams'] = 'rel="shadowbox[' . $this->cObj->data['uid'] . ']"';
                    }
                    // if extension SK Fancybox is loaded, use config
                    if (ExtensionManagementUtility::isLoaded('sk_fancybox')) {
                        $imgTSConfig['imageLinkWrap.']['typolink.']['ATagParams'] = 'class="fancybox" rel="sk_fancybox' . $this->cObj->data['uid'] . '"';
                    }
                    // if extension per is loaded, use config
                    if (ExtensionManagementUtility::isLoaded('perfectlightbox')) {
                        $imgTSConfig['imageLinkWrap.']['typolink.']['ATagParams'] = 'rel="lightbox[' . $this->cObj->data['uid'] . ']"';
                    } else {
                        // On Click vergroten standaard
                        $imgTSConfig['imageLinkWrap'] = 1;
                        $imgTSConfig['imageLinkWrap.']['enable'] = 1;
                        $imgTSConfig['imageLinkWrap.']['bodyTag'] = '<BODY style="background-color:#FFFFFF;">';
                        $imgTSConfig['imageLinkWrap.']['wrap'] = '<A href="javascript:close();"> | </A>';
                        $imgTSConfig['imageLinkWrap.']['alt'] = $altText[$row];
                        $imgTSConfig['imageLinkWrap.']['title'] = $titleText[$row];

                        if ($this->conf['pic.']['file.']['popup_maxW'] > 0) {
                            $imgTSConfig['imageLinkWrap.']['width'] = $this->conf['pic.']['file.']['popup_maxW'];
                        }
                        if ($this->conf['pic.']['file.']['popup_maxH'] > 0) {
                            $imgTSConfig['imageLinkWrap.']['height'] = $this->conf['pic.']['file.']['popup_maxH'];
                        }
                        $imgTSConfig['imageLinkWrap.']['JSwindow'] = 1;
                        $imgTSConfig['imageLinkWrap.']['JSwindow.']['newWindow'] = 1;
                        $imgTSConfig['imageLinkWrap.']['JSwindow.']['expand'] = '17,20';
                    }
                }

                // image links in flexform
                if ($this->lConf['imageLinks']) { //if there are links defined
                    //echo $imageLinks[$row];
                    $tConf = ['parameter' => $imageLinks[$row]];
                    $singleImageLink = $cObj->typoLink($imageLinks[$row], $tConf);
                    //echo $singleImageLink.'<br>';
                    // Fill marker array
                    // Put imgTSConfig in submarker with IMAGE function to make it an image and wrap with <li></li>
                    $markerArrayItem['###FIELD_ITEM###'] = '<li>' . $cObj->typoLink($this->cObj->IMAGE($imgTSConfig), $tConf) . $caption[$row] . '</li>';
                } else { // no images links
                    // Fill marker array
                    // Put imgTSConfig in submarker with IMAGE function to make it an image and wrap with <li></li>
                    $markerArrayItem['###FIELD_ITEM###'] = '<li>' . $this->cObj->IMAGE($imgTSConfig) . $caption[$row] . '</li>';
                }

                // Compose the content
                $contentItem .= $this->cObj->substituteMarkerArrayCached($subparts['item'], $markerArrayItem);
            } // end for
        } // end filling marker array items, for images
        // Create buttons HTML if useButtons in flexform = 1
        $useButtons = $this->lConf['useButtons'];
        $clearGifPath = ExtensionManagementUtility::siteRelPath('jpcarousel') . 'res/img/clear.gif';
        if ($useButtons == 1) {
            $buttonLeftCarousel = '<a href="#" class="carouselprev" id="prev' . $currentCeID . '" title="' . $this->conf['altTitlePreviousButton'] . '"><img title="' . $this->conf['altTitlePreviousButton'] . '" alt="' . $this->conf['altTitlePreviousButton'] . '" src="' . $clearGifPath . '" /></a>';
            $buttonRightCarousel = '<a href="#" class="carouselnext" id="next' . $currentCeID . '" title="' . $this->conf['altTitleNextButton'] . '"><img title="' . $this->conf['altTitleNextButton'] . '" alt="' . $this->conf['altTitleNextButton'] . '" src="' . $clearGifPath . '" /></a>';
            //$buttonLeftCarousel = '<button class="prev">&lt;&lt;</button>';
            //$buttonRightCarousel = '<button class="next">&gt;&gt;</button>';
        }
        // Configure width, height and background-color of div.carouselContainer
        // take the values of the constant editor unless value in the flexform
        $CarouselWidth = $this->conf['carouselWidth'];
        if ($this->lConf['carouselWidth']) {
            $CarouselWidth = $this->lConf['carouselWidth'];
        }
        $CarouselHeight = $this->conf['carouselHeight'];
        if ($this->lConf['carouselHeight']) {
            $CarouselHeight = $this->lConf['carouselHeight'];
        }
        $carouselBgColor = $this->conf['carouselBgColor'];
        if ($this->lConf['carouselBgColor']) {
            $carouselBgColor = $this->lConf['carouselBgColor'];
        }
        $styleCarouselContainer = 'width:' . $CarouselWidth . 'px; ' . 'height:' . $CarouselHeight . 'px; ' . 'background:' . $carouselBgColor . '; ';

        // Show title if it exists
        if ($this->pi_getFFvalue($piFlexForm, 'carouselTitle', 'features')) {
            $carousel_title = '<h3 class="carousel_title">' . $this->pi_getFFvalue($piFlexForm, 'carouselTitle', 'features') . '</h3>';
        }

        // Create pagination if pagination in flexform = 1
        $pagination = $this->lConf['pagination'];
        if ($pagination == 1) {
            $paginationButtons = '<div class="pagination" id="pagination' . $currentCeID . '"></div>';
        }

        // insert js, CSS and configuration js in header
        if ($includeOwnJqueryLib) {
            //load jquery if needed
            $GLOBALS['TSFE']->getPageRenderer()->addJsFooterFile($GLOBALS['TSFE']->tmpl->getFileName($this->conf['pathToJquery']), $type = 'text/javascript', $compress = true, $forceOnTop = false, $allWrap = '');
        }
        //load jquery.carouFredSel.js if path is filled in
        $includeCarouFredselJs = $this->conf['pathToJPcarouselJS'] ? true : false;
        if ($includeCarouFredselJs) {
            $GLOBALS['TSFE']->getPageRenderer()->addJsFooterFile($GLOBALS['TSFE']->tmpl->getFileName($pathToJPcarouselJS), $type = 'text/javascript', $compress = true, $forceOnTop = false, $allWrap = '');
        }

        // if general CSS should be loaded (checkbox in Flexform)
        if ($this->lConf['includeCSS']) {
            $GLOBALS['TSFE']->getPageRenderer()->addCssFile($GLOBALS['TSFE']->tmpl->getFileName($pathToJPcarouselCSS), $rel = 'stylesheet', $media = 'all', $title = '', $compress = true, $forceOnTop = false, $allWrap = '');
        }
        //load dynamic CSS
        $dynamicJpCCSS = '#' . $currentCeID . ' .' . $carouselID . ' li {width:' . $li_width . 'px; height:' . $li_height . 'px;} #' . $currentCeID . ' .carouselContainer {' . $styleCarouselContainer . '}';
        $GLOBALS['TSFE']->getPageRenderer()->addCssInlineBlock($currentCeID . $carouselID, $dynamicJpCCSS, $compress = true, $forceOnTop = false);
        //load dynamic JS
        $GLOBALS['TSFE']->getPageRenderer()->addJsFooterInlineCode($currentCeID . $carouselID, $carouFredSelConfig, $compress = true, $forceOnTop = false);

        // Fill marker array
        // Don't put <ul></ul> marks when "Type of list item" is a list, that content should itself provide these marks
        if ($this->lConf['myType'] != 'list') {
            $ulStart = '<ul class="jpcarousel">';
            $ulEnd = '</ul>';
        }

        $markerArrayScript['###FIELD_SCRIPT###'] = $javascriptCarousel;
        $markerArrayScript['###FIELD_BUTTON_LEFT###'] = $buttonLeftCarousel;
        $markerArrayScript['###FIELD_BUTTON_RIGHT###'] = $buttonRightCarousel;
        $markerArrayScript['###FIELD_TITLE###'] = $carousel_title;
        $markerArrayScript['###PAGINATION###'] = $paginationButtons;

        // Substitute markers
        $contentItemControl = $this->cObj->substituteMarkerArrayCached($subparts['control'], $markerArrayScript);

        // Fill subpart markers
        $subpartArray['###FIELD_CAROUSEL_ID###'] = $carouselID;
        $subpartArray['###FIELD_UL_START###'] = $ulStart;
        $subpartArray['###FIELD_UL_END###'] = $ulEnd;
        $subpartArray['###ITEM###'] = $contentItem;
        $subpartArray['###CONTROL###'] = $contentItemControl;

        // Complete the template expansion
        $content = $this->cObj->substituteMarkerArrayCached($subparts['template'], null, $subpartArray);

        return $this->pi_wrapInBaseClass($content);
    }

    /**
     * Gets tt_content for a pid number
     *
     * @param    int    $pid: Uid of the page
     * @return    tt_content
     */
    public function getCE($id)
    {
        $conf['tables'] = 'tt_content';
        $conf['source'] = $id;
        $conf['dontCheckPid'] = 1;
        return $this->cObj->cObjGetSingle('RECORDS', $conf);
    }
}
