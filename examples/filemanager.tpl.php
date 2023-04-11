<?php $strPageTitle = 'Examples of file management' ; ?>

<?php require('header.inc.php'); ?>

<style>
   /*.vauu-scroll-table .ui-selecting { background: #FECA40; }*/
    .vauu-scroll-table .ui-selected { background: #F39814; color: white;}
   .vauu-scroll-table .ui-selected:hover { background: #F39814; color: white;}
</style>

<?php $this->RenderBegin(); ?>

<!-- BEGIN CONTENT -->

<?= _r($this->objUpload); ?>

<div class="page-content-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-md-12">
                <div class="content-body">
<!--                    <div class="files-heading">

                        <div class="row">
                            <div class="col-md-9">
                                <div class="btn-group" role="group">
                                    <?php /*= _r($this->btnUpload); */?>
                                    <?php /*= _r($this->btnAddFolder); */?>
                                </div>
                                &nbsp;
                                <div class="btn-group" role="group">
                                    <?php /*= _r($this->btnMove); */?>
                                    <?php /*= _r($this->btnRename); */?>
                                    <?php /*= _r($this->btnCopy); */?>
                                    <?php /*= _r($this->btnDownload); */?>
                                    <?php /*= _r($this->btnDelete); */?>
                                </div>
                                &nbsp;
                                <div class="btn-group" role="group">
                                    <?php /*= _r($this->btnListView); */?>
                                    <?php /*= _r($this->btnGridView); */?>
                                    <?php /*= _r($this->btnBoxView); */?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <?php /*= _r($this->txtFilter); */?>
                            </div>
                        </div>
                    </div>-->

                    <div class="fileupload-buttonbar">
                        <div class="row">
                            <div class="col-md-12">
                                <?= _r($this->btnAddFiles); ?>
                                <?= _r($this->btnAllStart); ?>
                                <?= _r($this->btnAllCancel); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-body">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="break-word">
                                    <a href="" class="head">Library</a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div id="alert-wrapper"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="files"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-9">
                                <div data-control="media-items">
                                    <svg viewBox="0 0 18 18" class="svg-preloader svg-preloader-active preloader-body">
                                        <circle cx="9" cy="9" r="8" pathLength="100" class="svg-preloader-circle"></circle>
                                    </svg>
                                    <div class="scrollpad control-scrollpad">
                                        <div class="scroll-wrapper"> <!-- This element is required for the scrollpad control -->
                                            <?= _r($this->objManager); ?>
                                        </div>
                                    </div>

                                    <div class="" style="padding: 15px 0;"> <!--  border: 1px solid #ccc;  -->
                                        Full size: 13.38 MB | Files: 24 | Folders: 4
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="file-info">

                                    <div class="file-info-title">
                                        <div class="caption">File info</div>
                                    </div>

                                    <div class="file-info-body">
                                        <div class="file-info-no-wrapper">
                                            <i class="fa fa-crop fa-5x" aria-hidden="true"></i>
                                            <p>Nothing is selected</p>
                                        </div>
                                    </div>


                                </div>
                            </div>



                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="pull-right" style="padding-top: 10px;">
                                    <button type="submit" class="btn btn-orange" style="display:none">Sisesta</button>
                                    <button type="submit" class="btn btn-darkblue" style="display:none">Redigeeri</button>
                                    <button type="submit" class="btn btn-default" style="display:none">Loobu</button>
                                </div>
                            </div>
                        </div>


                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->RenderEnd(); ?>

<?php require('footer.inc.php');

// https://stackoverflow.com/questions/17450861/add-scroll-bar-to-table-body

// http://jsfiddle.net/JamesKyle/5Fjdf/

// https://www.revilodesign.de/blog/css-tricks/css-table-header-fixed-thead-einer-tabelle-fixieren/

// https://www.revilodesign.de/

// http://jsfiddle.net/FranWahl/rFGWZ/


// :::::::::::::::::::::::::::::::::::::::::::::::::

// http://jsfiddle.net/yNWSY/

// https://api.jquery.com/on/

// ||KÕIGE PAREM|| https://www.jqueryscript.net/demo/jQuery-Plugin-To-Enable-Multi-Rows-Selection-On-Table-Row-Selector/

// OPPIMINE: https://www.tutorialsteacher.com/javascript/

// https://codingstatus.com/change-url-without-reloading-page-jquery-ajax-php/

// https://stackoverflow.com/questions/3338642/updating-address-bar-with-new-url-without-hash-or-reloading-the-page


// ajax change url without refreshing page

// https://stackoverflow.com/questions/35395485/change-url-without-refresh-the-page

// https://css-tricks.com/using-the-html5-history-api/

// https://www.javascripttutorial.net/web-apis/javascript-history-pushstate/

// ******************************************

// https://dmitripavlutin.com/screen-window-page-sizes/

// https://stackoverflow.com/questions/1575141/how-to-make-a-div-100-height-of-the-browser-window

// https://stackoverflow.com/questions/12172177/set-div-height-equal-to-screen-size

// https://jsfiddle.net/ascii/5pt98bxz/

// https://github.com/ideatic/jquery_fm

// ::::::::::::::::::::::::::::::::

// https://stackoverflow.com/questions/5647461/how-do-i-send-a-post-request-with-php


// :::::::::::::::::::::::::::::::::::::::::::::


// https://www.geeksforgeeks.org/how-to-fetch-data-from-json-file-and-display-in-html-table-using-jquery/


// How to select specific data in json file from javascript or jquery?

// https://stackoverflow.com/questions/11922383/how-can-i-access-and-process-nested-objects-arrays-or-json

// https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images


// ||||||||||||||||||||||||||||||||||||||||||||||||||||||||

// https://designdrizzle.com/best-php-image-manipulation-libraries-which-developers-must-use/

// https://image.intervention.io/v2

// https://stackoverflow.com/questions/807878/how-to-make-javascript-execute-after-page-load

// |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||

// https://www.freecodecamp.org/news/here-is-the-most-popular-ways-to-make-an-http-request-in-javascript-954ce8c95aaa/

// https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch
// https://github.com/mdn/fetch-examples/

// https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch

// https://stackoverflow.com/questions/1034621/get-the-current-url-with-javascript

// ||||||||||||||||||||||||||||||||||||||||

// JSON tegelemine

// https://stackoverflow.com/questions/38305335/multiple-json-files-that-i-would-like-to-parse-edit-and-merge-into-one-object
// https://stackoverflow.com/questions/62579243/reading-big-arrays-from-big-json-file-in-php
//

// ||||||||||||||||||||||||||||||||||||||||||||||||||||||
// Javascripyiga kuupäeva ja aja tegelemine

// https://stackoverflow.com/questions/3552461/how-to-format-a-javascript-date

// https://haydenjames.io/php-8-compatibility-check-and-performance-tips/

?>


