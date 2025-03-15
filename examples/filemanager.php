<?php
require_once('qcubed.inc.php');
require_once ('../src/FileInfo.php');
require_once ('../src/DestinationInfo.class.php');
require_once ('../src/Archive.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Plugin\FileHandler;
use QCubed\Plugin\FileManager;
use QCuded\Plugin\FileInfo;
use QCubed\QDateTime;
use QCubed\Folder;
use QCubed\QString;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Html;
use QCubed\Query\QQ;
use QCubed\Action\Ajax;
use QCubed\Jqui\Event\SelectableStop;

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
    protected $dlgModal1;
    protected $dlgModal2;
    protected $dlgModal3;
    protected $dlgModal4;
    protected $dlgModal5;
    protected $dlgModal6;
    protected $dlgModal7;
    protected $dlgModal8;
    protected $dlgModal9;
    protected $dlgModal10;
    protected $dlgModal11;
    protected $dlgModal12;
    protected $dlgModal13;
    protected $dlgModal14;
    protected $dlgModal15;
    protected $dlgModal16;
    protected $dlgModal17;
    protected $dlgModal18;
    protected $dlgModal19;
    protected $dlgModal20;
    protected $dlgModal21;
    protected $dlgModal22;
    protected $dlgModal23;
    protected $dlgModal24;
    protected $dlgModal25;
    protected $dlgModal26;
    protected $dlgModal27;
    protected $dlgModal28;
    protected $dlgModal29;
    protected $dlgModal30;
    protected $dlgModal31;
    protected $dlgModal32;
    protected $dlgModal33;
    protected $dlgModal34;

    protected $dlgModal35;
    protected $dlgModal36;
    protected $dlgModal37;
    protected $dlgModal38;
    protected $dlgModal39;

    protected $dlgModal40;
    protected $dlgModal41;
    protected $dlgModal42;
    protected $dlgModal43;
    protected $dlgModal44;
    protected $dlgModal45;
    protected $dlgModal46;

    protected $objUpload;
    protected $objManager;
    protected $dlgPopup;
    protected $objInfo;
    protected $lblSearch;
    protected $objHomeLink;

    protected $btnAddFiles;
    protected $btnAllStart;
    protected $btnAllCancel;
    protected $btnBack;
    protected $btnDone;

    protected $btnUploadStart;
    protected $btnAddFolder;
    protected $btnRefresh;
    protected $btnRename;
    protected $btnCrop;
    protected $btnCopy;
    protected $btnDelete;
    protected $btnMove;
    protected $btnDownload;
    protected $btnImageListView;
    protected $btnListView;
    protected $btnBoxView;
    protected $txtFilter;

    protected $txtAddFolder;
    protected $lblError;
    protected $lblSameName;
    protected $lblRenameName;
    protected $lblDirectoryError;
    protected $txtRename;

    protected $lblDestinationError;
    protected $lblCourceTitle;
    protected $lblCourcePath;
    protected $lblCopyingTitle;
    protected $dlgCopyingDestination;

    protected $lblMovingError;
    protected $lblMoveInfo;
    protected $lblMovingDestinationError;
    protected $lblMovingCourceTitle;
    protected $lblMovingCourcePath;
    protected $lblMovingTitle;
    protected $dlgMovingDestination;

    protected $lblDeletionWarning;
    protected $lblDeletionInfo;
    protected $lblDeleteError;
    protected $lblDeleteInfo;
    protected $lblDeleteTitle;
    protected $lblDeletePath;

    protected $arrSomeArray = [];
    protected $tempItems = [];
    protected $tempSelectedItems = [];
    protected $objLockedFiles = 0;
    protected $objLockedDirs = [];

    protected $intDataId = "";
    protected $strDataName = "";
    protected $strDataPath = "";
    protected $strDataExtension = "";
    protected $strDataType = "";
    protected $intDataLocked = "";
    protected $strNewPath;
    protected $intStoredChecks = 0;
    protected $arrAllowed = array('jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif');
    protected $tempFolders = array('thumbnail', 'medium', 'large');
    protected $arrCroppieTypes = array('jpg', 'jpeg', 'png');

    protected $blnMove = false;

    protected function formCreate()
    {
        parent::formCreate();

        $this->objUpload = new Q\Plugin\FileUploadHandler($this);
        $this->objUpload->Language = "et"; // Default en
        //$this->objUpload->ShowIcons = true; // Default false
        //$this->objUpload->AcceptFileTypes = ['gif', 'jpg', 'jpeg', 'png', 'pdf', 'ppt', 'docx', 'mp4']; // Default null
        //$this->objUpload->MaxNumberOfFiles = 5; // Default null
        //$this->objUpload->MaxFileSize = 1024 * 1024 * 2; // 2 MB // Default null
        //$this->objUpload->MinFileSize = 500000; // 500 kb // Default null
        //$this->objUpload->ChunkUpload = false; // Default true
        $this->objUpload->MaxChunkSize = 1024 * 1024; // Default 5 MB
        //$this->objUpload->LimitConcurrentUploads = 5; // Default 2
        $this->objUpload->Url = 'php/upload.php'; // Default null
        //$this->objUpload->PreviewMaxWidth = 120; // Default 80
        //$this->objUpload->PreviewMaxHeight = 120; // Default 80

        $this->objUpload->UseWrapper = false;

        $this->objManager = new Q\Plugin\FileManager($this);
        $this->objManager->Language = 'et'; // Default en
        $this->objManager->RootPath = APP_UPLOADS_DIR;
        $this->objManager->RootUrl = APP_UPLOADS_URL;
        $this->objManager->TempPath = APP_UPLOADS_TEMP_DIR;
        $this->objManager->TempUrl = APP_UPLOADS_TEMP_URL;
        $this->objManager->DateTimeFormat = 'DD.MM.YYYY HH:mm:ss';
        $this->objManager->UseWrapper = false;
        $this->objManager->addAction(new SelectableStop(), new Ajax ('selectable_stop'));

        $this->dlgPopup = new Q\Plugin\FilePopupCroppie($this);
        $this->dlgPopup->Url = "php/crop_upload.php";
        $this->dlgPopup->Language = "et";
        $this->dlgPopup->TranslatePlaceholder = t("- Select a destination -");
        $this->dlgPopup->Theme = "web-vauu";
        $this->dlgPopup->HeaderTitle = t("Crop image");
        $this->dlgPopup->SaveText = t("Crop and save");
        $this->dlgPopup->CancelText = t("Cancel");

        $this->dlgPopup->addAction(new Q\Plugin\Event\ChangeObject(), new \QCubed\Action\Ajax('objManagerRefresh_Click'));

        if ($this->dlgPopup->Language) {
            $this->dlgPopup->AddJavascriptFile(QCUBED_FILEMANAGER_ASSETS_URL . "/js/i18n/". $this->dlgPopup->Language . ".js");
        }

        $this->objInfo = new Q\Plugin\FileInfo($this);
        $this->objInfo->RootUrl = APP_UPLOADS_URL;
        $this->objInfo->TempUrl = APP_UPLOADS_TEMP_URL;
        $this->objInfo->UseWrapper = false;

        $this->lblSearch = new Q\Plugin\Label($this);
        $this->lblSearch->addCssClass('search-results hidden');
        $this->lblSearch->setHtmlAttribute("data-lang", "search_results");
        $this->lblSearch->setCssStyle('font-weight', 600);
        $this->lblSearch->setCssStyle('font-size', '14px;');
        $this->lblSearch->Text = t('Search results:');

        $this->objHomeLink = new Q\Plugin\Label($this);
        $this->objHomeLink->addCssClass('homelink');
        $this->objHomeLink->setCssStyle('font-weight', 600);
        $this->objHomeLink->setCssStyle('font-size', '14px;');
        $this->objHomeLink->Text = Q\Html::renderLink("filemanager.php#/", "Repository", ["data-lang" => "repository"]);
        $this->objHomeLink->HtmlEntities = false;
        $this->objHomeLink->addAction(new Q\Event\Click(), new Q\Action\Ajax('appendData_Click'));

        $this->CreateButtons();
        $this->createModals();
        $this->portedAddFolderTextBox();
        $this->portedRenameTextBox();
        $this->portedCheckDestination();
        $this->portedCopyingListBox();
        $this->portedDeleteBox();
        $this->portedMovingListBox();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Processes the selected items from the action parameters and decodes them into an array.
     *
     * @param ActionParams $params The action parameters containing the selected items.
     * @return array Decoded array of selected items.
     */
    public function selectable_stop(ActionParams $params)
    {
        $arr = $this->objManager->SelectedItems;
        $this->arrSomeArray = json_decode($arr, true);

        return $this->arrSomeArray;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates and initializes various buttons and input elements required
     * for file management operations within the application. These include
     * buttons for file upload, folder creation, navigation, and actions such
     * as renaming, deleting, copying, and moving files, among others.
     * Additionally, it configures view toggles and a search filter field.
     *
     * @return void
     */
    public function CreateButtons()
    {
        $this->btnAddFiles = new Q\Plugin\BsFileControl($this, 'files');
        $this->btnAddFiles->Text = t(' Add files');
        $this->btnAddFiles->Glyph = 'fa fa-upload';
        $this->btnAddFiles->Multiple = true;
        $this->btnAddFiles->CssClass = 'btn btn-orange fileinput-button';
        $this->btnAddFiles->UseWrapper = false;

        $this->btnAllStart = new Bs\Button($this);
        $this->btnAllStart->Text = t('Start upload');
        $this->btnAllStart->CssClass = 'btn btn-darkblue all-start disabled';
        $this->btnAllStart->UseWrapper = false;
        $this->btnAllStart->addAction(new Q\Event\Click(), new Q\Action\Ajax('confirmParent_Click'));

        $this->btnAllCancel = new Bs\Button($this);
        $this->btnAllCancel->Text = t('Cancel all uploads');
        $this->btnAllCancel->CssClass = 'btn btn-warning all-cancel disabled';
        $this->btnAllCancel->UseWrapper = false;

        $this->btnBack = new Bs\Button($this);
        $this->btnBack->Text = t('Back to file manager');
        $this->btnBack->CssClass = 'btn btn-default back';
        $this->btnBack->UseWrapper = false;
        $this->btnBack->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnBack_Click'));
        $this->btnBack->addAction(new Q\Event\Click(), new Q\Action\Ajax('dataClearing_Click'));

        $this->btnDone = new Bs\Button($this);
        $this->btnDone->Text = t('Done');
        $this->btnDone->CssClass = 'btn btn-success pull-right done';
        $this->btnDone->UseWrapper = false;
        $this->btnDone->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDone_Click'));

        /////////////////////////////////////////////////////////////////////

        $this->btnUploadStart = new Q\Plugin\Button($this);
        $this->btnUploadStart->Text = t(' Upload');
        $this->btnUploadStart->Glyph = 'fa fa-upload';
        $this->btnUploadStart->CssClass = 'btn btn-orange launch-start';
        $this->btnUploadStart->CausesValidation = false;
        $this->btnUploadStart->UseWrapper = false;
        $this->btnUploadStart->addAction(new Q\Event\Click(), new Q\Action\Ajax('uploadStart_Click'));

        /////////////////////////////////////////////////////////////////////

        $this->btnAddFolder = new Q\Plugin\Button($this);
        $this->btnAddFolder->Text = t(' Add folder');
        $this->btnAddFolder->Glyph = 'fa fa-folder';
        $this->btnAddFolder->CssClass = 'btn btn-orange';
        $this->btnAddFolder->CausesValidation = false;
        $this->btnAddFolder->UseWrapper = false;
        $this->btnAddFolder->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnAddFolder_Click'));

        $this->btnRefresh = new Q\Plugin\Button($this);
        $this->btnRefresh->Glyph = 'fa fa-refresh';
        $this->btnRefresh->CssClass = 'btn btn-darkblue';
        $this->btnRefresh->CausesValidation = false;
        $this->btnRefresh->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnRefresh_Click'));

        $this->btnRename = new Q\Plugin\Button($this);
        $this->btnRename->Text = t(' Rename');
        $this->btnRename->Glyph = 'fa fa-pencil';
        $this->btnRename->CssClass = 'btn btn-darkblue';
        $this->btnRename->CausesValidation = false;
        $this->btnRename->UseWrapper = false;
        $this->btnRename->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnRename_Click'));

        $this->btnCrop = new Q\Plugin\Button($this);
        $this->btnCrop->Text = t(' Crop');
        $this->btnCrop->Glyph = 'fa fa-crop';
        $this->btnCrop->CssClass = 'btn btn-darkblue';
        $this->btnCrop->CausesValidation = false;
        $this->btnCrop->UseWrapper = false;
        $this->btnCrop->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnCrop_Click'));

        $this->btnCopy = new Q\Plugin\Button($this);
        $this->btnCopy->Text = t(' Copy');
        $this->btnCopy->Glyph = 'fa fa-files-o';
        $this->btnCopy->CssClass = 'btn btn-darkblue';
        $this->btnCopy->CausesValidation = false;
        $this->btnCopy->UseWrapper = false;
        $this->btnCopy->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnCopy_Click'));

        $this->btnDelete = new Q\Plugin\Button($this);
        $this->btnDelete->Text = t(' Delete');
        $this->btnDelete->Glyph = 'fa fa-trash-o';
        $this->btnDelete->CssClass = 'btn btn-darkblue';
        $this->btnDelete->CausesValidation = false;
        $this->btnDelete->UseWrapper = false;
        $this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));

        $this->btnMove = new Q\Plugin\Button($this);
        $this->btnMove->Text = t(' Move');
        $this->btnMove->Glyph = 'fa fa-reply-all';
        $this->btnMove->CssClass = 'btn btn-darkblue';
        $this->btnMove->CausesValidation = false;
        $this->btnMove->UseWrapper = false;
        $this->btnMove->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMove_Click'));

        $this->btnDownload = new Q\Plugin\Button($this);
        $this->btnDownload->Text = t(' Download');
        $this->btnDownload->Glyph = 'fa fa-download';
        $this->btnDownload->CssClass = 'btn btn-darkblue';
        $this->btnDownload->CausesValidation = false;
        $this->btnDownload->UseWrapper = false;
        $this->btnDownload->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDownload_Click'));

        $this->btnImageListView = new Q\Plugin\Button($this);
        $this->btnImageListView->Glyph = 'fa fa-list'; //  fa-align-justify
        $this->btnImageListView->CssClass = 'btn btn-darkblue';
        $this->btnImageListView->addCssClass('btn-imageList active');
        $this->btnImageListView->UseWrapper = false;
        $this->btnImageListView->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnImageListView_Click'));

        $this->btnListView = new Q\Plugin\Button($this);
        $this->btnListView->Glyph = 'fa fa-align-justify';
        $this->btnListView->CssClass = 'btn btn-darkblue';
        $this->btnListView->addCssClass('btn-list');
        $this->btnListView->UseWrapper = false;
        $this->btnListView->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnListView_Click'));

        $this->btnBoxView = new Q\Plugin\Button($this);
        $this->btnBoxView->Glyph = 'fa fa-th-large';
        $this->btnBoxView->CssClass = 'btn btn-darkblue';
        $this->btnBoxView->addCssClass('btn-box');
        $this->btnBoxView->UseWrapper = false;
        $this->btnBoxView->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnBoxView_Click'));

        $this->txtFilter = new Bs\TextBox($this);
        $this->txtFilter->Placeholder = t('Search...');
        $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
        $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
        $this->txtFilter->addCssClass('search-trigger');
    }

    /**
     * Initializes and creates various modal dialogs used in the application for displaying warnings,
     * tips, information, and actions related to folder or file operations.
     *
     * @return void
     */
    public function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->Text = t('<p style="margin-top: 15px;">Corrupted table "folders" in the database or folder "upload" in the file system!</p>
                                    <p style="margin-top: 15px;">The table and the file system must be in sync.</p>
                                    <p style="margin-top: 15px;">Please contact the developer or webmaster!</p>');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addCloseButton(t("I take note and ask for help"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // UPLOAD

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t('Tip');
        $this->dlgModal2->Text = t('<p style="margin-top: 15px;">Sorry, files cannot be added to this reserved folder!</p>
                                    <p style="margin-top: 15px;">Choose another folder!</p>');
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addCloseButton(t("I close the window"));

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t('Tip');
        $this->dlgModal3->Text = t('<p style="margin-top: 15px;">Please choose only specific folder to upload files!</p>');
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addCloseButton(t("I close the window"));

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t('Tip');
        $this->dlgModal4->Text = t('<p style="margin-top: 15px;">Cannot select multiple folders to upload files!</p>');
        $this->dlgModal4->HeaderClasses = 'btn-darkblue';
        $this->dlgModal4->addCloseButton(t("I close the window"));

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->AutoRenderChildren = true;
        $this->dlgModal5->Title = t('Info');
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 10px;">Please check if the destination is correct!</p>');
        $this->dlgModal5->HeaderClasses = 'btn-default';
        $this->dlgModal5->addButton(t("I will continue"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal5->addCloseButton(t("I'll cancel"));
        $this->dlgModal5->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('startUploadProcess_Click'));
        $this->dlgModal5->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('dataClearing_Click'));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // NEW FOLDER

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Title = t('Tip');
        $this->dlgModal6->Text = t('<p style="margin-top: 15px;">Sorry, a new folder cannot be added to this reserved folder!</p>
                                    <p style="margin-top: 15px;">Choose another folder!</p>');
        $this->dlgModal6->HeaderClasses = 'btn-darkblue';
        $this->dlgModal6->addCloseButton(t("I close the window"));

        $this->dlgModal7 = new Bs\Modal($this);
        $this->dlgModal7->Title = t('Tip');
        $this->dlgModal7->Text = t('<p style="margin-top: 15px;">Please select only one folder to create a new folder in!</p>');
        $this->dlgModal7->HeaderClasses = 'btn-darkblue';
        $this->dlgModal7->addCloseButton(t("I close the window"));

        $this->dlgModal8 = new Bs\Modal($this);
        $this->dlgModal8->AutoRenderChildren = true;
        $this->dlgModal8->Title = t('Info');
        $this->dlgModal8->Text = t('<p style="line-height: 25px; margin-bottom: 10px;">Please check if the destination is correct!</p>');
        $this->dlgModal8->HeaderClasses = 'btn-default';
        $this->dlgModal8->addButton(t("I will continue"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal8->addCloseButton(t("I'll cancel"));
        $this->dlgModal8->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('startAddFolderProcess_Click'));
        $this->dlgModal8->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('dataClearing_Click'));

        $this->dlgModal9 = new Bs\Modal($this);
        $this->dlgModal9->AutoRenderChildren = true;
        $this->dlgModal9->Title = t('Name of new folder');
        $this->dlgModal9->HeaderClasses = 'btn-default';
        $this->dlgModal9->addButton(t("I accept"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal9->addCloseButton(t("I'll cancel"));
        $this->dlgModal9->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('addFolderName_Click'));
        $this->dlgModal9->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('dataClearing_Click'));

        $this->dlgModal10 = new Bs\Modal($this);
        $this->dlgModal10->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">New folder created successfully!</p>');
        $this->dlgModal10->Title = t("Success");
        $this->dlgModal10->HeaderClasses = 'btn-success';
        $this->dlgModal10->addCloseButton(t("I close the window"));

        $this->dlgModal11 = new Bs\Modal($this);
        $this->dlgModal11->Title = t('Warning');
        $this->dlgModal11->Text = t('<p style="margin-top: 15px;">Failed to create new folder!</p>');
        $this->dlgModal11->HeaderClasses = 'btn-danger';
        $this->dlgModal11->addCloseButton(t("I understand"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // RENAME

        $this->dlgModal12 = new Bs\Modal($this);
        $this->dlgModal12->Title = t('Tip');
        $this->dlgModal12->Text = t('<p style="margin-top: 15px;">Sorry, this reserved folder or file cannot be renamed!</p>
                                    <p style="margin-top: 15px;">Choose another folder or file!</p>');
        $this->dlgModal12->HeaderClasses = 'btn-darkblue';
        $this->dlgModal12->addCloseButton(t("I close the window"));

        $this->dlgModal13 = new Bs\Modal($this);
        $this->dlgModal13->Title = t('Tip');
        $this->dlgModal13->Text = t('<p style="margin-top: 15px;">Please select a folder or file!</p>');
        $this->dlgModal13->HeaderClasses = 'btn-darkblue';
        $this->dlgModal13->addCloseButton(t("I close the window"));

        $this->dlgModal14 = new Bs\Modal($this);
        $this->dlgModal14->Title = t('Tip');
        $this->dlgModal14->Text = t('<p style="margin-top: 15px;">Please select only one folder or file to rename!</p>');
        $this->dlgModal14->HeaderClasses = 'btn-darkblue';
        $this->dlgModal14->addCloseButton(t("I close the window"));

        $this->dlgModal15 = new Bs\Modal($this);
        $this->dlgModal15->AutoRenderChildren = true;
        $this->dlgModal15->Title = t('Rename the folder or file name');
        $this->dlgModal15->HeaderClasses = 'btn-default';
        $this->dlgModal15->addButton(t("I accept"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal15->addCloseButton(t("I'll cancel"));
        $this->dlgModal15->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('renameName_Click'));
        $this->dlgModal15->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('dataClearing_Click'));

        $this->dlgModal16 = new Bs\Modal($this);
        $this->dlgModal16->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Folder name changed successfully!</p>');
        $this->dlgModal16->Title = t("Success");
        $this->dlgModal16->HeaderClasses = 'btn-success';
        $this->dlgModal16->addCloseButton(t("I close the window"));

        $this->dlgModal17 = new Bs\Modal($this);
        $this->dlgModal17->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Failed to rename folder!</p>');
        $this->dlgModal17->Title = t("Warning");
        $this->dlgModal17->HeaderClasses = 'btn-danger';
        $this->dlgModal17->addCloseButton(t("I understand"));

        $this->dlgModal18 = new Bs\Modal($this);
        $this->dlgModal18->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">File name changed successfully!</p>');
        $this->dlgModal18->Title = t("Success");
        $this->dlgModal18->HeaderClasses = 'btn-success';
        $this->dlgModal18->addCloseButton(t("I close the window"));

        $this->dlgModal19 = new Bs\Modal($this);
        $this->dlgModal19->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Failed to rename file!</p>');
        $this->dlgModal19->Title = t("Warning");
        $this->dlgModal19->HeaderClasses = 'btn-danger';
        $this->dlgModal19->addCloseButton(t("I understand"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // COPY

        $this->dlgModal20 = new Bs\Modal($this);
        $this->dlgModal20->Title = t('Tip');
        $this->dlgModal20->Text = t('<p style="margin-top: 15px;">Please select a specific folder(s) or file(s)!</p>');
        $this->dlgModal20->HeaderClasses = 'btn-darkblue';
        $this->dlgModal20->addCloseButton(t("I close the window"));

        $this->dlgModal21 = new Bs\Modal($this);
        $this->dlgModal21->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">It is not possible to copy the main directory!</p>');
        $this->dlgModal21->Title = t("Warning");
        $this->dlgModal21->HeaderClasses = 'btn-danger';
        $this->dlgModal21->addCloseButton(t("I understand"));

        $this->dlgModal22 = new Bs\Modal($this);
        $this->dlgModal22->AutoRenderChildren = true;
        $this->dlgModal22->Title = t('Copy files or folders');
        $this->dlgModal22->HeaderClasses = 'btn-default';
        $this->dlgModal22->addButton(t("I will continue"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal22->addCloseButton(t("I'll cancel"));
        $this->dlgModal22->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('startCopyingProcess_Click'));
        $this->dlgModal22->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('dataClearing_Click'));

        $this->dlgModal23 = new Bs\Modal($this);
        $this->dlgModal23->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Selected files and folders have been copied successfully!</p>');
        $this->dlgModal23->Title = t("Success");
        $this->dlgModal23->HeaderClasses = 'btn-success';
        $this->dlgModal23->addCloseButton(t("Ok"));

        $this->dlgModal24 = new Bs\Modal($this);
        $this->dlgModal24->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Error while copying items!</p>');
        $this->dlgModal24->Title = t("Warning");
        $this->dlgModal24->HeaderClasses = 'btn-danger';
        $this->dlgModal24->addCloseButton(t("I understand"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // DELETE

        $this->dlgModal25 = new Bs\Modal($this);
        $this->dlgModal25->Title = t('Tip');
        $this->dlgModal25->Text = t('<p style="margin-top: 15px;">Sorry, this reserved folder or file cannot be deleted!</p>
                                    <p style="margin-top: 15px;">Choose another folder or file!</p>');
        $this->dlgModal25->HeaderClasses = 'btn-darkblue';
        $this->dlgModal25->addCloseButton(t("I close the window"));

        $this->dlgModal26 = new Bs\Modal($this);
        $this->dlgModal26->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">It is not possible to delete the main directory!</p>');
        $this->dlgModal26->Title = t("Warning");
        $this->dlgModal26->HeaderClasses = 'btn-danger';
        $this->dlgModal26->addCloseButton(t("I understand"));

        $this->dlgModal27 = new Bs\Modal($this);
        $this->dlgModal27->AutoRenderChildren = true;
        $this->dlgModal27->Title = t('Delete files or folders');
        $this->dlgModal27->HeaderClasses = 'btn-danger';
        $this->dlgModal27->addButton(t("I will continue"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal27->addCloseButton(t("I'll cancel"));
        $this->dlgModal27->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('startDeletionProcess_Click'));
        $this->dlgModal27->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('dataClearing_Click'));

        $this->dlgModal28 = new Bs\Modal($this);
        $this->dlgModal28->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The selected files and folders have been successfully deleted!</p>');
        $this->dlgModal28->Title = t("Success");
        $this->dlgModal28->HeaderClasses = 'btn-success';
        $this->dlgModal28->addCloseButton(t("Ok"));

        $this->dlgModal29 = new Bs\Modal($this);
        $this->dlgModal29->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Error while deleting items!</p>');
        $this->dlgModal29->Title = t("Warning");
        $this->dlgModal29->HeaderClasses = 'btn-danger';
        $this->dlgModal29->addCloseButton(t("I understand"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // MOVE

        $this->dlgModal30 = new Bs\Modal($this);
        $this->dlgModal30->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">It is not possible to move the main directory!</p>');
        $this->dlgModal30->Title = t("Warning");
        $this->dlgModal30->HeaderClasses = 'btn-danger';
        $this->dlgModal30->addCloseButton(t("I understand"));

        $this->dlgModal31 = new Bs\Modal($this);
        $this->dlgModal31->Title = t('Tip');
        $this->dlgModal31->Text = t('<p style="margin-top: 15px;">Sorry, this reserved folder or file cannot be moved!</p>
                                    <p style="margin-top: 15px;">Choose another folder or file!</p>');
        $this->dlgModal31->HeaderClasses = 'btn-darkblue';
        $this->dlgModal31->addCloseButton(t("I close the window"));

        $this->dlgModal32 = new Bs\Modal($this);
        $this->dlgModal32->AutoRenderChildren = true;
        $this->dlgModal32->Title = t('Move files or folders');
        $this->dlgModal32->HeaderClasses = 'btn-default move-class';
        $this->dlgModal32->addCssClass("move-class");
        $this->dlgModal32->addButton(t("I will continue"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal32->addCloseButton(t("I'll cancel"));
        $this->dlgModal32->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('startMovingProcess_Click'));
        $this->dlgModal32->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('dataClearing_Click'));

        $this->dlgModal33 = new Bs\Modal($this);
        $this->dlgModal33->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">The selected files and folders have been successfully moved!</p>');
        $this->dlgModal33->Title = t("Success");
        $this->dlgModal33->HeaderClasses = 'btn-success';
        $this->dlgModal33->addCloseButton(t("Ok"));

        $this->dlgModal34 = new Bs\Modal($this);
        $this->dlgModal34->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Error while moving items!</p>');
        $this->dlgModal34->Title = t("Warning");
        $this->dlgModal34->HeaderClasses = 'btn-danger';
        $this->dlgModal34->addCloseButton(t("I understand"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // DOWNLOAD

        $this->dlgModal35 = new Bs\Modal($this);
        $this->dlgModal35->Title = t('Warning');
        $this->dlgModal35->Text = t('<p style="margin-top: 15px;">Operations with archives are not available!</p>');
        $this->dlgModal35->HeaderClasses = 'btn-danger';
        $this->dlgModal35->addCloseButton(t("I understand"));

        $this->dlgModal36 = new Bs\Modal($this);
        $this->dlgModal36->Text = '<p style="line-height: 25px; margin-bottom: 2px;">It is not possible to download the main directory!</p>';
        $this->dlgModal36->Title = t("Warning");
        $this->dlgModal36->HeaderClasses = 'btn-danger';
        $this->dlgModal36->addCloseButton(t("I understand"));

        $this->dlgModal37 = new Bs\Modal($this);
        $this->dlgModal37->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Failed to create archive for download!</p>');
        $this->dlgModal37->Title = t("Warning");
        $this->dlgModal37->HeaderClasses = 'btn-danger';
        $this->dlgModal37->addCloseButton(t("I understand"));

        $this->dlgModal38 = new Bs\Modal($this);
        $this->dlgModal38->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">ZipArchive is not available!</p>');
        $this->dlgModal38->Title = t("Warning");
        $this->dlgModal38->HeaderClasses = 'btn-danger';
        $this->dlgModal38->addCloseButton(t("I understand"));

        $this->dlgModal39 = new Bs\Modal($this);
        $this->dlgModal39->Title = t('Tip');
        $this->dlgModal39->Text = t('<p style="margin-top: 15px;">Empty folder(s) do not contain files!</p>
                                    <p style="margin-top: 15px;">Then no packing and no downloading!</p>');
        $this->dlgModal39->HeaderClasses = 'btn-darkblue';
        $this->dlgModal39->addCloseButton(t("I close the window"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // CROP

        $this->dlgModal40 = new Bs\Modal($this);
        $this->dlgModal40->Title = t('Tip');
        $this->dlgModal40->Text = t('<p style="margin-top: 15px;">Please select a image!</p>');
        $this->dlgModal40->HeaderClasses = 'btn-darkblue';
        $this->dlgModal40->addCloseButton(t("I close the window"));

        $this->dlgModal41 = new Bs\Modal($this);
        $this->dlgModal41->Title = t('Tip');
        $this->dlgModal41->Text = t('<p style="margin-top: 15px;">Please select only one image to crop!</p>
                                    <p style="margin-top: 15px;">Allowed file types: jpg, jpeg, png.</p>');
        $this->dlgModal41->HeaderClasses = 'btn-darkblue';
        $this->dlgModal41->addCloseButton(t("I close the window"));

        $this->dlgModal42 = new Bs\Modal($this);
        $this->dlgModal42->Title = t('Tip');
        $this->dlgModal42->Text = t('<p style="margin-top: 15px;">Please select only one image to crop!</p>');
        $this->dlgModal42->HeaderClasses = 'btn-darkblue';
        $this->dlgModal42->addCloseButton(t("I close the window"));

        $this->dlgModal43 = new Bs\Modal($this);
        $this->dlgModal43->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Image cropping succeeded!</p>');
        $this->dlgModal43->Title = t("Success");
        $this->dlgModal43->HeaderClasses = 'btn-success';
        $this->dlgModal43->addCloseButton(t("I close the window"));

        $this->dlgModal44 = new Bs\Modal($this);
        $this->dlgModal44->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Image cropping failed!</p>');
        $this->dlgModal44->Title = t("Warning");
        $this->dlgModal44->HeaderClasses = 'btn-danger';
        $this->dlgModal44->addCloseButton(t("I understand"));

        $this->dlgModal45 = new Bs\Modal($this);
        $this->dlgModal45->Text = t('<p style="margin-top: 15px;">The image is invalid for cropping!</p>
                                    <p style="margin-top: 15px;">It is recommended to delete this image and upload it again!</p>');
        $this->dlgModal45->Title = t("Warning");
        $this->dlgModal45->HeaderClasses = 'btn-danger';
        $this->dlgModal45->addCloseButton(t("I understand"));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // CSRF PROTECTION

        $this->dlgModal46 = new Bs\Modal($this);
        $this->dlgModal46->Text = t('<p style="margin-top: 15px;">CSRF Token is invalid! The request was aborted.</p>');
        $this->dlgModal46->Title = t("Warning");
        $this->dlgModal46->HeaderClasses = 'btn-danger';
        $this->dlgModal46->addCloseButton(t("I understand"));
    }

    /**
     * Initializes and sets up two DestinationInfo panels with specific modal dialogs.
     *
     * @return void
     */
    public function portedCheckDestination()
    {
        $pnl1 = new Q\Plugin\DestinationInfo($this->dlgModal5);
        $pnl2 = new Q\Plugin\DestinationInfo($this->dlgModal8);
    }

    /**
     * Initializes the components necessary for adding a folder through a modal interface.
     * This method sets up error labels and a text box for folder name input, configuring
     * their styles, visibility, and required attributes.
     *
     * @return void
     */
    public function portedAddFolderTextBox()
    {
        $this->lblError = new Q\Plugin\Label($this->dlgModal9);
        $this->lblError->Text = t('Folder cannot be created without name!');
        $this->lblError->addCssClass("modal-error-text hidden");
        $this->lblError->setCssStyle('color', '#ff0000');
        $this->lblError->setCssStyle('font-weight', 600);
        $this->lblError->setCssStyle('padding-top', '5px');
        $this->lblError->UseWrapper = false;

        $this->lblSameName = new Q\Plugin\Label($this->dlgModal9);
        $this->lblSameName->Text = t('Cannot create a folder with the same name!');
        $this->lblSameName->addCssClass("modal-error-same-text hidden");
        $this->lblSameName->setCssStyle('color', '#ff0000');
        $this->lblSameName->setCssStyle('font-weight', 600);
        $this->lblSameName->setCssStyle('padding-top', '5px');
        $this->lblSameName->UseWrapper = false;

        $this->txtAddFolder = new Bs\TextBox($this->dlgModal9);
        $this->txtAddFolder->setHtmlAttribute('autocomplete', 'off');
        $this->txtAddFolder->addCssClass("modal-check-textbox");
        $this->txtAddFolder->setCssStyle('margin-top', '15px');
        $this->txtAddFolder->setCssStyle('margin-bottom', '15px');
        $this->txtAddFolder->setHtmlAttribute('required', 'required');
        $this->txtAddFolder->UseWrapper = false;
    }

    /**
     * Initializes and configures text boxes and error label messages for renaming functionality.
     *
     * @return void
     */
    public function portedRenameTextBox()
    {
        $this->lblDirectoryError = new Q\Plugin\Label($this->dlgModal15);
        $this->lblDirectoryError->Text = t('The name of the main directory cannot be changed!');
        $this->lblDirectoryError->addCssClass("modal-error-directory hidden");
        $this->lblDirectoryError->setCssStyle('font-weight', 400);
        $this->lblDirectoryError->setCssStyle('padding-top', '5px');
        $this->lblDirectoryError->UseWrapper = false;

        $this->lblError = new Q\Plugin\Label($this->dlgModal15);
        $this->lblError->Text = t('Cannot rename a folder or file without a name!');
        $this->lblError->addCssClass("modal-error-text hidden");
        $this->lblError->setCssStyle('color', '#ff0000');
        $this->lblError->setCssStyle('font-weight', 600);
        $this->lblError->setCssStyle('padding-top', '5px');
        $this->lblError->UseWrapper = false;

        $this->lblRenameName = new Q\Plugin\Label($this->dlgModal15);
        $this->lblRenameName->Text = t('This name cannot be used because it is already in use!');
        $this->lblRenameName->addCssClass("modal-error-rename-text hidden");
        $this->lblRenameName->setCssStyle('color', '#ff0000');
        $this->lblRenameName->setCssStyle('font-weight', 600);
        $this->lblRenameName->setCssStyle('padding-top', '5px');
        $this->lblRenameName->UseWrapper = false;

        $this->txtRename = new Bs\TextBox($this->dlgModal15);
        $this->txtRename->setHtmlAttribute('autocomplete', 'off');
        $this->txtRename->addCssClass("modal-check-rename-textbox");
        $this->txtRename->setCssStyle('margin-top', '15px');
        $this->txtRename->setCssStyle('margin-bottom', '15px');
        $this->txtRename->setHtmlAttribute('required', 'required');
        $this->txtRename->UseWrapper = false;
    }

    /**
     * Initializes and configures labels and a dropdown list box for managing source and destination folder selection.
     *
     * @return void
     */
    public function portedCopyingListBox()
    {
        $this->lblDestinationError = new Q\Plugin\Label($this->dlgModal22);
        $this->lblDestinationError->Text = t('Please select a destination folder!');
        $this->lblDestinationError->addCssClass('destination-error hidden');
        $this->lblDestinationError->setCssStyle('width', '100%');
        $this->lblDestinationError->setCssStyle('color', '#ff0000');
        $this->lblDestinationError->setCssStyle('font-weight', 600);
        $this->lblDestinationError->setCssStyle('padding-top', '5px');
        $this->lblDestinationError->UseWrapper = false;

        $this->lblCourceTitle = new Q\Plugin\Label($this->dlgModal22);
        $this->lblCourceTitle->Text = t('Source folder: ');
        $this->lblCourceTitle->addCssClass('source-title');
        $this->lblCourceTitle->setCssStyle('width', '100%');
        $this->lblCourceTitle->setCssStyle('font-weight', 600);
        $this->lblCourceTitle->setCssStyle('padding-right', '5px');
        $this->lblCourceTitle->setCssStyle('padding-bottom', '5px');
        $this->lblCourceTitle->UseWrapper = false;

        $this->lblCourcePath = new Q\Plugin\Label($this->dlgModal22);
        $this->lblCourcePath->addCssClass('source-path');
        $this->lblCourcePath->setCssStyle('width', '100%');
        $this->lblCourcePath->setCssStyle('font-weight', 400);
        $this->lblCourcePath->setCssStyle('padding-right', '5px');
        $this->lblCourcePath->setCssStyle('padding-bottom', '5px');
        $this->lblCourcePath->UseWrapper = false;

        $this->lblCopyingTitle = new Q\Plugin\Label($this->dlgModal22);
        $this->lblCopyingTitle->Text = t('Destination folder: ');
        $this->lblCopyingTitle->setCssStyle('width', '100%');
        $this->lblCopyingTitle->setCssStyle('font-weight', 600);
        $this->lblCopyingTitle->setCssStyle('padding-right', '5px');
        $this->lblCopyingTitle->setCssStyle('padding-bottom', '5px');
        $this->lblCopyingTitle->UseWrapper = false;

        $this->dlgCopyingDestination = new Q\Plugin\Select2($this->dlgModal22);
        $this->dlgCopyingDestination->Width = '100%';
        $this->dlgCopyingDestination->MinimumResultsForSearch = -1; // If you want to remove the search box, set it to "-1"
        $this->dlgCopyingDestination->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->dlgCopyingDestination->AddItem(t('- Select One -'), null);
        $this->dlgCopyingDestination->Theme = 'web-vauu';
        $this->dlgCopyingDestination->AddAction(new Q\Event\Change(), new Q\Action\Ajax('dlgDestination_Change'));
    }

    /**
     * Configures and initializes multiple label components for a modal dialog related to the deletion of files and folders.
     * These labels display various deletion-related warnings, errors, and informational messages to the user.
     *
     * @return void
     */
    public function portedDeleteBox()
    {
        $this->lblDeletionWarning = new Q\Plugin\Label($this->dlgModal27);
        $this->lblDeletionWarning->Text = t('Are you sure you want to permanently delete these files and folders?');
        $this->lblDeletionWarning->addCssClass("deletion-warning-text");
        $this->lblDeletionWarning->setCssStyle('width', '100%');
        $this->lblDeletionWarning->setCssStyle('color', '#ff0000');
        $this->lblDeletionWarning->setCssStyle('font-weight', 600);
        $this->lblDeletionWarning->setCssStyle('padding-top', '5px');
        $this->lblDeletionWarning->UseWrapper = false;

        $this->lblDeletionInfo = new Q\Plugin\Label($this->dlgModal27);
        $this->lblDeletionInfo->Text = t("Can\'t undo it afterwards!");
        $this->lblDeletionInfo->addCssClass("deletion-info-text");
        $this->lblDeletionInfo->setCssStyle('width', '100%');
        $this->lblDeletionInfo->setCssStyle('color', '#ff0000');
        $this->lblDeletionInfo->setCssStyle('font-weight', 600);
        $this->lblDeletionInfo->setCssStyle('padding-top', '5px');
        $this->lblDeletionInfo->UseWrapper = false;

        $this->lblDeleteError = new Q\Plugin\Label($this->dlgModal27);
        $this->lblDeleteError->Text = t('Files are locked or cannot be deleted together with folders!');
        $this->lblDeleteError->addCssClass("delete-error-text hidden");
        $this->lblDeleteError->setCssStyle('width', '100%');
        $this->lblDeleteError->setCssStyle('color', '#ff0000');
        $this->lblDeleteError->setCssStyle('font-weight', 600);
        $this->lblDeleteError->setCssStyle('padding-top', '5px');
        $this->lblDeleteError->UseWrapper = false;

        $this->lblDeleteInfo = new Q\Plugin\Label($this->dlgModal27);
        $this->lblDeleteInfo->Text = t('Unlocked files can be deleted!');
        $this->lblDeleteInfo->addCssClass("delete-info-text hidden");
        $this->lblDeleteInfo->setCssStyle('width', '100%');
        $this->lblDeleteInfo->setCssStyle('font-weight', 600);
        $this->lblDeleteInfo->setCssStyle('padding-top', '5px');
        $this->lblDeleteInfo->setCssStyle('padding-bottom', '15px');
        $this->lblDeleteInfo->UseWrapper = false;

        $this->lblDeleteTitle = new Q\Plugin\Label($this->dlgModal27);
        $this->lblDeleteTitle->Text = t('Files and folders to be deleted: ');
        $this->lblDeleteTitle->setCssStyle('font-weight', 600);
        $this->lblDeleteTitle->setCssStyle('padding-right', '5px');
        $this->lblDeleteTitle->setCssStyle('padding-bottom', '5px');
        $this->lblDeleteTitle->UseWrapper = false;

        $this->lblDeletePath = new Q\Plugin\Label($this->dlgModal27);
        $this->lblDeletePath->addCssClass('delete-path');
        $this->lblDeletePath->setCssStyle('font-weight', 400);
        $this->lblDeletePath->setCssStyle('padding-right', '5px');
        $this->lblDeletePath->setCssStyle('padding-bottom', '5px');
        $this->lblDeletePath->UseWrapper = false;
    }

    /**
     * Initializes and configures the components required for the moving list box functionality.
     * This method creates several labels and a select box to handle displaying error messages,
     * informational text, and user selections for the moving operation dialogs.
     *
     * @return void
     */
    public function portedMovingListBox()
    {
        $this->lblMovingError = new Q\Plugin\Label($this->dlgModal32);
        $this->lblMovingError->Text = t('Files are locked or cannot be moved together  with folders!');
        $this->lblMovingError->addCssClass("move-error-text hidden");
        $this->lblMovingError->setCssStyle('width', '100%');
        $this->lblMovingError->setCssStyle('color', '#ff0000');
        $this->lblMovingError->setCssStyle('font-weight', 600);
        $this->lblMovingError->setCssStyle('padding-top', '5px');
        $this->lblMovingError->UseWrapper = false;

        $this->lblMoveInfo = new Q\Plugin\Label($this->dlgModal32);
        $this->lblMoveInfo->Text = t('Unlocked files can be moved!');
        $this->lblMoveInfo->addCssClass("move-info-text hidden");
        $this->lblMoveInfo->setCssStyle('width', '100%');
        $this->lblMoveInfo->setCssStyle('font-weight', 600);
        $this->lblMoveInfo->setCssStyle('padding-top', '5px');
        $this->lblMoveInfo->setCssStyle('padding-bottom', '15px');
        $this->lblMoveInfo->UseWrapper = false;

        $this->lblMovingDestinationError = new Q\Plugin\Label($this->dlgModal32);
        $this->lblMovingDestinationError->Text = t('Please select a destination folder!');
        $this->lblMovingDestinationError->addCssClass('destination-moving-error hidden');
        $this->lblMovingDestinationError->setCssStyle('color', '#ff0000');
        $this->lblMovingDestinationError->setCssStyle('font-weight', 600);
        $this->lblMovingDestinationError->setCssStyle('padding-top', '5px');
        $this->lblMovingDestinationError->UseWrapper = false;

        $this->lblMovingCourceTitle = new Q\Plugin\Label($this->dlgModal32);
        $this->lblMovingCourceTitle->Text = t('Source folder: ');
        $this->lblMovingCourceTitle->addCssClass('moving-source-title');
        $this->lblMovingCourceTitle->setCssStyle('font-weight', 600);
        $this->lblMovingCourceTitle->setCssStyle('padding-right', '5px');
        $this->lblMovingCourceTitle->setCssStyle('padding-bottom', '5px');
        $this->lblMovingCourceTitle->UseWrapper = false;

        $this->lblMovingCourcePath = new Q\Plugin\Label($this->dlgModal32);
        $this->lblMovingCourcePath->addCssClass('moving-source-path');
        $this->lblMovingCourcePath->setCssStyle('font-weight', 400);
        $this->lblMovingCourcePath->setCssStyle('padding-right', '5px');
        $this->lblMovingCourcePath->setCssStyle('padding-bottom', '5px');
        $this->lblMovingCourcePath->UseWrapper = false;

        $this->lblMovingTitle = new Q\Plugin\Label($this->dlgModal32);
        $this->lblMovingTitle->Text = t('Destination folder: ');
        $this->lblMovingTitle->Width = '100%';
        $this->lblMovingTitle->setCssStyle('font-weight', 600);
        $this->lblMovingTitle->setCssStyle('padding-right', '5px');
        $this->lblMovingTitle->setCssStyle('padding-bottom', '5px');
        $this->lblMovingTitle->UseWrapper = false;

        $this->dlgMovingDestination = new Q\Plugin\Select2($this->dlgModal32);
        $this->dlgMovingDestination->Width = '100%';
        $this->dlgMovingDestination->MinimumResultsForSearch = -1; // If you want to remove the search box, set it to "-1"
        $this->dlgMovingDestination->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->dlgMovingDestination->AddItem(t('- Select One -'), null);
        $this->dlgMovingDestination->Theme = 'web-vauu';
        $this->dlgMovingDestination->AddAction(new Q\Event\Change(), new Q\Action\Ajax('dlgDestination_Change'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // REPOSITORY LINK

    /**
     * Handles the append data operation and executes the necessary JavaScript.
     *
     * @param ActionParams $params Parameters containing information for the action.
     * @return array The updated array containing appended data.
     */
    public function appendData_Click(ActionParams $params)
    {
        $this->arrSomeArray = [["data-id" => 1, "data-path" => "", "data-item-type" => "dir", "data-locked" => 0, "data-activities-locked" => 0]];
        Application::executeJavaScript(sprintf("$('.breadcrumbs').empty()"));

        return $this->arrSomeArray;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // UPLOAD

    /**
     * Handles the initiation of the upload process by validating selected folders
     * and setting required session variables. If any conditions are not met,
     * appropriate dialog messages are displayed and the process is halted.
     *
     * @param ActionParams $params The parameters associated with the action triggering this method.
     * @return void This method does not return a value, but may execute JavaScript or show dialog messages.
     */
    public function uploadStart_Click(ActionParams $params)
    {
        clearstatcache();

        Application::executeJavaScript("$('.alert').remove();");

        if ($this->dataScan() !== $this->scan($this->objManager->RootPath)) {
            $this->dlgModal1->showDialogBox(); // Corrupted table "folders" in the database or directory "upload" in the file system! ...
            return;
        }

        if (!$this->arrSomeArray) {
            $this->showDialog(3); // Select only a specific folder for file uploads!
            return;
        }

        $locked = $this->arrSomeArray[0]["data-activities-locked"];

        if ($locked == 1) {
            $this->showDialog(2); // Sorry, files cannot be added to this reserved folder! ...
            return;
        }

        if ($this->arrSomeArray[0]["data-item-type"] !== "dir") {
            $this->showDialog(3); // Select only a specific folder for file uploads!
            return;
        }

        if (count($this->arrSomeArray) !== 1) {
            $this->showDialog(7); // Please choose only one folder for creating a new folder!
            return;
        }

        $this->showDialog(5); // Please check if the destination is correct!

        $this->intDataId = $this->arrSomeArray[0]["data-id"];
        $this->strDataPath = $this->arrSomeArray[0]["data-path"];
        $_SESSION['folderId'] = $this->intDataId;
        $_SESSION['filePath'] = $this->strDataPath;

        if ($this->strDataPath == "") {
            $_SESSION['folderId'] = 1;
            $_SESSION['filePath'] = "";
            Application::executeJavaScript(sprintf("$('.modalPath').append('/')"));
        } else {
            Application::executeJavaScript(sprintf("$('.modalPath').append('{$this->strDataPath}')"));
        }
    }

    /**
     * Displays a dialog box based on the provided modal number.
     *
     * @param int $modalNumber The numerical identifier for the dialog to be displayed.
     * @return void
     */
    private function showDialog($modalNumber)
    {
        $dialog = $this->getDialogByNumber($modalNumber);
        $dialog->showDialogBox();
    }

    /**
     * Retrieves the dialog object corresponding to the given modal number.
     *
     * @param int $modalNumber The number of the modal to retrieve.
     * @return object The dialog object associated with the given modal number,
     *                or dlgModal3 by default if the number is not recognized.
     */
    private function getDialogByNumber($modalNumber)
    {
        switch ($modalNumber) {
            case 2:
                return $this->dlgModal2;
            case 3:
                return $this->dlgModal3;
            case 5:
                return $this->dlgModal5;
            case 7:
                return $this->dlgModal7;
            default:
                // Default to dlgModal3 if an unknown modal number is provided.
                return $this->dlgModal3;
        }
    }

    /**
     * Initiates the upload process by modifying the visibility of specific UI elements
     * and executing a JavaScript script to set up the required upload interface.
     *
     * @param ActionParams $params Information related to the action triggering this function.
     * @return void
     */
    public function startUploadProcess_Click(ActionParams $params)
    {
        $script = "
            $('.fileupload-buttonbar').removeClass('hidden');
            $('.upload-wrapper').removeClass('hidden');
            $('.fileupload-donebar').addClass('hidden');
            $('body').removeClass('no-scroll');
            $('.head').addClass('hidden');
            $('.files-heading').addClass('hidden');
            $('.scroll-wrapper').addClass('hidden');
            $('.alert').remove();
        ";

        Application::executeJavaScript($script);

        $this->dlgModal5->hideDialogBox(); // Please check if the destination is correct!
    }

    /**
     * Handles the action triggered when confirming the parent folder operation.
     *
     * @param ActionParams $params Parameters passed to the action triggered by the user.
     * @return void
     */
    public function confirmParent_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $path = $this->objManager->RootPath . $this->strDataPath;

        $folderId = isset($_SESSION['folderId']) ? $_SESSION['folderId'] : null;

        if ($folderId) {
            $objFolder = Folders::loadById($folderId);

            // Check if the folder exists before updating properties
            if ($objFolder) {
                $objFolder->setLockedFile(1);
                $objFolder->setMtime(filemtime($path));
                $objFolder->save();
            }
        }
    }

    /**
     * Handles the click event for the "Back" button and performs UI updates.
     *
     * @param ActionParams $params Parameters related to the triggered action.
     * @return void
     */
    public function btnBack_Click(ActionParams $params)
    {
        $script = "
            $('.fileupload-buttonbar').addClass('hidden');
            $('.upload-wrapper').addClass('hidden');
            $('body').addClass('no-scroll');
            $('.head').removeClass('hidden');
            $('.files-heading').removeClass('hidden');
            $('.scroll-wrapper').removeClass('hidden');
            $('.alert').remove();
        ";

        Application::executeJavaScript($script);

        $this->objManager->refresh();
    }

    /**
     * Handles the actions to be performed when the "Done" button is clicked.
     *
     * @param ActionParams $params The parameters for the action triggered by the button click.
     * @return void This method does not return a value.
     */
    protected function btnDone_Click(ActionParams $params)
    {
        unset($_SESSION['folderId']);
        unset($_SESSION['filePath']);

        Application::executeJavaScript("
            $('.fileupload-buttonbar').addClass('hidden');
            $('.upload-wrapper').addClass('hidden');
            $('body').addClass('no-scroll');
            $('.head').removeClass('hidden');
            $('.files-heading').removeClass('hidden');
            $('.scroll-wrapper').removeClass('hidden');
            $('.alert').remove();
        ");

        $this->objManager->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // NEW FOLDER

    /**
     * Handles the click event for adding a folder. This method performs various checks to determine
     * if the process can proceed, including verifying the database and file system states,
     * validating input data, and setting certain properties if all checks are satisfied.
     *
     * @param ActionParams $params The parameters associated with the action triggering this method.
     * @return void No return value. Displays modal dialog boxes for errors or confirmations as needed.
     */
    public function btnAddFolder_Click(ActionParams $params)
    {
        clearstatcache();

        if ($this->dataScan() !== $this->scan($this->objManager->RootPath)) {
            $this->dlgModal1->showDialogBox(); // Corrupted table "folders" in the database or directory "upload" in the file system! ...
            return;
        }

        if (!$this->arrSomeArray) {
            $this->dlgModal7->showDialogBox();
            return;
        }

        $locked = $this->arrSomeArray[0]["data-activities-locked"];

        if ($locked == 1) {
            $this->dlgModal6->showDialogBox();
            return;
        }

        if (count($this->arrSomeArray) !== 1 || $this->arrSomeArray[0]["data-item-type"] !== "dir") {
            $this->dlgModal7->showDialogBox();
            return;
        }

        $this->dlgModal8->showDialogBox();
        $this->intDataId = $this->arrSomeArray[0]["data-id"];
        $this->strDataPath = $this->arrSomeArray[0]["data-path"];

        if ($this->strDataPath == "") {
            $this->intDataId = 1;
            $this->strDataPath = "";
            Application::executeJavaScript(sprintf("$('.modalPath').append('/')"));
        } else {
            Application::executeJavaScript(sprintf("$('.modalPath').append('{$this->strDataPath}')"));
        }
    }

    /**
     * Initiates the process for adding a new folder. Updates session variables, manages modal dialog boxes,
     * clears relevant text fields, and binds JavaScript for UI interaction and validation during folder creation.
     *
     * @param ActionParams $params Contains parameters related to the action event.
     * @return void
     */
    public function startAddFolderProcess_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $_SESSION['fileId'] = $this->intDataId;
        $_SESSION['filePath'] = $this->strDataPath;

        $this->dlgModal8->hideDialogBox();
        $this->dlgModal9->showDialogBox(); // New folder name
        $this->txtAddFolder->Text = '';

        $javascript = "
        $('.modal-check-textbox').on('keyup keydown', function() {
            var length = $(this).val().length;
            var modalHeader = $('.modal-header');
            var modalFooterBtn = $('.modal-footer .btn-orange');

            if (length === 0) {
                modalHeader.removeClass('btn-default').addClass('btn-danger');
                $('.modal-error-same-text').addClass('hidden');
                $('.modal-error-text').removeClass('hidden');
                modalFooterBtn.attr('disabled', 'disabled');
            } else {
                modalHeader.removeClass('btn-danger').addClass('btn-default');
                $('.modal-error-same-text').addClass('hidden');
                $('.modal-error-text').addClass('hidden');
                modalFooterBtn.removeAttr('disabled', 'disabled');
            }
        });
    ";

        Application::executeJavaScript(sprintf($javascript));
    }

    /**
     * Handles the addition of a new folder when triggered.
     *
     * @param ActionParams $params Parameters associated with the action triggering this method.
     * @return void
     */
    public function addFolderName_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $path = $this->objManager->RootPath . $_SESSION['filePath'];
        $scanned_directory = array_diff(scandir($path), array('..', '.'));

        if (trim($this->txtAddFolder->Text) == "") {
            Application::executeJavaScript($this->getJavaScriptForEmptyFolder());
            return;
        }

        if (in_array(trim($this->txtAddFolder->Text), $scanned_directory)) {
            Application::executeJavaScript($this->getJavaScriptForDuplicateFolder());
            return;
        }

        $this->makeFolders($this->txtAddFolder->Text, $_SESSION['fileId'], $path);
        $this->dlgModal9->hideDialogBox();
    }

    /**
     * Generates JavaScript code to update the DOM elements of a modal when an empty folder condition is encountered.
     *
     * @return string The JavaScript code as a string, which modifies the modal's header, hides specific error text, displays other error text, and disables a button.
     */
    private function getJavaScriptForEmptyFolder()
    {
        return sprintf("
            $('.modal-header').removeClass('btn-default').addClass('btn-danger');
            $('.modal-error-same-text').addClass('hidden');
            $('.modal-error-text').removeClass('hidden');
            $('.modal-footer .btn-orange').attr('disabled', 'disabled');
        ");
    }

    /**
     * Generates JavaScript code for handling the duplicate folder modal.
     *
     * @return string JavaScript code to update the modal's classes and attributes for a duplicate folder error.
     */
    private function getJavaScriptForDuplicateFolder()
    {
        return sprintf("
            $('.modal-header').removeClass('btn-default').addClass('btn-danger');
            $('.modal-error-same-text').removeClass('hidden');
            $('.modal-error-text').addClass('hidden');
            $('.modal-footer .btn-orange').attr('disabled', 'disabled');
        ");
    }

    /**
     * Creates folders and sets up the corresponding folder structure with metadata.
     *
     * @param string $text The name of the folder to be created, sanitized for use in URLs.
     * @param int|null $id The ID of the parent folder, if applicable. Null if no parent folder exists.
     * @param string $path The base path where the folder will be created.
     * @return void
     */
    protected function makeFolders($text, $id, $path)
    {
        clearstatcache();

        $fullPath = $path . "/" . trim(QString::sanitizeForUrl($text));
        $relativePath = $this->objManager->getRelativePath($fullPath);

        Folder::makeDirectory($fullPath, 0777);

        if ($id) {
            $objFolder = Folders::loadById($id);
            if ($objFolder->getLockedFile() !== 1) {
                $objFolder->setMtime(filemtime($path));
                $objFolder->setLockedFile(1);
                $objFolder->save();
            }
        }

        $objAddFolder = new Folders();
        $objAddFolder->setParentId($id);
        $objAddFolder->setPath($relativePath);
        $objAddFolder->setName(trim($text));
        $objAddFolder->setType('dir');
        $objAddFolder->setMtime(filemtime($path));
        $objAddFolder->setLockedFile(0);
        $objAddFolder->save();

        foreach ($this->tempFolders as $tempFolder) {
            $tempPath = $this->objManager->TempPath . '/_files/' . $tempFolder . $relativePath;
            Folder::makeDirectory($tempPath, 0777);
        }

        $dialogBox = file_exists($fullPath) ? $this->dlgModal10 : $this->dlgModal11;
        $dialogBox->showDialogBox();

        unset($_SESSION['fileId']);
        unset($_SESSION['filePath']);
        $this->objManager->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // REFRESH

    /**
     * Handles the click event for the refresh button by invoking the refresh method on the manager object.
     *
     * @param ActionParams $params The parameters associated with the refresh action.
     * @return void This method does not return a value.
     */
    public function btnRefresh_Click(ActionParams $params)
    {
        $this->objManager->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // RENAME

    /**
     * Handles the click event for the Rename button. This method performs various checks and updates
     * before displaying appropriate dialog boxes or initiating the renaming process. The checks include
     * data integrity validation, activity lock status, and the presence of a valid data entry for renaming.
     *
     * @param ActionParams $params Parameters provided during the button click, which may include details
     *                             about the triggering action or event metadata.
     * @return void This method does not return a value, but it updates the internal state of the application,
     *              displays dialog boxes, or executes JavaScript functions based on the results of its checks.
     */
    public function btnRename_Click(ActionParams $params)
    {
        clearstatcache();

        if ($this->dataScan() !== $this->scan($this->objManager->RootPath)) {
            $this->dlgModal1->showDialogBox(); // Corrupted table "folders" in the database or directory "upload" in the file system! ...
            return;
        }

        if (!$this->arrSomeArray) {
            $this->dlgModal13->showDialogBox();
            return;
        }

        $locked = $this->arrSomeArray[0]["data-activities-locked"];

        if ($locked == 1) {
            $this->dlgModal12->showDialogBox();
            return;
        }

        if (count($this->arrSomeArray) !== 1) {
            $this->dlgModal14->showDialogBox();
            return;
        }

        $this->intDataId = $this->arrSomeArray[0]["data-id"];
        $this->strDataName = $this->arrSomeArray[0]["data-name"];
        $this->strDataPath = $this->arrSomeArray[0]["data-path"];
        $this->strDataType = $this->arrSomeArray[0]["data-item-type"];
        $this->intDataLocked = $this->arrSomeArray[0]["data-locked"];

        $this->txtRename->Text = $this->strDataName;

        $this->dlgModal15->showDialogBox();

        if ($this->txtRename->Text == "upload") {
            $this->showUploadError();
        } else {
            $this->showRenameJavaScript();
        }
    }

    /**
     * Executes JavaScript code to dynamically modify the DOM elements of a modal when a file upload error occurs.
     *
     * @return void This method does not return any value, as it directly executes the JavaScript code to handle upload errors.
     */
    private function showUploadError()
    {
        $script = "
            $('.modal-header').removeClass('btn-default').addClass('btn-danger');
            $('.modal-error-directory').removeClass('hidden');
            $('.modal-check-rename-textbox').addClass('hidden');
            $('.modal-error-rename-text').addClass('hidden');
            $('.modal-error-text').addClass('hidden');
            $('.modal-footer .btn-orange').attr('disabled', 'disabled');
        ";
        Application::executeJavaScript($script);
    }

    /**
     * Generates and executes JavaScript code to handle user input validation in a rename modal.
     * The script dynamically updates the modal's appearance and behavior based on the text length within the input field.
     *
     * @return void The method executes the JavaScript code directly and does not return a value.
     */
    private function showRenameJavaScript()
    {
        $script = "
            $('.modal-check-rename-textbox').on('keyup keydown', function() {
                var length = $('.modal-check-rename-textbox').val().length;
                if(length == 0) {
                    $('.modal-header').removeClass('btn-default').addClass('btn-danger');
                    $('.modal-error-rename-text').addClass('hidden');
                    $('.modal-error-text').removeClass('hidden');
                    $('.modal-footer .btn-orange').attr('disabled', 'disabled');
                } else {
                    $('.modal-header').removeClass('btn-danger').addClass('btn-default');
                    $('.modal-error-rename-text').addClass('hidden');
                    $('.modal-error-text').addClass('hidden');
                    $('.modal-footer .btn-orange').removeAttr('disabled', 'disabled');
                }
            });
        ";
        Application::executeJavaScript($script);
    }

    /**
     * Handles the click event for renaming an item (file or directory) based on the provided parameters.
     *
     * @param ActionParams $params The parameters associated with the rename action, including relevant data for processing the rename operation.
     * @return void This method does not return any value but initiates rename operations, updates the UI, and handles related post-rename tasks.
     */
    public function renameName_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $path = $this->objManager->RootPath . $this->strDataPath;

        // Check conditions preventing renaming
        if ($this->isRenameNotAllowed($path)) {
            $this->showRenameError();
            return;
        }

         // Perform the renaming based on the data type
        if ($this->strDataType == "dir") {
            $this->renameDirectory();
        } else {
            $this->renameFile();
        }

        // Additional operations after renaming
        $this->postRenameOperations();

        $this->objManager->refresh();
    }

    // Helper functions

    /**
     * Verifies if renaming a file or folder to the given name is not allowed by checking for naming conflicts in the directory.
     *
     * @param string $path The full path of the file or folder to be renamed.
     * @return bool Returns true if renaming is not allowed due to a naming conflict, otherwise false.
     */
    private function isRenameNotAllowed($path)
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $files = array_diff(scandir(dirname($path)), array('..', '.'));

        $matchedString = ($this->strDataType == "file") ? $this->txtRename->Text . "." . $ext : $this->txtRename->Text;

        return in_array($matchedString, $files);
    }

    /**
     * Executes JavaScript code to update the DOM elements of a modal when a rename error occurs.
     *
     * @return void Executes a JavaScript snippet that modifies the modal's header styling, displays the rename error message, hides other error messages, and disables a specific button in the modal footer.
     */
    private function showRenameError()
    {
        Application::executeJavaScript(sprintf("
        $('.modal-header').removeClass('btn-default').addClass('btn-danger');
        $('.modal-error-rename-text').removeClass('hidden');
        $('.modal-error-text').addClass('hidden');
        $('.modal-footer .btn-orange').attr('disabled', 'disabled');
    "));
    }

    /**
     * Renames a directory and updates all associated paths and data in the system, including subdirectories
     * and files. Handles directories with or without subfolders/files, ensuring all affected paths are updated
     * in the database and file system.
     *
     * @return void This method does not return a value but updates directory and file paths both in the file system
     *              and the database, ensuring data consistency.
     */
    private function renameDirectory()
    {
        // Perform directory renaming logic

        $path = $this->objManager->RootPath . $this->strDataPath;
        $parts = pathinfo($path);
        $sanitizedName = QString::sanitizeForUrl(trim($this->txtRename->Text));
        $this->strNewPath = $parts['dirname'] . '/' . $sanitizedName;

        $objFolders = Folders::loadAll();
        $objFiles = Files::loadAll();

        // If the folder does not contain subfolders and files, renaming the folder is easy. If this folder contains
        // subfolders and files, all names and paths in descending order must be renamed according to the same logic
        if ($this->intDataLocked == 0) {
            // If there are no subfolders or files in a folder, renaming is easy.
            if (is_dir($path)) {
                // We will immediately update the database accordingly.
                $objFolder = Folders::loadById($this->intDataId);
                $objFolder->Name = trim($this->txtRename->Text);
                $objFolder->Path = $this->objManager->getRelativePath($this->strNewPath);
                $objFolder->Mtime = time();
                $objFolder->save();

                $this->objManager->rename($path, $this->strNewPath);
            }

            // Here the files must be renamed according to the same logic in temp directories
            foreach ($this->tempFolders as $tempFolder) {
                if (is_dir($this->objManager->TempPath . '/_files/' . $tempFolder . $this->strDataPath)) {
                    $this->objManager->rename($this->objManager->TempPath . '/_files/' . $tempFolder . $this->strDataPath, $this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($this->strNewPath));
                }
            }

            $this->handleResult();
        } else {
            // If there are subfolders and files in the folder, they must also be renamed.
            $this->tempItems = $this->fullScanIds($this->intDataId);
            $arrUpdatehash = [];

            if ($this->intDataId) {
                $obj = Folders::loadById($this->intDataId);
                $obj->Name = trim($this->txtRename->Text);
                $obj->Mtime = time();
                $obj->save();
            }

            foreach ($objFolders as $objFolder) {
                foreach ($this->tempItems as $temp) {
                    if ($temp == $objFolder->getId()) {
                        $newPath = str_replace(basename($this->strDataPath), $sanitizedName, $objFolder->Path);
                        $this->strNewPath = $this->objManager->RootPath . $newPath;

                        $arrUpdatehash[] = $newPath;
                        $this->objManager->UpdatedHash = rawurlencode(dirname($arrUpdatehash[0]));

                        if (is_dir($this->objManager->RootPath . $objFolder->getPath())) {
                            $this->objManager->rename($this->objManager->RootPath . $objFolder->getPath(), $this->strNewPath);
                        }

                        foreach ($this->tempFolders as $tempFolder) {
                            if (is_dir($this->objManager->TempPath . '/_files/' . $tempFolder . $objFolder->getPath())) {
                                $this->objManager->rename($this->objManager->TempPath . '/_files/' . $tempFolder . $objFolder->getPath(), $this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($this->strNewPath));
                            }
                        }

                        if ($this->intDataLocked !== 0) {
                            $obj = Folders::loadById($objFolder->getId());
                            $obj->Path = $this->objManager->getRelativePath($this->strNewPath);
                            $obj->Mtime = time();
                            $obj->save();
                        }

                    }
                }
            }

            foreach ($objFiles as $objFile) {
                foreach ($this->tempItems as $temp) {
                    if ($temp == $objFile->getFolderId()) {
                        $newPath = str_replace(basename($this->strDataPath), $sanitizedName, $objFile->Path);
                        $this->strNewPath = $this->objManager->RootPath . $newPath;

                        if (is_file($this->objManager->RootPath . $objFile->getPath())) {
                            $this->objManager->rename($this->objManager->RootPath . $objFile->getPath(), $this->objManager->RootPath . $this->strNewPath);
                        }

                        $obj = Files::loadById($objFile->getId());
                        $obj->Path = $this->objManager->getRelativePath($this->strNewPath);
                        $obj->Mtime = time();
                        $obj->save();
                    }
                }
            }

            $this->handleResult();
        }
    }

    /**
     * Renames a file and updates its associated metadata in the database. The method also ensures that
     * the file is renamed consistently in related temporary directories if applicable.
     *
     * @return void This method does not return a value but performs file renaming operations, updates file metadata,
     *              and handles the result of the rename operation.
     */
    private function renameFile()
    {
        // Perform file renaming logic

        $path = $this->objManager->RootPath . $this->strDataPath;
        $parts = pathinfo($path);

        // The file name is changed in the main directory
        if (is_file($path)) {
            $this->strNewPath = $parts['dirname'] . '/' . trim($this->txtRename->Text) . '.' . strtolower($parts['extension']);
            $this->objManager->rename($this->objManager->RootPath . $this->strDataPath, $this->strNewPath);
        }

        // Here the files must be renamed according to the same logic in temp directories
        if (in_array(strtolower($parts['extension']), $this->arrAllowed)) {
            foreach ($this->tempFolders as $tempFolder) {
                if (is_file($this->objManager->TempPath . '/_files/' . $tempFolder . $this->strDataPath)) {
                    $this->objManager->rename($this->objManager->TempPath . '/_files/' . $tempFolder . $this->strDataPath, $this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($this->strNewPath));
                }
            }
        }

        $objFile = Files::loadById($this->intDataId);
        $objFile->Name = basename($this->strNewPath);
        $objFile->Path = $this->objManager->getRelativePath($this->strNewPath);
        $objFile->Size = filesize($this->strNewPath);
        $objFile->Mtime = time();
        $objFile->save();

        $this->handleResult();
    }

    /**
     * Handles the result of a rename operation by determining whether it was successful or failed and displaying the appropriate dialog box.
     *
     * @return void This method does not return any value but dynamically shows or hides dialog boxes based on the operation result and data type (directory or file).
     */
    private function handleResult()
    {
        // Handle success or failure scenarios after renaming

        if (file_exists($this->strNewPath)) {

            $this->dlgModal15->hideDialogBox(); // Rename the folder or file

            if ($this->strDataType == "dir") {
                $this->dlgModal16->showDialogBox(); // Folder name successfully changed!
            } else {
                $this->dlgModal18->showDialogBox(); // File name successfully changed!
            }
        } else {
            if ($this->strDataType == "dir") {
                $this->dlgModal17->showDialogBox(); // Folder name change failed!
            } else {
                $this->dlgModal19->showDialogBox(); // File name change failed!
            }
        }
    }

    /**
     * Executes JavaScript code to update the breadcrumbs in the application's UI after a rename operation,
     * if a specific condition related to the array size is met.
     *
     * @return void Executes JavaScript code directly without returning any value.
     */
    private function postRenameOperations()
    {
        if (count($this->arrSomeArray) === 1) {
            Application::executeJavaScript(sprintf("$('.breadcrumbs').empty()"));
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // CROP

    /**
     * Handles the crop button click event for managing image cropping and folder selection.
     * Performs validation checks on the selected image, verifies its properties, and retrieves
     * available folder data for user selection.
     *
     * @param ActionParams $params Action parameters containing the context of the button click.
     *
     * @return void Does not return a value. Displays modal dialogs or populates selection dialogs based on conditions.
     */
    public function btnCrop_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        clearstatcache();

        $this->strDataPath = $this->arrSomeArray[0]["data-path"];
        $fullFilePath = $this->objManager->RootUrl . $this->strDataPath;

        if ($this->dataScan() !== $this->scan($this->objManager->RootPath)) {
            $this->dlgModal1->showDialogBox(); // Corrupted table "folders" in the database or directory "upload" in the file system! ...
            return;
        }

        if (!$this->arrSomeArray) {
            $this->dlgModal40->showDialogBox(); // Please select a image!
            return;
        }

        if ($this->arrSomeArray[0]['data-item-type'] == 'file' &&
            !in_array(strtolower($this->arrSomeArray[0]['data-extension']), $this->arrCroppieTypes)) {
            $this->dlgModal41->showDialogBox(); // Please select only one image to crop! Allowed file types: jpg, jpeg, png.
            return;
        }

        if (count($this->arrSomeArray) !== 1 || $this->arrSomeArray[0]['data-item-type'] !== 'file') {
            $this->dlgModal42->showDialogBox(); // Please select only one image to crop!
            return;
        }

        // Check if the file exists and its size is 0 bytes
        if (file_exists($fullFilePath) && filesize($fullFilePath) === 0) {
            $this->dlgModal45->showDialogBox(); // The image is invalid for cropping! It is recommended to delete this image and upload it again!
            return;
        }

        $scanFolders = $this->scanForSelect();
        $folderData = [];

        foreach ($scanFolders as $folder) {
            if ($folder['activities_locked'] !== 1) {
                $level = $folder['depth'];
                if ($this->checkString($folder['path'])) {
                    $level = 0;
                }
                $folderData[] = [
                    'id' => $folder['path'],
                    'text' => $folder['name'],
                    'level' => $level,
                    'folderId' => $folder['id']
                ];
            }
        }

        $this->dlgPopup->showDialogBox();

        $this->dlgPopup->SelectedImage = $fullFilePath;
        $this->dlgPopup->Data = $folderData;
    }

    /**
     * Handles the click event for refreshing the object manager.
     * Checks the existence of the file at the specified path and displays the appropriate modal dialog box based on the result.
     * Also refreshes the object manager.
     *
     * @param ActionParams $params The parameters passed from the action triggering this method.
     * @return void This method does not return a value.
     */
    public function objManagerRefresh_Click(ActionParams $params)
    {
        if (file_exists($this->objManager->RootPath . $this->dlgPopup->FinalPath)) {
            $this->dlgModal43->showDialogBox(); // Image cropping succeeded!
        } else {
            $this->dlgModal44->showDialogBox(); // Image cropping failed!
        }

        $this->objManager->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // COPY

    /**
     * Handles the click event for the Copy button, performing operations related to copying folders and files.
     *
     * This method performs a series of tasks, including validation, data preparation, processing,
     * and updating the UI before initiating a copy operation. It interacts with folder and file data
     * and manages the user interface elements related to the copy functionality.
     *
     * @param ActionParams $params The parameters associated with the action event triggered by the Copy button.
     * @return void This method does not return a value but performs operations directly related to the copy functionality.
     */
    public function btnCopy_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objFolders = Folders::loadAll();
        $objFiles = Files::loadAll();

        // Check for conditions preventing copying
        if (!$this->validateCopyConditions()) {
            return;
        }

        // Prepare and send data to function fullCopy($src, $dst)
        $this->prepareCopyData();

        // Data validation and processing
        $this->processCopyData($objFolders, $objFiles);

        // UI-related operations
        $this->updateCopyDestinationDialog();

        // Show the copy dialog
        $this->showCopyDialog();
    }

    // Helper functions

    /**
     * Validates the conditions required for copying files or folders.
     * Checks the integrity of the file system and database, ensures that a valid selection exists,
     * and ensures that copying to the root directory is not attempted.
     *
     * @return bool Returns true if all conditions are met for copying; otherwise, returns false and shows an appropriate dialog box.
     */
    private function validateCopyConditions()
    {
        clearstatcache();

        if ($this->dataScan() !== $this->scan($this->objManager->RootPath)) {
            $this->dlgModal1->showDialogBox(); // Corrupted table "folders" in the database or directory "upload" in the file system! ...
            return false;
        }

        if (!$this->arrSomeArray) {
            $this->dlgModal20->showDialogBox(); // Please choose specific folder(s) or file(s)!
            return false;
        }

        if ($this->arrSomeArray[0]["data-id"] == 1 && $this->arrSomeArray[0]["data-path"] == "") {
            $this->dlgModal21->showDialogBox(); // It's not possible to copy to the root directory!
            return false;
        }

        return true;
    }

    /**
     * Prepares and processes data for copying by organizing selected items into a temporary array.
     * Iterates through input data, collects the necessary file paths, and stores them for further operations.
     *
     * @return void This function does not return any value, but modifies internal class properties to store the prepared data.
     */
    private function prepareCopyData()
    {
        // Preparing and sending data to the function fullCopy($src, $dst)

        $tempArr = [];

        foreach ($this->arrSomeArray as $arrSome) {
            $tempArr[] = $arrSome;
        }
        foreach ($tempArr as $temp) {
            if ($temp['data-path']) {
                $this->tempSelectedItems[] = $temp['data-path'];
            }
        }
    }

    /**
     * Processes the copying of data by handling both folder and file operations.
     *
     * @param object $objFolders An object containing information about the folders to be copied.
     * @param object $objFiles An object containing information about the files to be copied.
     *
     * @return void
     */
    private function processCopyData($objFolders, $objFiles)
    {
        // Processing logic for copying data

        $this->copyDirectory($objFolders);
        $this->copyFile($objFiles);
    }

    /**
     * Copies directory contents by scanning and processing folder IDs.
     *
     * This method processes an array of objects representing folders, identifying
     * and copying their paths based on specific conditions.
     *
     * @param array $objFolders Array of folder objects to be processed. Each object should provide methods to retrieve its ID and path.
     * @return void No return value, as the method operates directly on the class's internal properties.
     */
    private function copyDirectory($objFolders)
    {
        // Perform directory copying logic

        $dataFolders = [];
        $tempIds = [];

        foreach ($this->arrSomeArray as $arrSome) {
            if ($arrSome["data-item-type"] == "dir") {
                $dataFolders[] = $arrSome["data-id"];
            }
        }
        foreach ($dataFolders as $dataFolder) {
            $tempIds = array_merge($tempIds, $this->fullScanIds($dataFolder));

        }
        foreach ($objFolders as $objFolder) {
            foreach ($tempIds as $tempId) {
                if ($objFolder->getId() == $tempId) {
                    $this->tempItems[] = $objFolder->getPath();
                }
            }
            sort($this->tempItems);
        }
    }

    /**
     * Copies specified file objects by matching their folder or file IDs with predefined arrays and updating an internal temporary items list.
     *
     * @param array $objFiles An array of file objects to be processed. Each file object should provide methods to retrieve folder IDs and file paths.
     * @return void This method does not return a value; it updates the internal state by modifying the temporary items list.
     */
    private function copyFile($objFiles)
    {
        // Perform file copying logic

        $tempIds = [];
        $dataFiles = [];

        foreach ($objFiles as $objFile) {
            foreach ($tempIds as $tempId) {
                if ($objFile->getFolderId() == $tempId) {
                    $this->tempItems[] = $objFile->getPath();
                }
            }
            sort($this->tempItems);
        }

        foreach ($this->arrSomeArray as $arrSome) {
            if ($arrSome["data-item-type"] == "file") {
                $dataFiles[] = $arrSome["data-id"];
            }
        }
        foreach ($objFiles as $objFile) {
            foreach ($dataFiles as $dataFile) {
                if ($objFile->getId() == $dataFile) {
                    $this->tempItems[] = $objFile->getPath();
                }
            }
            sort($this->tempItems);
        }
    }

    /**
     * Updates the copy destination dialog interface by scanning directories, marking locked directories,
     * and populating the dialog with relevant items.
     *
     * This method identifies locked directories based on the current state and additional checks,
     * and then integrates the scanned paths into the copy destination dialog with appropriate markers.
     * Locked directories are highlighted based on specific conditions.
     *
     * @return void This method does not return a value.
     */
    private function updateCopyDestinationDialog()
    {
        // Update destination dialog UI

        $objPaths = $this->scanForSelect();

        foreach ($this->tempItems as $tempItem) {
            if (is_dir($this->objManager->RootPath . $tempItem)) {
                $this->objLockedDirs[] = $tempItem;
            }
        }

        if ($objPaths) foreach ($objPaths as $objPath) {
            if ($objPath['activities_locked'] == 1) {
                array_push($this->objLockedDirs, $objPath["path"]);
            }
        }

        if ($objPaths) foreach ($objPaths as $objPath) {
            if (in_array($objPath["path"], $this->objLockedDirs)) {
                $mark = true;
            } else {
                $mark = false;
            }
            $this->dlgCopyingDestination->AddItem($this->printDepth($objPath['name'], $objPath['parent_id'], $objPath['depth']), $objPath, null, $mark);
        }
    }

    /**
     * Displays the copy dialog and updates its state based on the conditions provided.
     * Adjusts labels, styles, enabled states of components, and executes necessary JavaScript code.
     * Specifically, it handles the enabling/disabling of action buttons and shows the modal dialog for copying operations.
     *
     * @return void This method does not return any value.
     */
    private function showCopyDialog()
    {
        // Show the copy dialog

        if (count($this->tempItems) !== 0) {
            $source = join(', ', $this->tempItems);
            $this->lblCourcePath->Text = $source;
            $this->lblCourcePath->setCssStyle('color', '#000000');
            $this->dlgCopyingDestination->Enabled = true;
        } else {
            $this->lblCourcePath->Text = t("It is not possible to copy the main directory!");
            $this->lblCourcePath->setCssStyle('color', '#ff0000');
            $this->dlgCopyingDestination->Enabled = false;
        }

        if (count($this->tempItems) == 0 || $this->dlgCopyingDestination->SelectedValue == null) {
            Application::executeJavaScript(sprintf("
                $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        } else {
            Application::executeJavaScript(sprintf("
                $('.modal-footer .btn-orange').removeAttr('disabled', 'disabled');
            "));
        }

        $this->dlgModal22->showDialogBox();  // Copy files or folders
    }

    /**
     * Initiates the process of copying selected items to a specified destination.
     * Handles errors for invalid destination paths, executes the copy operation,
     * and updates the UI or handles the result accordingly.
     *
     * @param ActionParams $params Parameters for the action that trigger the copying process.
     * @return void This method does not return a value but performs operations such as validation,
     *              file copying, and result handling.
     */
    public function startCopyingProcess_Click(ActionParams $params)
    {
        $objPath = $this->dlgCopyingDestination->SelectedValue;

        if (!$objPath) {
            $this->handleCopyError();
            return;
        }

        $this->dlgModal22->hideDialogBox(); // Copy files or folders

        if ($this->dlgCopyingDestination->SelectedValue !== null) {
            foreach ($this->tempSelectedItems as $selectedItem) {
                $this->fullCopyItem($selectedItem, $objPath);
            }
        }

        $this->handleCopyResult();
    }

    // Helper functions

    /**
     * Handles the error encountered during a copy operation by resetting the destination, displaying an error, and performing cleanup tasks.
     *
     * @return void This method does not return a value as it focuses on error handling and cleanup processes.
     */
    private function handleCopyError()
    {
        $this->resetDestinationAndDisplayError();
        $this->cleanupAfterCopy();
    }

    /**
     * Resets the destination selection and displays an error message in the modal.
     * Updates the modal's appearance and functionality by modifying the DOM elements to indicate an error state.
     * Ensures that the destination dropdown is reset with the default option when no value is selected.
     *
     * @return void This method does not return a value, but executes JavaScript to update the modal's state and appearance.
     */
    private function resetDestinationAndDisplayError()
    {
        if ($this->dlgCopyingDestination->SelectedValue == null) {
            $this->dlgCopyingDestination->removeAllItems();
            $this->dlgCopyingDestination->AddItem(t('- Select One -'), null);

            Application::executeJavaScript(sprintf("
                $('.modal-header').removeClass('btn-default').addClass('btn-danger');
                $('.destination-error').removeClass('hidden');
                $('.source-title').addClass('hidden');
                $('.source-path').addClass('hidden');
                $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        }
    }

    /**
     * Copies the specified item from a source path to a destination path, maintaining the item's structure and contents.
     *
     * @param string $selectedItem The name or relative path of the item to be copied.
     * @param array $objPath An associative array containing the key 'path', which specifies the destination directory.
     *
     * @return void
     */
    private function fullCopyItem($selectedItem, $objPath)
    {
        $sourcePath = $this->objManager->RootPath . $selectedItem;
        $destinationPath = $this->objManager->RootPath . $objPath['path'] . "/" . basename($selectedItem);

        // Perform the copying logic
        $this->fullCopy($sourcePath, $destinationPath);
    }

    /**
     * Handles the result of a copy operation by determining success or failure based on the number of stored checks and temporary items.
     * Displays the appropriate modal dialog box and performs cleanup after the copying process.
     *
     * @return void This method does not return a value. It manages the state and side effects of the copy operation.
     */
    private function handleCopyResult()
    {
        if ($this->intStoredChecks >= count($this->tempItems)) {
            $this->dlgModal23->showDialogBox(); // The selected files and folders have been successfully copied!
        } else {
            $this->dlgModal24->showDialogBox(); // Error copying items!
        }

        // Clean up after the copying process
        $this->cleanupAfterCopy();
    }

    /**
     * Cleans up temporary data and refreshes the object manager after a copy operation.
     *
     * @return void This method does not return a value.
     */
    private function cleanupAfterCopy()
    {
        unset($this->tempSelectedItems);
        unset($this->tempItems);
        unset($this->objLockedDirs);
        $this->objManager->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // DELETE

    /**
     * Handles the click event for the delete button by performing validation checks and initiating the delete operation.
     *
     * @param ActionParams $params Parameters passed to the action, which may contain context-specific data for the click event.
     * @return void No value is returned. The method either performs an action, displays a specific dialog box for validation errors,
     *              or initiates the delete operation for selected folders or files.
     */
    public function btnDelete_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        clearstatcache();

        if ($this->dataScan() !== $this->scan($this->objManager->RootPath)) {
            $this->dlgModal1->showDialogBox(); // Corrupted table "folders" in the database or directory "upload" in the file system! ...
            return;
        }

        if (!$this->arrSomeArray) {
            $this->dlgModal20->showDialogBox(); // Please choose specific folder(s) or file(s)!
            return;
        }

        if ($this->arrSomeArray[0]["data-id"] == 1 && $this->arrSomeArray[0]["data-path"] == "") {
            $this->dlgModal26->showDialogBox(); // It's not possible to delete the root directory!
            return;
        }

        if ($this->arrSomeArray[0]["data-activities-locked"] == 1) {
            $this->dlgModal25->showDialogBox(); // Sorry, but this reserved folder or file cannot be deleted!
            return;
        }

        $this->initializeDeleteOperation();
    }

    // Helper functions

    /**
     * Initializes the delete operation by loading all folders and files, preparing and processing data for deletion,
     * and handling the necessary user interface updates associated with the delete operation.
     *
     * @return void This method does not return any value; it performs the required setup and UI handling for the delete operation.
     */
    private function initializeDeleteOperation()
    {
        $objFolders = Folders::loadAll();
        $objFiles = Files::loadAll();

        // Prepare and send data to function fullRemove($dir)
        $this->prepareDeleteData();

        // Data validation and processing
        $this->processDeleteData($objFolders, $objFiles);

        // UI-related operations
        $this->deleteListDialog();
    }

    /**
     * Prepares data by extracting paths from an internal array and storing them in a temporary property for further processing.
     *
     * @return void This method does not return a value but modifies the object's state by populating the tempSelectedItems property with data-path values.
     */
    private function prepareDeleteData()
    {
        // Preparing and sending data to the function fullRemove($dir)

        $tempArr = [];

        foreach ($this->arrSomeArray as $arrSome) {
            $tempArr[] = $arrSome;
        }
        foreach ($tempArr as $temp) {
            if ($temp['data-path']) {
                $this->tempSelectedItems[] = $temp['data-path'];
            }
        }
    }

    /**
     * Handles the processing of deleting specified folders and files.
     *
     * @param mixed $objFolders The object or data structure representing the folders to be deleted.
     * @param mixed $objFiles The object or data structure representing the files to be deleted.
     *
     * @return void
     */
    private function processDeleteData($objFolders, $objFiles)
    {
        // Processing logic for deleting data

        $this->deleteDirectory($objFolders, $objFiles);
        $this->deleteFile($objFiles);

    }

    /**
     * Processes the deletion of directories and their associated files by scanning folder IDs,
     * checking for locked files, and preparing a list of paths to be removed.
     *
     * @param array $objFolders An array of folder objects to be processed for deletion.
     * @param array $objFiles An array of file objects to be checked and processed for deletion.
     * @return void
     */
    private function deleteDirectory($objFolders, $objFiles)
    {
        $dataFolders = [];
        $dataFiles = [];
        $tempIds = [];

        foreach ($this->arrSomeArray as $arrSome) {
            if ($arrSome["data-item-type"] == "dir") {
                $dataFolders[] = $arrSome["data-id"];
            }
        }

        foreach ($dataFolders as $dataFolder) {
            $tempIds = array_merge($tempIds, $this->fullScanIds($dataFolder));
        }

        foreach ($objFiles as $objFile) {
            foreach ($tempIds as $tempId) {
                if ($objFile->getFolderId() == $tempId) {
                    $dataFiles[] = $objFile->getId();
                }
            }
        }

        // Here have to check whether the files are locked
        foreach ($objFiles as $objFile) {
            foreach ($dataFiles as $dataFile) {
                if ($objFile->getId() == $dataFile) {
                    if ($objFile->getLockedFile() === 1) {
                        $this->objLockedFiles++;
                    }
                }
            }
        }

        foreach ($objFolders as $objFolder) {
            foreach ($tempIds as $tempId) {
                if ($objFolder->getId() == $tempId) {
                    $this->tempItems[] = $objFolder->getPath();
                }
            }
            sort($this->tempItems);
        }
        foreach ($objFiles as $objFile) {
            foreach ($dataFiles as $dataFile) {
                if ($objFile->getId() == $dataFile) {
                    $this->tempItems[] = $objFile->getPath();
                }
            }
            sort($this->tempItems);
        }
    }

    /**
     * Deletes files by iterating through a passed collection of file objects and matching them with a pre-defined array of file IDs.
     * If a match is found, the file's path is added to a temporary array, and locked files are counted.
     *
     * @param array $objFiles An array of file objects, each providing methods to retrieve their ID, path, and locked status.
     * @return void This method does not return a value.
     */
    private function deleteFile($objFiles)
    {
        $dataFiles = [];

        foreach ($this->arrSomeArray as $arrSome) {
            if ($arrSome["data-item-type"] == "file") {
                $dataFiles[] = $arrSome["data-id"];
            }
        }

        foreach ($objFiles as $objFile) {
            foreach ($dataFiles as $dataFile) {
                if ($objFile->getId() == $dataFile) {



                    if ($objFile->getId() == $dataFile) {
                        $this->tempItems[] = $objFile->getPath();
                    }
                    // Here have to check whether the files are locked
                    if ($objFile->getLockedFile() > 0) {
                        $this->objLockedFiles++;
                    }
                }
            }
        }
    }

    /**
     * Configures and displays a modal dialog for deleting lists, updating the UI elements
     * to reflect the current state of the deletion process, including handling locked files
     * and allowing the user to proceed with or cancel the operation.
     *
     * @return void This method does not return a value. It updates the UI and displays the dialog box.
     */
    private function deleteListDialog()
    {
        // Update list dialog UI

        // Show folder and file names before deletion
        if (count($this->tempItems) !== 0) {
            $source = implode(', ', $this->tempItems);
            $this->lblDeletePath->Text = $source;
        }

        // Here have to check if some files have already been locked before.
        //If so, cancel and select unlocked files again...
        if ($this->objLockedFiles !== 0) {
            Application::executeJavaScript(sprintf("
                $('.deletion-warning-text').addClass('hidden');
                $('.deletion-info-text').addClass('hidden');
                $('.delete-error-text').removeClass('hidden');
                $('.delete-info-text').removeClass('hidden');
                $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        } else {
            Application::executeJavaScript(sprintf("
                $('.deletion-warning-text').removeClass('hidden');
                $('.deletion-info-text').removeClass('hidden');
                $('.delete-error-text').addClass('hidden');
                $('.delete-info-text').addClass('hidden');
                $('.modal-footer .btn-orange').removeAttr('disabled', 'disabled');
            "));
        }

        $this->dlgModal27->showDialogBox(); // Delete files or folders
    }

    /**
     * Initiates the deletion process for selected items and updates the application state accordingly.
     *
     * @param ActionParams $params Parameters associated with the action triggering the deletion process.
     * @return void No value is returned as the method performs the deletion process and updates the dialog box state.
     */
    public function startDeletionProcess_Click(ActionParams $params)
    {
        $this->dlgModal27->hideDialogBox(); // Delete files or folders

        foreach ($this->tempSelectedItems as $tempSelectedItem) {
            $this->fullRemoveItem($tempSelectedItem);
        }

        $this->handleDeletionResult();
    }

    // Helper functions

    /**
     * Removes an item fully from the system by its path.
     *
     * @param string $tempSelectedItem The selected item path to be removed, relative to the root path.
     * @return void
     */
    private function fullRemoveItem($tempSelectedItem)
    {
        $itemPath = $this->objManager->RootPath . $tempSelectedItem;

        // Perform the removal logic
        $this->fullRemove($itemPath);
    }

    /**
     * Handles the result of the deletion process by displaying an appropriate dialog box
     * based on the outcome and performing necessary cleanup operations.
     *
     * @return void
     */
    private function handleDeletionResult()
    {
        if ($this->intStoredChecks >= count($this->tempItems)) {
            $this->dlgModal28->showDialogBox(); // The selected files and folders have been successfully deleted!
        } else {
            $this->dlgModal29->showDialogBox(); // Error deleting items!
        }

        // Clean up after the deletion process
        $this->cleanupAfterDeletion();
    }

    /**
     * Cleans up temporary and locked file data after a deletion operation by unsetting relevant properties.
     *
     * @return void
     */
    private function cleanupAfterDeletion()
    {
        unset($this->tempSelectedItems);
        unset($this->objLockedFiles);
        unset($this->tempItems);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // MOVE

    /**
     * Handles the click event on the "Move" button. This method performs the necessary operations
     * to process file and folder relocation, including validation, data preparation, and UI updates.
     *
     * @param ActionParams $params The parameters associated with the button click event, typically holding context regarding the action performed.
     * @return void This method does not return any value but performs multiple internal operations to handle the move process.
     */
    public function btnMove_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        $objFolders = Folders::loadAll();
        $objFiles = Files::loadAll();

        // Check for conditions preventing relocation
        if (!$this->validateMoveConditions()) {
            return;
        }

        // Prepare and send data to function fullMove($src, $dst)
        $this->prepareMoveData();

        // Data validation and processing
        $this->processMoveData($objFolders, $objFiles);

        // UI-related operations
        $this->updateMoveDestinationDialog();

        // Show the move dialog
        $this->showMoveDialog();
    }

    // Helper functions

    /**
     * Validates the conditions required for moving files or folders.
     *
     * The function checks several preconditions to ensure the move operation is feasible and safe.
     * It evaluates system state, user selection, and restrictions tied to the move process.
     * Appropriate dialog boxes are displayed when validation fails, with specific error messages.
     *
     * @return bool Returns true if all move conditions are valid; otherwise, false.
     */
    private function validateMoveConditions()
    {
        clearstatcache();

        if ($this->dataScan() !== $this->scan($this->objManager->RootPath)) {
            $this->dlgModal1->showDialogBox(); // Corrupted table "folders" in the database or directory "upload" in the file system! ...
            return false;
        }

        if (!$this->arrSomeArray) {
            $this->dlgModal20->showDialogBox(); // Please choose specific folder(s) or file(s)!
            return false;
        }

        if ($this->arrSomeArray[0]["data-id"] == 1 && $this->arrSomeArray[0]["data-path"] == "") {
            $this->dlgModal30->showDialogBox(); // It's not possible to move to the root directory!
            return false;
        }

        if ($this->arrSomeArray[0]["data-activities-locked"] == 1) {
            $this->dlgModal31->showDialogBox(); // Sorry, but this reserved folder or file cannot be moved!
            return false;
        }

        return true;
    }

    /**
     * Prepares data for the move operation by processing an internal array and extracting specific data paths.
     *
     * @return void
     */
    private function prepareMoveData()
    {
        // Preparing and sending data to the function fullMove($src, $dst)

        $tempArr = [];

        foreach ($this->arrSomeArray as $arrSome) {
            $tempArr[] = $arrSome;
        }
        foreach ($tempArr as $temp) {
            if ($temp['data-path']) {
                $this->tempSelectedItems[] = $temp['data-path'];
            }
        }
    }

    /**
     * Processes the data necessary for moving directories and files.
     *
     * @param mixed $objFolders The folders data to process for moving.
     * @param mixed $objFiles The files data to process for moving.
     *
     * @return void
     */
    private function processMoveData($objFolders, $objFiles)
    {
        // Processing logic for moving data

        $this->moveDirectory($objFolders, $objFiles);
        $this->moveFile($objFiles);
    }

    /**
     * Moves a directory along with its contained files and folders to a new location.
     *
     * @param array $objFolders An array of folder objects to be moved.
     * @param array $objFiles An array of file objects to be moved.
     * @return void
     */
    private function moveDirectory($objFolders, $objFiles)
    {
        // Perform directory moving logic

        $dataFolders = [];
        $dataFiles = [];
        $tempIds = [];

        foreach ($this->arrSomeArray as $arrSome) {
            if ($arrSome["data-item-type"] == "dir") {
                $dataFolders[] = $arrSome["data-id"];
            }
        }

        foreach ($dataFolders as $dataFolder) {
            $tempIds = array_merge($tempIds, $this->fullScanIds($dataFolder));
        }

        foreach ($objFiles as $objFile) {
            foreach ($tempIds as $tempId) {
                if ($objFile->getFolderId() == $tempId) {
                    $dataFiles[] = $objFile->getId();
                }
            }
        }

        // Here have to check whether the files are locked
        foreach ($objFiles as $objFile) {
            foreach ($dataFiles as $dataFile) {
                if ($objFile->getId() == $dataFile) {
                    if ($objFile->getLockedFile() == 1) {
                        $this->objLockedFiles++;
                    }
                }
            }
        }

        foreach ($objFolders as $objFolder) {
            foreach ($tempIds as $tempId) {
                if ($objFolder->getId() == $tempId) {
                    $this->tempItems[] = $objFolder->getPath();
                }
            }
            sort($this->tempItems);
        }
        foreach ($objFiles as $objFile) {
            foreach ($dataFiles as $dataFile) {
                if ($objFile->getId() == $dataFile) {
                    $this->tempItems[] = $objFile->getPath();
                }
            }
            sort($this->tempItems);
        }
    }

    /**
     * Moves specified files by performing necessary logic and updates internal properties.
     *
     * @param array $objFiles An array of file objects to be moved. Each file object must implement methods like getId, getPath, and getLockedFile.
     * @return void
     */
    private function moveFile($objFiles)
    {
        // Perform file moving logic

        $dataFiles = [];

        foreach ($this->arrSomeArray as $arrSome) {
            if ($arrSome["data-item-type"] == "file") {
                $dataFiles[] = $arrSome["data-id"];
            }
        }

        foreach ($objFiles as $objFile) {
            foreach ($dataFiles as $dataFile) {
                if ($objFile->getId() == $dataFile) {
                    if ($objFile->getId() == $dataFile) {
                        $this->tempItems[] = $objFile->getPath();
                    }

                    // Here have to check whether the files are locked
                    if ($objFile->getLockedFile() == 1) {
                        $this->objLockedFiles++;
                    }
                }
            }
        }
    }

    /**
     * Updates the move destination dialog by analyzing directories and paths,
     * managing locks, and marking items for the UI based on specific conditions.
     *
     * @return void
     */
    private function updateMoveDestinationDialog()
    {
        // Update destination dialog UI

        $objPaths = $this->scanForSelect();

        foreach ($this->tempItems as $tempItem) {
            if (is_dir($this->objManager->RootPath . $tempItem)) {
                $this->objLockedDirs[] = $tempItem;
            }
        }

        if ($objPaths) foreach ($objPaths as $objPath) {
            if ($objPath['activities_locked'] == 1) {
                array_push($this->objLockedDirs, $objPath["path"]);
            }
        }

        if ($objPaths) foreach ($objPaths as $objPath) {
            if (in_array($objPath["path"], $this->objLockedDirs)) {
                $mark = true;
            } else {
                $mark = false;
            }
            $this->dlgMovingDestination->AddItem($this->printDepth($objPath['name'], $objPath['parent_id'], $objPath['depth']), $objPath, null, $mark);
        }
    }

    /**
     * Displays the move dialog where users can manage and confirm their moving operations.
     * This method populates the dialog with folder and file names, checks if any files
     * are locked, and enforces rules around move permissions and destination selection.
     *
     * @return void
     */
    private function showMoveDialog()
    {
        // Show the move dialog

        // Show folder and file names before moving
        if (count($this->tempItems) !== 0) {
            $source = implode(', ', $this->tempItems);
            $this->lblMovingCourcePath->Text = $source;
        }

        // Here have to check if some files have already been locked before.
        //If so, cancel and select unlocked files again...
        if ($this->objLockedFiles !== 0) {
            Application::executeJavaScript(sprintf("
                $('.modal-header').removeClass('btn-default').addClass('btn-danger');
                $('.move-error-text').removeClass('hidden');
                $('.move-info-text').removeClass('hidden');
                $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        } else {
            Application::executeJavaScript(sprintf("
                $('.modal-header').removeClass('btn-danger').addClass('btn-default');
                $('.move-error-text').addClass('hidden');
                $('.move-info-text').addClass('hidden');
                $('.modal-footer .btn-orange').removeAttr('disabled', 'disabled');
            "));
        }

        if ($this->dlgMovingDestination->SelectedValue == null) {
            Application::executeJavaScript(sprintf("
                $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        } else {
            Application::executeJavaScript(sprintf("
                $('.modal-footer .btn-orange').removeAttr('disabled', 'disabled');
            "));
        }

        $this->dlgModal32->showDialogBox(); // Move files or folders
    }

    /**
     * Handles the click event for starting the moving process of files or folders.
     *
     * @param ActionParams $params Parameters associated with the action event, such as user interaction data.
     * @return void
     */
    public function startMovingProcess_Click(ActionParams $params)
    {
        $objPath = $this->dlgMovingDestination->SelectedValue;

        if (!$objPath) {
            $this->handleMovingError();
            return;
        }

        $this->dlgModal32->hideDialogBox(); // Move files or folders

        if ($this->dlgMovingDestination->SelectedValue !== null) {
            foreach ($this->tempSelectedItems as $selectedItem) {
                $this->fullMoveItem($selectedItem, $objPath);
            }
        }

        $this->handleMovingResult();
    }

    // Helper functions

    /**
     * Moves the selected item from its source path to the specified destination path.
     *
     * @param string $selectedItem The name or path of the item to be moved.
     * @param array $objPath An associative array containing the destination path details.
     *                        Example: ['path' => 'target_directory']
     * @return void
     */
    private function fullMoveItem($selectedItem, $objPath)
    {
        $sourcePath = $this->objManager->RootPath . $selectedItem;
        $destinationPath = $this->objManager->RootPath . $objPath['path'] . "/" . basename($selectedItem);



        // Perform the move logic
        $this->fullMove($sourcePath, $destinationPath);
    }

    /**
     * Handles errors related to the moving destination selection.
     * Ensures that a default value is added to the moving destination dropdown
     * if no value is selected, and updates the interface to indicate the error state.
     *
     * @return void
     */
    private function handleMovingError()
    {
        if ($this->dlgMovingDestination->SelectedValue == null) {
            $this->dlgMovingDestination->removeAllItems();
            $this->dlgMovingDestination->AddItem(t('- Select One -'), null);

            Application::executeJavaScript(sprintf("
               $('.modal-header').removeClass('btn-default').addClass('btn-danger');
               $('.destination-moving-error').removeClass('hidden');
               $('.moving-source-title').addClass('hidden');
               $('.moving-source-path').addClass('hidden');
               $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        }
    }

    /**
     * Handles the result of the moving process.
     *
     * This method determines whether the moving operation was successful based on
     * the comparison of stored checks and the total number of temporary items. It
     * displays the appropriate dialog box indicating success or failure and then
     * performs cleanup operations after the moving process.
     *
     * @return void
     */
    private function handleMovingResult()
    {
        if ($this->intStoredChecks >= count($this->tempItems)) {
            $this->dlgModal33->showDialogBox(); // The selected files and folders have been successfully moved!
        } else {
            $this->dlgModal34->showDialogBox(); // Error moving items!
        }

        // Clean up after the moving process
        $this->cleanupAfterMoving();
    }

    /**
     * Cleans up temporary data and resets the state after items have been moved.
     *
     * This method removes temporary selected items, locked files, temporary items,
     * and locked directories. It also refreshes the manager to ensure a consistent state.
     *
     * @return void
     */
    private function cleanupAfterMoving()
    {
        unset($this->tempSelectedItems);
        unset($this->objLockedFiles);
        unset($this->tempItems);
        unset($this->objLockedDirs);
        $this->objManager->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    // DOWNLOAD

    /**
     * Handles the click event for the download button, performing file download or archive creation/download.
     *
     * Checks the download conditions, determines the type of data to process, and either initiates a direct
     * download or creates a ZIP archive for the selected items using the `ZipArchive` class, if available.
     * Displays appropriate dialog boxes in case of errors or missing dependencies.
     *
     * @param ActionParams $params Parameters associated with the button click event.
     * @return void Executes JavaScript for file download, displays dialogs, or performs archive creation.
     */
    public function btnDownload_Click(ActionParams $params)
    {
        if (!Application::verifyCsrfToken()) {
            $this->dlgModal46->showDialogBox();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return;
        }

        // Check for conditions preventing download
        $this->validateDownloadConditions();

        $this->strDataType = $this->arrSomeArray[0]["data-item-type"];
        $this->strDataPath = $this->arrSomeArray[0]["data-path"];
        $url = QCUBED_FILEMANAGER_ASSETS_URL;

        if (count($this->arrSomeArray) == 1 && $this->strDataType == "file") {
            Application::executeJavaScript(sprintf("
                document.location.href = '{$url}' + '/php/download.php?download=' + '{$this->strDataPath}'
            "));
            return;
        }

        // Code for creating the archive...
        $tempArr = [];
        foreach ($this->arrSomeArray as $arrSome) {
            $tempArr[] = $arrSome;
        }

        foreach ($tempArr as $temp) {
            if ($temp['data-path']) {
                $fullPath = $this->objManager->RootPath . $temp['data-path'];
                $this->tempSelectedItems[] = $fullPath;
            }
        }

        if (class_exists('ZipArchive')) {
            $zipName = $this->objManager->TempPath . '/_files/zip/' . 'archive_' . date('Y-m-d-h-i-s') . '.zip';
            $zipLocation = $this->objManager->TempPath . '/_files/zip/';

            $temps = [];
            $files = [];

            foreach($this->tempSelectedItems as $file) {
                $temps[] = $file;
            }

            foreach ($temps as $temp) {
                if (is_dir($temp)) {
                    foreach ( array_diff(scandir($temp), array('..', '.')) as $file) {
                       $files[] = $file;
                    }

                    if (!empty($files)) {
                        $this->zipCreate($zipName, $this->tempSelectedItems);
                    } else {
                        $this->dlgModal39->showDialogBox(); // The empty/sequential folder(s) do not contain files! ...
                    }
                } else {
                    $this->zipCreate($zipName, $this->tempSelectedItems);
                }
            }

            foreach (glob($zipLocation . "*.zip") as $file) {
                if ($file) {
                    $this->zipDownload($file);
                }
            }
        } else {
            $this->dlgModal38->showDialogBox(); // ZipArchive is not available!
        }

        unset($this->tempSelectedItems);
    }

    /**
     * Validates the conditions required for initiating a download process.
     *
     * Ensures the integrity of the directory and database, checks for the presence
     * of necessary classes, validates user selections, and enforces restrictions
     * on downloading specific directories.
     *
     * @return void
     */
    private function validateDownloadConditions()
    {
        clearstatcache();

        if ($this->dataScan() !== $this->scan($this->objManager->RootPath)) {
            $this->dlgModal1->showDialogBox(); // Corrupted table "folders" in the database or directory "upload" in the file system! ...
            return;
        }

        if (!class_exists('ZipArchive')) {
            $this->dlgModal35->showDialogBox(); // Operations with archives are not available!
            return;
        }

        if (!$this->arrSomeArray) {
            $this->dlgModal20->showDialogBox(); // Please choose specific folder(s) or file(s)!
            return;
        }

        if ($this->arrSomeArray[0]["data-id"] == 1 && $this->arrSomeArray[0]["data-path"] == "") {
            $this->dlgModal36->showDialogBox(); // It's not possible to download the root directory!
            return;
        }
    }

    /**
     * Creates a zip archive with the specified name and files.
     *
     * @param string $zipName The name of the zip archive to be created.
     * @param array $files An array of file paths to be included in the zip archive.
     * @return void This method does not return a value but shows a dialog box if the zip creation fails.
     */
    private function zipCreate($zipName, $files)
    {
        $zip = new Archive();
        $res = $zip->create($zipName, $files);

        if (!$res) {
            $this->dlgModal37->showDialogBox(); // Creating an archive for download failed!
            return;
        }
    }

    /**
     * Initiates the download of a zip file by generating a JavaScript redirect to the specified file URL.
     *
     * @param string $file The full path of the file to be downloaded.
     * @return void
     */

    private function zipDownload($file)
    {
        $url = QCUBED_FILEMANAGER_ASSETS_URL;
        $basename = basename($file);

        Application::executeJavaScript(sprintf("
            if ('{$basename}') {
                document.location.href = '{$url}' + '/php/zip-download.php?download=' + '{$basename}'
            }
        "));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the data clearing operation for the user interface elements, variables, and session storage.
     *
     * @return void
     */
    public function dataClearing_Click()
    {
        // Clearing form elements
        $this->txtAddFolder->Text = '';
        $this->dlgCopyingDestination->SelectedValue = '';
        $this->dlgMovingDestination->SelectedValue = '';
        $this->clearDropdownOptions($this->dlgCopyingDestination);
        $this->clearDropdownOptions($this->dlgMovingDestination);

        // Unset variables
        $this->clearVariables();

        // Clearing session storage
        Application::executeJavaScript(sprintf("sessionStorage.clear();"));
    }

    // Helper functions

    /**
     * Clears all existing options from a dropdown and adds a default placeholder option.
     *
     * @param object $dropdown The dropdown component from which all options will be removed and the placeholder option will be added.
     * @return void
     */
    private function clearDropdownOptions($dropdown)
    {
        $dropdown->removeAllItems();
        $dropdown->AddItem(t('- Select One -'), null);
    }

    /**
     * Clears specific variables by unsetting their values.
     *
     * This method is used to reset the state of various class properties
     * related to temporary selections, data identifiers, and locked items.
     *
     * @return void
     */
    private function clearVariables()
    {
        unset($this->tempSelectedItems);
        unset($this->objLockedFiles);
        unset($this->tempItems);
        unset($this->intDataId);
        unset($this->strDataName);
        unset($this->strDataPath);
        unset($this->intDataLocked);
        unset($this->objLockedDirs);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Scans and processes data from folders, extracting their paths, removing the first element,
     * and sorting the remaining paths alphabetically.
     *
     * @return array The sorted list of folder paths, excluding the first element.
     */

    protected function dataScan()
    {
        $folders = Folders::loadAll();

        // Use array_map to extract paths.
        $arr = array_map(function ($folder) {
            return $folder->getPath();
        }, $folders);

        // Remove the first element from the array
        array_shift($arr);
        // Sort the paths.
        sort($arr);

        return $arr;
    }

    /**
     * Recursively scans a directory and retrieves a sorted list of folder paths relative to a predefined base.
     *
     * @param string $path The directory path to scan.
     * @return array An array of relative folder paths sorted alphabetically.
     */
    protected function scan($path)
    {
        $folders = [];

        if (file_exists($path)) {
            foreach (scandir($path) as $f) {
                if ($f[0] == '.') {
                    continue;
                }

                $fullPath = $path . DIRECTORY_SEPARATOR . $f;

                if (is_dir($fullPath)) {
                    $folders[] = $this->objManager->getRelativePath($fullPath);
                    array_push($folders, ...$this->scan($fullPath));
                }
            }
        }

        sort($folders);

        return $folders;
    }

    /**
     * Recursively retrieves all descendant folder IDs, including the given parent ID.
     *
     * @param int $parentId The ID of the parent folder to begin scanning for descendants.
     * @return array An array of all descendant folder IDs, including the provided parent ID.
     */
    protected function fullScanIds($parentId)
    {
        $objFolders = Folders::loadAll();
        $descendantIds = [];

        foreach ($objFolders as $objFolder) {
            if ($objFolder->ParentId == $parentId) {
                array_push($descendantIds, ...$this->fullScanIds($objFolder->Id));
            }
        }

        array_push($descendantIds, $parentId);

        return $descendantIds;
    }

    /**
     * Scans and retrieves a sorted list of folders with their associated details.
     * The method loads all folders, gathers data for each folder including id, parent id,
     * name, path, depth, and activities locked status, and then sorts the folders by their paths.
     *
     * @return array Returns a sorted array of folder data. Each folder's data includes:
     *               - id: The unique identifier of the folder.
     *               - parent_id: The identifier of the parent folder.
     *               - name: The name of the folder.
     *               - path: The complete path of the folder.
     *               - depth: The depth of the folder in the hierarchy based on the path.
     *               - activities_locked: The status indicating whether activities are locked for the folder.
     */
    protected function scanForSelect()
    {
        $folders = Folders::loadAll();
        $folderData = [];
        $sortedNames = [];

        foreach ($folders as $folder) {
            $folderData[] = [
                'id' => $folder->getId(),
                'parent_id' => $folder->getParentId(),
                'name' => $folder->getName(),
                'path' => $folder->getPath(),
                'depth' => substr_count($folder->getPath(), '/'),
                'activities_locked' => $folder->getActivitiesLocked(),
            ];
        }

        foreach ($folderData as $key => $val) {
            $sortedNames[$key] = strtolower($val['path']);
        }

        array_multisort($sortedNames, SORT_ASC, $folderData);

        return $folderData;
    }

    /**
     * Validates a given string by checking if it contains at most one segment after splitting by slashes.
     *
     * @param string $str The input string to be checked.
     * @return bool Returns true if the string has at most one segment or the second segment is empty, otherwise false.
     */
    protected function checkString($str) {
        // Remove leading and trailing spaces
        $str = trim($str);

        // Split the string based on the slashes
        $parts = explode('/', $str);

        // We check if there are more parts after the first element
        return count($parts) <= 2 && empty($parts[1]);
    }

    /**
     * Prints a formatted string based on the depth and parent information provided.
     *
     * @param string $name The name to be displayed in the formatted output.
     * @param mixed $parent The parent information; can be null to indicate no parent.
     * @param int $depth The depth of the element, used to determine the level of indentation.
     * @return string The formatted string with the appropriate depth-based indentation.
     */
    protected function printDepth($name, $parent, $depth)
    {
        $spacer = str_repeat('&nbsp;', 5); // Adjust the number as needed for your indentation.

        if ($parent !== null) {
            $strHtml = str_repeat(html_entity_decode($spacer), $depth) . ' ' . t($name);
        } else {
            $strHtml = t($name);
        }

        return $strHtml;
    }

    /**
     * Moves all contents from the source location to the destination location by copying first and then removing the original files.
     *
     * @param string $src The source directory or file path.
     * @param string $dst The destination directory or file path.
     * @return void
     */
    protected function fullMove($src, $dst)
    {
        $this->fullCopy($src, $dst);
        $this->fullRemove($src);
    }

    /**
     * Recursively copies a file or directory from source to destination,
     * while managing metadata and associated operations.
     *
     * @param string $src The source path to copy from. It can be a file or directory.
     * @param string $dst The destination path to copy to.
     * @return void
     */
    protected function fullCopy($src, $dst)
    {
        $objId = $this->getIdFromParent($dst);

        if ($objId) {
            $objFolder = Folders::loadById($objId);
            if ($objFolder->getLockedFile() !== 1) {
                $objFolder->setMtime(filemtime(dirname($dst)));
                $objFolder->setLockedFile(1);
                $objFolder->save();
            }
        }

        $dirname = $this->objManager->removeFileName($dst);
        $name = pathinfo($dst, PATHINFO_FILENAME);
        $ext = pathinfo($dst, PATHINFO_EXTENSION);

        if (is_dir($src)) {
            if (file_exists($dirname . '/' . basename($name))) {
                $inc = 1;
                while (file_exists($dirname . '/' . $name . '-' . $inc)) $inc++;
                $dst = $dirname . '/' . $name . '-' . $inc;
            }

            Folder::makeDirectory($dst, 0777);

            $objFolder = new Folders();
            $objFolder->setParentId($objId);
            $objFolder->setPath($this->objManager->getRelativePath(realpath($dst)));
            $objFolder->setName(basename($dst));
            $objFolder->setType("dir");
            $objFolder->setMtime(filemtime($dst));
            $objFolder->save();

            foreach ($this->tempFolders as $tempFolder) {
                Folder::makeDirectory($this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($dst),0777);
            }

            $files = array_diff(scandir($src), array('..', '.'));
            foreach($files as $file) {
                $this->fullCopy("$src" . "/" . "$file", "$dst" . "/". "$file");
            }

        } else if (file_exists($src)) {
            if (file_exists($dirname . '/' . basename($name) . '.' . $ext)) {
                $inc = 1;
                while (file_exists($dirname . '/' . $name . '-' . $inc . '.' . $ext)) $inc++;
                $dst = $dirname . '/' . $name . '-' . $inc . '.' . $ext;
            }

            copy($src,$dst);

            if (in_array(strtolower($ext), $this->arrAllowed)) {
                foreach ($this->tempFolders as $tempFolder) {
                    copy($this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($src),$this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($dst));
                }
            }

            $objFiles = new Files();
            $objFiles->setFolderId($objId);
            $objFiles->setName(basename($dst));
            $objFiles->setType("file");
            $objFiles->setPath($this->objManager->getRelativePath(realpath($dst)));
            $objFiles->setExtension($this->objManager->getExtension($dst));
            $objFiles->setMimeType($this->objManager->getMimeType($dst));
            $objFiles->setSize(filesize($dst));
            $objFiles->setMtime(filemtime($dst));
            $objFiles->setDimensions($this->objManager->getDimensions($dst));
            $objFiles->save();
        }

        if (file_exists($dst)) {
            $this->intStoredChecks++;
        }

        $this->objManager->refresh();
        clearstatcache();
    }

    /**
     * Retrieves the ID of a folder based on its parent path.
     *
     * @param string $path The file path from which the parent folder's ID will be determined.
     * @return int|null The ID of the folder if a match is found, 1 if the path is empty, or null if no match is found.
     */
    protected function getIdFromParent($path)
    {
        $objFolders = Folders::loadAll();
        $objPath = $this->objManager->getRelativePath(realpath(dirname($path)));

        foreach ($objFolders as $objFolder) {
            if ($objPath == $objFolder->getPath()) {
                return $objFolder->getId();
            }
        }

        // Handle the case where no matching folder is found.
        return ($objPath == "") ? 1 : null;
    }

    /**
     * Recursively removes a directory or file, including associated database entries and temporary files.
     *
     * @param string $dir The path of the directory or file to be removed.
     *
     * @return void
     */
    protected function fullRemove($dir)
    {
        $objFolders = Folders::loadAll();
        $objFiles = Files::loadAll();

        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('..', '.'));

            foreach ($files as $file) {
                $this->fullRemove($dir . "/" . $file);
            }

            foreach ($objFolders as $objFolder) {
                if ($objFolder->getPath() == $this->objManager->getRelativePath($dir)) {
                    if ($objFolder->getId()) {
                        $obj = Folders::loadById($objFolder->getId());
                        $obj->delete();
                        $this->intStoredChecks++;
                    }
                }
            }

            if (file_exists($dir)) {
                rmdir($dir);

                foreach ($this->tempFolders as $tempFolder) {
                    $tempPath = $this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($dir);
                    if (is_dir($tempPath)) {
                        rmdir($tempPath);
                    }
                }
            }
        } elseif (file_exists($dir)) {
            foreach ($objFiles as $objFile) {
                if ($objFile->getPath() == $this->objManager->getRelativePath($dir)) {
                    if ($objFile->getId()) {
                        $obj = Files::loadById($objFile->getId());
                        $obj->delete();
                        $this->intStoredChecks++;
                    }
                }
            }

            unlink($dir);

            foreach ($this->tempFolders as $tempFolder) {
                $tempPath = $this->objManager->TempPath . '/_files/' . $tempFolder . $this->objManager->getRelativePath($dir);
                if (is_file($tempPath)) {
                    unlink($tempPath);
                }
            }
        }

        $dirname = dirname($dir);
        if (is_dir($dirname)) {
            $folders = glob($dirname . '/*', GLOB_ONLYDIR);
            $files = array_filter(glob($dirname . '/*'), 'is_file');

            foreach ($objFolders as $objFolder) {
                if ($objFolder->getPath() == $this->objManager->getRelativePath($dirname)) {
                    if (count($folders) == 0 && count($files) == 0) {
                        $obj = Folders::loadById($objFolder->getId());
                        if ($obj->getLockedFile() == 1) {
                            $obj->setMtime(filemtime($dirname));
                            $obj->setLockedFile(0);
                            $obj->save();
                        }
                    }
                }
            }
        }

        $this->objManager->refresh();
    }

    /**
     * Handles the change event for the destination dialog. Updates the UI components and
     * JavaScript behaviors based on the selected values of the copying and moving destinations,
     * as well as the state of `objLockedFiles`.
     *
     * @param ActionParams $params The parameters passed during the change event triggering, containing any relevant data.
     * @return void This method does not return any value.
     */
    public function dlgDestination_Change(ActionParams $params)
    {
        if (is_array($this->dlgCopyingDestination->SelectedValue) || is_array($this->dlgMovingDestination->SelectedValue)) {
            Application::executeJavaScript(sprintf("
                $('.modal-header').removeClass('btn-danger').addClass('btn-default');
                $('.destination-error').addClass('hidden');
                $('.destination-moving-error').addClass('hidden');
                $('.source-title').removeClass('hidden');
                $('.moving-source-title').removeClass('hidden');
                $('.source-path').removeClass('hidden');
                $('.moving-source-path').removeClass('hidden');
                $('.modal-footer .btn-orange').removeAttr('disabled', 'disabled');
            "));
        } else {
            Application::executeJavaScript(sprintf("
                $('.modal-header').removeClass('btn-default').addClass('btn-danger');
                $('.destination-error').removeClass('hidden');
                $('.destination-moving-error').removeClass('hidden');
                $('.source-title').addClass('hidden');
                $('.moving-source-title').addClass('hidden');
                $('.source-path').addClass('hidden');
                $('.moving-source-path').addClass('hidden');
                $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        }

        if ($this->objLockedFiles !== 0) {

            Application::executeJavaScript(sprintf("
               $('.modal-header').removeClass('btn-default').addClass('btn-danger');
               //$('.destination-moving-error').removeClass('hidden');
               $('.moving-source-title').addClass('hidden');
               $('.moving-source-path').addClass('hidden');
               $('.modal-footer .btn-orange').attr('disabled', 'disabled');
            "));
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles the click event for the image list view button. Updates the UI and object manager
     * state to reflect the image list view selection.
     *
     * @param ActionParams $params The parameters associated with the action event triggered by the button click.
     * @return void This method does not return a value.
     */
    public function btnImageListView_Click(ActionParams $params)
    {
        $this->btnImageListView->addCssClass("active");
        $this->btnListView->removeCssClassesByPrefix("active");
        $this->btnBoxView->removeCssClassesByPrefix("active");

        $this->objManager->IsImageListView = true;
        $this->objManager->IsListView = false;
        $this->objManager->IsBoxView = false;
        $this->objManager->refresh();
    }

    /**
     * Handles the click event for the List View button. Activates the List View mode by
     * adding the active CSS class to the button and updating the object manager
     * to reflect the current view mode. Also adjusts the active states of other
     * view-related buttons.
     *
     * @param ActionParams $params Parameters related to the action event, such as
     *                             details about the user interaction triggering the event.
     * @return void This method does not return any value.
     */
    public function btnListView_Click(ActionParams $params)
    {
        $this->btnListView->addCssClass("active");
        $this->btnImageListView->removeCssClassesByPrefix("active");
        $this->btnBoxView->removeCssClassesByPrefix("active");

        $this->objManager->IsListView = true;
        $this->objManager->IsImageListView = false;
        $this->objManager->IsBoxView = false;
        $this->objManager->refresh();
    }

    /**
     * Handles the click event for the btnBoxView button, activating the Box View layout.
     *
     * @param ActionParams $params The parameters associated with the action triggered by the button click.
     * @return void
     */
    public function btnBoxView_Click(ActionParams $params)
    {
        $this->btnBoxView->addCssClass("active");
        $this->btnImageListView->removeCssClassesByPrefix("active");
        $this->btnListView->removeCssClassesByPrefix("active");

        $this->objManager->IsBoxView = true;
        $this->objManager->IsImageListView = false;
        $this->objManager->IsListView = false;
        $this->objManager->refresh();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

}

SampleForm::run('SampleForm');
