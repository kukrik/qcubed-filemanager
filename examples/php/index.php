<?php

require_once('../qcubed.inc.php');

use QCubed\Plugin\FileHandler;
use QCubed\Project\Application;

$options = array(
    //'ImageResizeQuality' => 75, // Defult 85
    //'ImageResizeFunction' => 'imagecopyresized', // Default imagecopyresampled
    //'ImageResizeSharpen' => false, // Default true
    //'TempFolders' =>  ['thumbnail', 'medium', 'large'], // Please read the UploadHandler description and manual
    //'ResizeDimensions' => [320, 480, 1500], // Please read the UploadHandler description and manual
    //'DestinationPath' => null, // Please read the UploadHandler description and manual
    //'AcceptFileTypes' => ['gif', 'jpg', 'jpeg', 'png', 'pdf', 'ppt', 'docx', 'xlsx', 'txt', 'mp4'], // Default null
    'DestinationPath' => !empty($_SESSION["filePath"]) ? $_SESSION["filePath"] : null, // Default null
    //'MaxFileSize' => 1024 * 1024 * 2 // 2 MB // Default null
    //'UploadExists' => 'overwrite', // increment || overwrite Default 'increment'
);



class CustomFileUploadHandler extends FileHandler
{
    protected function uploadInfo()
    {
        parent::uploadInfo();

        if ($this->options['FileError'] == 0) {
            $obj = new Files();
            $obj->setName(basename($this->options['FileName']));
            $obj->setType('file');
            $obj->setPath($this->getRelativePath($this->options['FileName']));
            $obj->setDescription(null);
            $obj->setExtension($this->getExtension($this->options['FileName']));
            $obj->setMimeType($this->getMimeType($this->options['FileName']));
            $obj->setSize($this->options['FileSize']);
            $obj->setMtime(filemtime($this->options['FileName']));
            $obj->setDimensions($this->getDimensions($this->options['FileName']));
            $obj->save(true);
        }

        $filesWithoutFolder = [];

        // Find files files without a folder ID
        foreach (Files::loadAll() as $file) {
            if ($file->FolderId === null) {
                $filesWithoutFolder[] = $file->Id;
            }
        }

        // Update folderId for files without a folder ID
        foreach ($filesWithoutFolder as $fileId) {
            $file = Files::loadById($fileId);
            $file->setFolderId($_SESSION['folderId']);
            $file->save();
        }
    }
}

$objHandler = new CustomFileUploadHandler($options);
















