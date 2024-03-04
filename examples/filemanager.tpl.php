<?php $strPageTitle = 'Examples of file management' ; ?>

<?php require('header.inc.php'); ?>

<?php $this->RenderBegin(); ?>

<div class="page-content-wrapper">
    <div class="page-content">
        <div class="content-body">
            <div class="files-heading">
                <div class="row">
                    <div class="col-md-9">
                        <div class="btn-group" role="group">
                            <?= _r($this->btnUploadStart); ?>
                            <?= _r($this->btnAddFolder); ?>
                        </div>
                        <div class="btn-group" role="group">
                            <?= _r($this->btnRefresh); ?>
                        </div>
                        <div class="btn-group" role="group">
                            <?= _r($this->btnRename); ?>
                            <?php // _r($this->btnChange); ?>
                            <?= _r($this->btnCopy); ?>
                            <?= _r($this->btnDelete); ?>
                            <?= _r($this->btnMove); ?>
                            <?= _r($this->btnDownload); ?>
                        </div>
                        <div class="btn-group" role="group">
                            <?= _r($this->btnImageListView); ?>
                            <?= _r($this->btnListView); ?>
                            <?= _r($this->btnBoxView); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="search">
                            <?= _r($this->txtFilter); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="fileupload-buttonbar hidden">
                <div class="row">
                    <div class="col-md-12">
                        <?= _r($this->btnAddFiles); ?>
                        <?= _r($this->btnAllStart); ?>
                        <?= _r($this->btnAllCancel); ?>
                        <?= _r($this->btnBack); ?>
                    </div>
                </div>
            </div>
            <div class="form-body">
                <div class="row">
                    <div class="col-md-9">
                        <div class="break-word">
                            <div class="head">
                                <?= _r($this->lblSearch); ?>
                                <?= _r($this->objHomeLink); ?>
                            </div>&nbsp;&nbsp;&nbsp;
                            <div class="breadcrumbs"></div>
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                </div>
                <div class="row equal">
                    <div class="col-md-9">
                        <div id="alert-wrapper"></div>
                        <div class="alert-multi-wrapper"></div>
                        <div class="upload-wrapper hidden">
                            <?= _r($this->objUpload); ?>
                        </div>
                        <div class="scroll-wrapper"> <!-- This element is required for the scrollpad control -->
                            <div class="control-scrollpad">
                                <?= _r($this->objManager); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="file-info">
                            <?= _r($this->objInfo); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->RenderEnd(); ?>

<?php require('footer.inc.php');