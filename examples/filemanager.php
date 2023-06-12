<?php
require_once('qcubed.inc.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Folder;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Js;
use QCubed\Html;
use QCubed\Query\QQ;

use QCubed\Event\CellClick;

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
    protected $dlgModal1;
    protected $dlgModal2;
    protected $dlgModal3;
    protected $dlgModal4;

    protected $objUpload;
    protected $btnAddFiles;
    protected $btnAllStart;
    protected $btnAllCancel;
    protected $btnBack;

    protected $btnUpload;
    protected $btnFolder;
    protected $btnAddFolder;

    protected $btnMove;
    protected $btnRename;
    protected $btnCopy;
    protected $btnDownload;
    protected $btnDelete;

    protected $btnListView;
    protected $btnGridView;
    protected $btnBoxView;

    protected $btnRefresh;
    protected $chkCheckBox;
    protected $txtFilter;

    protected $objManager;
    protected $currentPath;

    protected $txtAddFolder;


    protected function formCreate()
    {
        parent::formCreate();

        $this->objUpload = new Q\Plugin\FileUploadHandler($this);
        $this->objUpload->Language = 'et'; // Default en
        //$this->objUpload->ShowIcons = true; // Default false
        $this->objUpload->AcceptFileTypes = ['gif', 'jpg', 'jpeg', 'png', 'pdf', 'docx', 'mp4']; // Default null
        //$this->objUpload->MaxNumberOfFiles = 5; // Default null
        $this->objUpload->MaxFileSize = 1024 * 1024 * 2; // 2 MB // Default null
        //$this->objUpload->MinFileSize = 500000; // 500 kb // Default null
        //$this->objUpload->ChunkUpload = false; // Default true
        $this->objUpload->MaxChunkSize = 1024 * 1024; // 10 MB // Default 5 MB
        //$this->objUpload->LimitConcurrentUploads = 5; // Default 2
        $this->objUpload->Url = 'php/'; // Default null
        //$this->objUpload->PreviewMaxWidth = 120; // Default 80
        //$this->objUpload->PreviewMaxHeight = 120; // Default 80
        //$this->objUpload->WithCredentials = true; // Default false


        $this->objManager = new Q\Plugin\FileManager($this);
        $this->objManager->setDataBinder('Data_Bind');
        $this->objManager->createRenderFolders([$this, 'Folder_Draw']);
        $this->objManager->Language = 'et';

        $this->CreateButtons();
        $this->createModals();
        $this->portedTextBoxes();

//        $directory = APP_UPLOADS_DIR . '/uudised 2023';
//        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
//        echo '<pre>';
//        print_r($scanned_directory);
//        echo '</pre>';

        //echo $this->objManager->RootPath . '<br>'; // APP_UPLOADS_DIR
//        echo basename(APP_UPLOADS_DIR);

    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function Data_Bind()
    {
        $this->objManager->DataSource = Folders::loadAll(
            QQ::expandAsArray(QQN::folders()->FilesAsFolder
            ));
    }

    public function Folder_Draw(Folders $objFolders)
    {
        $a['id'] = $objFolders->Id;
        $a['parent_id'] = $objFolders->ParentId;
        $a['name'] = Q\QString::htmlEntities($objFolders->Name);
        $a['type'] = $objFolders->Type;
        $a['path'] = $objFolders->Path;
        $a['created_date'] = $objFolders->CreatedDate;
        $a['mtime'] = $objFolders->Mtime;
        $a['locked_file'] = $objFolders->LockedFile;
        return $a;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function CreateButtons()
    {
        $this->btnAddFiles = new Q\Plugin\BsFileControl($this, 'files');
        $this->btnAddFiles->Text = t(' Add files');
        $this->btnAddFiles->Glyph = 'fa fa-upload';
        $this->btnAddFiles->Multiple = true;
        $this->btnAddFiles->CssClass = 'btn btn-orange fileinput-button';
        $this->btnAddFiles->UseWrapper = false;
        // $this->btnAddFiles->addAction(new Q\Event\Click(), new Q\Action\Ajax('fileUpload_Click'));

        $this->btnAllStart = new Bs\Button($this);
        $this->btnAllStart->Text = t('Start upload');
        $this->btnAllStart->CssClass = 'btn btn-darkblue all-start disabled';
        $this->btnAllStart->PrimaryButton = true;
        $this->btnAllStart->UseWrapper = false;
        // $this->btnAllStart->addAction(new Q\Event\Click(), new Q\Action\Ajax('fileSave_Click'));

        $this->btnAllCancel = new Bs\Button($this);
        $this->btnAllCancel->Text = t('Cancel all uploads');
        $this->btnAllCancel->CssClass = 'btn btn-warning all-cancel disabled';
        $this->btnAllCancel->UseWrapper = false;

        $this->btnBack = new Bs\Button($this);
        $this->btnBack->Text = t('Back to file manager');
        $this->btnBack->CssClass = 'btn btn-default back disabled';
        $this->btnBack->UseWrapper = false;

        $this->btnUpload = new Q\Plugin\BsFileControl($this);
        $this->btnUpload->Text = t(' Upload');
        $this->btnUpload->Glyph = 'fa fa-upload';
        $this->btnUpload->Multiple = true;
        $this->btnUpload->CssClass = 'btn btn-orange launch-start';
        $this->btnUpload->UseWrapper = false;

        $this->btnAddFolder = new Q\Plugin\Control\Button($this);
        $this->btnAddFolder->Text = t(' Add folder');
        $this->btnAddFolder->Glyph = 'fa fa-folder';
        $this->btnAddFolder->CssClass = 'btn btn-orange';
        $this->btnAddFolder->CausesValidation = false;
        $this->btnAddFolder->UseWrapper = false;
        $this->btnAddFolder->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnAddFolder_Click'));

        //$this->btnRefresh = new Q\Plugin\Control\Button($this);
        //$this->btnRefresh->Glyph = 'fa fa-refresh';
        //$this->btnRefresh->CssClass = 'btn btn-darkblue';
        //$this->btnRefresh->CausesValidation = false;
        //$this->btnRefresh->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnRefresh_Click'));

        $this->btnMove = new Q\Plugin\Control\Button($this);
        $this->btnMove->Text = t(' Move');
        $this->btnMove->Glyph = 'fa fa-reply-all';
        $this->btnMove->CssClass = 'btn btn-darkblue';
        $this->btnMove->CausesValidation = false;
        $this->btnMove->UseWrapper = false;
        $this->btnMove->Enabled = false;
        //$this->btnMove->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMove_Click'));

        $this->btnRename = new Q\Plugin\Control\Button($this);
        $this->btnRename->Text = t(' Rename');
        $this->btnRename->Glyph = 'fa fa-pencil-square-o';
        $this->btnRename->CssClass = 'btn btn-darkblue';
        $this->btnRename->CausesValidation = false;
        $this->btnRename->UseWrapper = false;
        $this->btnRename->Enabled = false;
        //$this->btnRename->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnRename_Click'));

        $this->btnCopy = new Q\Plugin\Control\Button($this);
        $this->btnCopy->Text = t(' Copy');
        $this->btnCopy->Glyph = 'fa fa-files-o';
        $this->btnCopy->CssClass = 'btn btn-darkblue';
        $this->btnCopy->CausesValidation = false;
        $this->btnCopy->UseWrapper = false;
        $this->btnCopy->Enabled = false;
        //$this->btnCopy->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnCopy_Click'));

        $this->btnDownload = new Q\Plugin\Control\Button($this);
        $this->btnDownload->Text = t(' Download');
        $this->btnDownload->Glyph = 'fa fa-download';
        $this->btnDownload->CssClass = 'btn btn-darkblue';
        $this->btnDownload->CausesValidation = false;
        $this->btnDownload->UseWrapper = false;
        $this->btnDownload->Enabled = false;
        //$this->btnDownload->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDownload_Click'));

        $this->btnDelete = new Q\Plugin\Control\Button($this);
        $this->btnDelete->Text = t(' Delete');
        $this->btnDelete->Glyph = 'fa fa-trash-o';
        $this->btnDelete->CssClass = 'btn btn-darkblue';
        $this->btnDelete->CausesValidation = false;
        $this->btnDelete->UseWrapper = false;
        $this->btnDelete->Enabled = false;
        //$this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));

        $this->btnListView = new Q\Plugin\Control\Button($this);
        $this->btnListView->Glyph = 'fa fa-align-justify';
        $this->btnListView->CssClass = 'btn btn-darkblue';
        $this->btnListView->addCssClass('active');
        $this->btnListView->CausesValidation = false;
        $this->btnListView->UseWrapper = false;
        //$this->btnListView->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnListView_Click'));

        $this->btnGridView = new Q\Plugin\Control\Button($this);
        $this->btnGridView->Glyph = 'fa fa-th';
        $this->btnGridView->CssClass = 'btn btn-darkblue';
        $this->btnGridView->CausesValidation = false;
        $this->btnGridView->UseWrapper = false;
        //$this->btnGridView->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnGridView_Click'));

        $this->btnBoxView = new Q\Plugin\Control\Button($this);
        $this->btnBoxView->Glyph = 'fa fa-th-large';
        $this->btnBoxView->CssClass = 'btn btn-darkblue';
        $this->btnBoxView->CausesValidation = false;
        $this->btnBoxView->UseWrapper = false;
        //$this->btnBoxView->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnBoxView_Click'));

        $this->txtFilter = new Bs\TextBox($this);
        $this->txtFilter->Placeholder = t('Search...');
        $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
        $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
        $this->txtFilter->addCssClass('search-trigger');
        //$this->addFilterActions();
    }

    public function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->AutoRenderChildren = true;
        $this->dlgModal1->Title = t('Name of new folder');
        $this->dlgModal1->Size = Bs\Bootstrap::MODAL_SMALL;
        $this->dlgModal1->HeaderClasses = 'btn-default';
        $this->dlgModal1->addButton(t("I accept"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addCloseButton(t("I'll cancel"));
        $this->dlgModal1->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('AddFolderName_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Title = t('Warning');
        $this->dlgModal2->Text = t('<p style="margin-top: 15px;">Cannot create a folder with the same name!</p>');
        $this->dlgModal2->Size = Bs\Bootstrap::MODAL_SMALL;
        $this->dlgModal2->HeaderClasses = 'btn-danger';
        $this->dlgModal2->addButton(t("Back"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal2->addCloseButton(t("I'll cancel"));
        $this->dlgModal2->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('AddFolderError_Click'));

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Title = t('Warning');
        $this->dlgModal3->Text = t('<p style="margin-top: 15px;">Folder cannot be created without name!</p>');
        $this->dlgModal3->Size = Bs\Bootstrap::MODAL_SMALL;
        $this->dlgModal3->HeaderClasses = 'btn-danger';
        $this->dlgModal3->addButton(t("Back"), null, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal3->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('AddFolderNameEmpty_Click'));

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Title = t('Info');
        $this->dlgModal4->Text = t('<p style="margin-top: 15px;">Failed to create folder! Try again!</p>');
        $this->dlgModal4->Size = Bs\Bootstrap::MODAL_SMALL;
        $this->dlgModal4->HeaderClasses = 'btn-darkblue';
//        $this->dlgModal4->addButton(t("Back"), null, false, false, null,
//            ['class' => 'btn btn-orange']);
        $this->dlgModal4->addCloseButton(t("I close the window"));
        //$this->dlgModal4->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('AddFolderNameEmpty_Click'));

    }

    public function portedTextBoxes()
    {
        $this->txtAddFolder = new Bs\TextBox($this->dlgModal1);
        $this->txtAddFolder->setHtmlAttribute('autocomplete', 'off');
        $this->txtAddFolder->setCssStyle('margin-top', '15px');
        $this->txtAddFolder->setCssStyle('margin-bottom', '15px');
        $this->txtAddFolder->UseWrapper = false;
    }

    public function btnAddFolder_Click(ActionParams $params)
    {
        $this->dlgModal1->showDialogBox();
        $this->txtAddFolder->Text = '';
    }

    public function AddFolderName_Click(ActionParams $params)
    {
        if (!empty(trim($this->txtAddFolder->Text))) {

            $scanned_directory = array_diff(scandir($this->objManager->RootPath), array('..', '.'));
            //DestinationPath'

            if ($this->objManager->RootPath . "/") {
                $path = $this->objManager->RootPath . "/" . trim($this->txtAddFolder->Text);
            } else {
                $path = $this->objManager->RootPath . "/" . trim($this->txtAddFolder->Text);
            }

            //$path = $this->objManager->RootPath . "/" . trim($this->txtAddFolder->Text);

            if (!in_array($this->txtAddFolder->Text, $scanned_directory)) {
                Folder::makeDirectory($path, 0777);
                $this->dlgModal1->hideDialogBox();

                if (file_exists($path) && is_dir($path)) {
                    $objAddFolder = new Folders();
                    //$objAddFolder->setParentId();
                    $objAddFolder->setPath($this->objManager->getRelativePath($path));
                    $objAddFolder->setName(trim($this->txtAddFolder->Text));
                    $objAddFolder->setType('dir');
                    $objAddFolder->setCreatedDate(Q\QDateTime::Now());
                    $objAddFolder->setMtime(filemtime($path));
                    $objAddFolder->setLockedFile(0);
                    $objAddFolder->save();

                    $this->objManager->refresh();

                    $strFullStoragePath = $this->objManager->TempPath . '/' . $this->objManager->StoragePath;
                    $strCreateDirs = ['/thumbnail', '/medium', '/large'];

                    foreach ($strCreateDirs as $strCreateDir) {
                        Folder::makeDirectory($strFullStoragePath . $strCreateDir . '/' . trim($this->txtAddFolder->Text), 0777);
                    }
                } else {
                    $this->dlgModal4->showDialogBox();
                }

            } else {
                $this->dlgModal1->hideDialogBox();
                $this->dlgModal2->showDialogBox();
            }
        } else {
            $this->dlgModal1->hideDialogBox();
            $this->dlgModal3->showDialogBox();
        }
    }

    public function AddFolderError_Click(ActionParams $params)
    {
        $this->dlgModal2->hideDialogBox();
        $this->dlgModal1->showDialogBox();
    }

    public function AddFolderNameEmpty_Click(ActionParams $params)
    {
        $this->dlgModal3->hideDialogBox();
        $this->dlgModal1->showDialogBox();
    }


    ///////////////////////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////////////////////////////

}

SampleForm::run('SampleForm');


