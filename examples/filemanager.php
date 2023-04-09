<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Js;
use QCubed\Html;
use QCubed\Query\QQ;

use QCubed\Event\CellClick;

require_once('qcubed.inc.php');
require_once('../src/FileManager.php');

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
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

    protected $strMenuManagerId;

    protected function formCreate()
    {
        parent::formCreate();

        $this->objManager = new Q\Plugin\FileManager($this);

        $this->CreateButtons();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function CreateButtons()
    {
        $this->btnUpload = new Q\Plugin\Control\Button($this);
        $this->btnUpload->Text = t(' Upload');
        $this->btnUpload->Glyph = 'fa fa-upload';
        $this->btnUpload->CssClass = 'btn btn-orange fileinput-button';
        $this->btnUpload->UseWrapper = false;

        //$this->btnUpload->Display = false;

        $this->btnUpload->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnUpload_Click'));

        $this->btnAddFolder = new Q\Plugin\Control\Button($this);
        $this->btnAddFolder->Text = t(' Add folder');
        $this->btnAddFolder->Glyph = 'fa fa-folder';
        $this->btnAddFolder->CssClass = 'btn btn-orange';
        $this->btnAddFolder->CausesValidation = false;
        $this->btnAddFolder->UseWrapper = false;
        //$this->btnAddFolder->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnAddFolder_Click'));

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

    ///////////////////////////////////////////////////////////////////////////////////////////

//    protected function Manager_Bind()
//    {
//        //$this->objManager->DataSource = Folders::loadAll();
//
//        print '<pre>';
//        print_r($this->objManager->DataSource);
//        print '</pre>';
//        $this->objManager->refresh();
//
//        $intTime = filemtime($this->objManager->RootPath);
//        $intModified = date($this->objManager->DateTimeFormat, $intTime);
//
//        if (count($this->objManager->DataSource) == 0) {
//            $objFolder = new Folders();
//            $objFolder->setBasename('Library');
//            $objFolder->setFilePerms(substr(sprintf('%o', fileperms($this->objManager->RootPath)), -4));
//            $objFolder->setType('folder');
//            $objFolder->setSize('Folder');
//            $objFolder->setMtime($intTime);
//            $objFolder->setLastModified(date($intModified, $intTime));
//            $objFolder->setPath($this->objManager->getRootRelative($this->objManager->RootPath));
//            $objFolder->save(true);
//        }


        //print_r($this->objManager->DataSource);
//    }

//    public function Folders_Draw(Folders $objFolder)
//    {
//        $a['id'] = $objFolder->Id;
//        $a['basename'] = Q\QString::htmlEntities($objFolder->Basename);
//        $a['fileperms'] = $objFolder->FilePerms;
//        $a['type'] = $objFolder->Type;
//        $a['size'] = $objFolder->Size;
//        $a['mtime'] = $objFolder->Mtime;
//        $a['last_modified'] = $objFolder->LastModified;
//        $a['path'] = $objFolder->Path;
//        $a['full_size'] = $objFolder->FullSize;
//        $a['num_files'] = $objFolder->NumFiles;
//        $a['num_folders'] = $objFolder->NumFolders;
//        return $a;
//    }

//    public function Files_Draw(Files $objFile)
//    {
//        $a['id'] = $objFile->Id;
//        $a['folder_id'] = $objFile->FolderId;
//        $a['basename'] = Q\QString::htmlEntities($objFile->Basename);
//        $a['fileperms'] = $objFile->FilePerms;
//        $a['type'] = $objFile->Type;
//        $a['size'] = $objFile->Size;
//        $a['mtime'] = $objFile->Mtime;
//        $a['last_modified'] = $objFile->LastModified;
//        $a['ext'] = $objFile->Ext;
//        $a['mime_type'] = $objFile->MimeType;
//        $a['dimensions'] = $objFile->Dimensions;
//        $a['path'] = $objFile->Path;
//        $a['public_url'] = $objFile->PublicUrl;
//        $a['resized_image_url'] = $objFile->ResizedImageUrl;
//        $a['locked_file'] = $objFile->LockedFile;
//        return $a;
//    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function objRow_Click(ActionParams $params)
    {
        /*foreach ($this->objManager->SelectedItems as $key => $selectedItem) {

            $strItemType = $selectedItem['data-type'];
            $strPath = $selectedItem['data-path'];
            $objTitle = $selectedItem['data-title'];
            $intFileSize = $selectedItem['data-size'];
            $strMimeType = $selectedItem['data-mime-type'];
            $strDimensions = $selectedItem['data-dimensions'];
            $intModified = $selectedItem['data-last-modified'];
            $strDocumentType = $selectedItem['data-document-type'];
            $strPublicUrl =  $selectedItem['data-public-url'];

            $strItems[] = [
                "data-type" => $strItemType,
                "data-path" => $strPath,
                "data-title" => $objTitle,
                "data-size" => $intFileSize,
                "data-mime-type" => $strMimeType,
                "data-dimensions" => $strDimensions,
                "data-last-modified" => $intModified,
                "data-document-type" => $strDocumentType,
                "data-public-url" => $strPublicUrl
            ];
        }*/

//        $ary = json_decode($this->objManager->SelectedItems, true);
//        $a = count($ary);
//        //Application::displayAlert($a);
//
//        Application::displayAlert($this->objManager->SelectedItems);
//
//        $arr = json_decode($this->objManager->SelectedItems, true);

        //if ($arr[0]['data-type'] == "folder") {

            //$this->objManager->FullPath = $this->objManager->RootPath . $arr[0]['data-path'];
            //$this->objManager->FullPath = $arr[0]['data-path'];

            //$this->objManager->refresh();
       // }

        //$this->objManager->refresh();

        //Application::displayAlert($this->objManager->FullPath);


//        if ($arr[0]['data-type'] !== "folder") {
//            $this->btnMove->Enabled = true;
//            $this->btnRename->Enabled = true;
//            $this->btnCopy->Enabled = true;
//            $this->btnDownload->Enabled = true;
//            $this->btnDelete->Enabled = true;
//        } else {
//            $this->btnMove->Enabled = false;
//            $this->btnRename->Enabled = false;
//            $this->btnCopy->Enabled = false;
//            $this->btnDownload->Enabled = false;
//            $this->btnDelete->Enabled = false;
//        }




    }

    protected function btnUpload_Click(ActionParams $params)
    {


//        $arr = $this->objManager->SelectedItems;
//        $strItems = json_encode($arr);
//        Application::displayAlert($strItems);

        /*if ($this->chkCheckBox->Checked) {
            $this->btnMove->Enabled = true;
            $this->btnRename->Enabled = true;
            $this->btnCopy->Enabled = true;
            $this->btnDownload->Enabled = true;
            $this->btnDelete->Enabled = true;
        } else {
            $this->btnMove->Enabled = false;
            $this->btnRename->Enabled = false;
            $this->btnCopy->Enabled = false;
            $this->btnDownload->Enabled = false;
            $this->btnDelete->Enabled = false;
        }*/

    }




}

SampleForm::run('SampleForm');


