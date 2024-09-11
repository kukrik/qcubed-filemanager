<?php

namespace QCubed\Plugin;

/**
 * Class CroppieHandler
 *
 * Note: the "upload" folder must already exist in /project/assets/ and this folder has 777 permissions.
 *
 * @property string $RootPath Default root path APP_UPLOADS_DIR. You may change the location of the file repository
 *                             at your own risk.*
 * Note: If you want to change TempPath, TempUrl and StoragePath, you have to rewrite the setup() function in the FileUpload class.
 * This class is located in the /project/includes/plugins folder.
 * @property string $TempPath = Default temp path APP_UPLOADS_TEMP_DIR. If necessary, the temp dir must be specified.
 * @property string $StoragePath Default dir named _files. This dir is generated together with the dirs
 *                               /thumbnail,  /medium,  /large when the corresponding page is opened for the first time.
 * @property string $FullStoragePath Please see the setup() function! Can only be changed in this function.
 *
 *
 * @property integer $ThumbnailResizeDimensions Default resized image dimensions. Default 320 is a good balance between
 *                                              visible quality and file size.
 * @property integer $MediumResizeDimensions Default 480. Resize image dimensions for high-density (retina) screens.
 *                                           This allows you to serve higher quality images for HiDPI screens, at the
 *                                           cost of slightly larger file size. For example, generated for site preview.
 * @property integer $LargeResizeDimensions Default 1500. Resize image dimensions for high-density (retina) screens.
 *                                          This allows you to serve higher quality images for HiDPI (e.g 27- and 30-inch
 *                                          monitors) screens, at the cost of slightly larger file size.*
 *
 *
 * @property integer $ImageResizeQuality Default 85. JPG compression level for resized images.
 * @property string $ImageResizeFunction Default 'imagecopyresampled'. Choose between 'imagecopyresampled' (smoother)
 *                                       and 'imagecopyresized' (faster). Difference is minimal, but you could use
 *                                       imagecopyresized for example if you want faster resizing when not using image
 *                                       resize cache.
 * @property boolean $ImageResizeSharpen Default true. Creates sharper (less blurry) preview images.
 * @property array $TempFolders Default '['thumbnail', 'medium', 'large']'. If you want to change the names of
 *                              the temporary folders, you need to override the setup() function of the FileUpload class
 *                              ($strCreateDirs = ['/thumbnail', '/medium', '/large'];).
 *
 * @property array $ResizeDimensions Default '[320, 480, 1500]'. Note: Here you need to set the ResizeDimensions
 *                                   [320, 480, 1500] in the order of TempFolders ['thumbnail', 'medium', 'large'].
 *
 *                                   Default 320 is a good balance between visible quality and file size.
 *
 *                                   Default 480. Resize image dimensions for high-density (retina) screens. This allows
 *                                   you to serve higher quality images for HiDPI screens, at the cost of slightly
 *                                   larger file size. For example, generated for site preview.
 *
 *                                   Default 1500. Resize image dimensions for high-density (retina) screens. This allows
 *                                   you to serve higher quality images for HiDPI (e.g 27- and 30-inch monitors) screens,
 *                                   at the cost of slightly larger file size.
 *
 * @property array $AcceptFileTypes Default null. The output form of the array looks like this:
 *                                  '['gif', 'jpg', 'jpeg', 'png', 'pdf']'. If necessary, specify the allowed file types.
 *                                  When empty (default), all file types are allowed.
 * @property integer $MaxFileSize Default null. Sets the maximum file size (bytes) allowed for uploads. Default value null
 *                                means no limit, but maximum file size will always be limited by your server's
 *                                PHP upload_max_filesize value.
 * @property string $UploadExists Default 'increment'. Decides what to do if uploaded filename already exists in upload
 *                                target folder. Default 'increment' will rename uploaded files by appending a number,
 *                                'overwrite' will overwrite existing files.
 *                                Usage:
 *                                $this->UploadExists = 'increment'; // increment filename, for example filename.jpg => filename-2.jpg
 *                                $this->UploadExists = 'overwrite', // overwrite existing file if filename exists
 *
 * @property-read string $FileName is the name of the file that the user uploads
 * @property-read string $FileType is the MIME type of the file
 * @property-read integer $FileSize is the size in bytes of the file
 * @property string $DestinationPath Default null. This is a prepared option. If there is a need to create new subfolders
 *                                   and save images there. Then you need to make your own function to create new folders.
 *                                   For example:
 *                                   [folder1]
 *                                   |___ [folder2]
 *                                        |___ [folder3]
 *                                   Then write $this->DestinationPath = 'folder1/folder2/folder3' etc...
 *
 * @package QCubed\Plugin
 */

class CroppieHandler
{
    protected $options;

    public function __construct($options = null)
    {
        $this->options = array(
            'RootPath' => APP_UPLOADS_DIR,
            'TempPath' => APP_UPLOADS_TEMP_DIR,
            'StoragePath' => '_files',
            'TempFolders' =>  ['thumbnail', 'medium', 'large'],
            'ResizeDimensions' => [320, 480, 1500],


            'Data' => null,
            'FileName' => null,
            'OriginalImageName' => null,
            'RelativePath' => null,
            'FolderId' => null,
            'FullStoragePath' => null
        );

        if ($options) {
            $this->options = array_merge($this->options, $options);
        }

        $this->options['FullStoragePath'] = '/' . $this->options['TempPath'] . '/' . $this->options['StoragePath'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->header();
            $this->upload();
        }
    }

    /**
     * @return void
     */
    protected function header()
    {
        // Make sure file is not cached (as it happens for example on iOS devices)
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/json');
    }

    public function upload()
    {
        $json = array();

        if (isset($_POST["cropImage"])) {
            $this->options['Data'] = $_POST['cropImage'];
            $this->options['Name'] = $_POST["fileName"];
            $this->options['RelativePath'] = $_POST["relativePath"];
            $this->options['FolderId'] = $_POST["folderId"];

            $associatedParameters = array_combine($this->options['TempFolders'], $this->options['ResizeDimensions']);

            list($type, $this->options['Data']) = explode(';', $this->options['Data']);
            list(, $this->options['Data']) = explode(',', $this->options['Data']);

            $this->options['Data'] = base64_decode($this->options['Data']);

            $this->options['OriginalImageName'] = $this->options['RootPath'] .  $this->options['RelativePath'] . '/' .  'crop_' .  $this->options['Name'] . '.png';

            if (file_exists($this->options['OriginalImageName'])) {
                $inc = 1;
                while (file_exists($this->options['RootPath'] .  $this->options['RelativePath'] . '/' . 'crop_' .  $this->options['Name'] . '-' . $inc . '.' . 'png')) $inc++;
                $this->options['OriginalImageName'] = $this->options['RootPath'] .  $this->options['RelativePath'] . '/' . 'crop_' .  $this->options['Name'] . '-' . $inc . '.' . 'png';
            }

            file_put_contents($this->options['OriginalImageName'], $this->options['Data']);

            // We create images of different sizes
            foreach ($associatedParameters as $tempFolder => $resizeDimension) {
                if ($this->options['RelativePath'] == null) {
                    $newPath = $this->options['FullStoragePath'] . '/' . $tempFolder . '/' . basename($this->options['OriginalImageName']);
                } else {
                    $newPath = $this->options['FullStoragePath']. '/' . $tempFolder . $this->options['RelativePath'] . '/' . basename($this->options['OriginalImageName']);
                }
                $this->resizeImage($this->options['OriginalImageName'], $resizeDimension,  $newPath);
            }

            $this->uploadInfo();
        }
    }

    // A function that creates images of different sizes
    protected function resizeImage($file, $width, $output) {
        list($originalWidth, $originalHeight) = getimagesize($file);
        $aspectRatio = $originalWidth / $originalHeight;

        // We calculate the new height, maintaining the proportions
        $height = $width / $aspectRatio;

        $src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($width, $height);

        // Let's change the transparency correctly
        imagesavealpha($dst, true);
        $trans_colour = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $trans_colour);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
        imagepng($dst, $output);
        imagedestroy($dst);
        imagedestroy($src);
    }


    /**
     * Send file data
     * @return void
     */
    protected function uploadInfo()
    {
        print json_encode(array(
            'folderId' => $this->options['FolderId'],
            'filename' => basename($this->options['OriginalImageName']),
            'path' => $this->getRelativePath($this->options['OriginalImageName']),
            'extension' => $this->getExtension($this->options['OriginalImageName']),
            'type' => $this->getMimeType($this->options['OriginalImageName']),
            'size' => filesize($this->options['OriginalImageName']),
            'mtime' => filemtime($this->options['OriginalImageName']),
            'dimensions' => $this->getDimensions($this->options['OriginalImageName'])
        ));
    }

    /**
     * Get file path without RootPath
     * @param $path
     * @return string
     */
    public function getRelativePath($path)
    {
        return substr($path, strlen($this->options['RootPath']));
    }

    /**
     * Get file extension
     * @param string $path
     * @return mixed|string
     */
    public static function getExtension($path)
    {
        if(!is_dir($path) && is_file($path)){
            return strtolower(substr(strrchr($path, '.'), 1));
        }
    }

    /**
     * Get mime type
     * @param string $path
     * @return mixed|string
     */
    public static function getMimeType($path)
    {
        if(function_exists('mime_content_type')) {
            return mime_content_type($path);
        } else {
            return function_exists('finfo_file') ? finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path) : false;
        }
    }

    /**
     * Get size of an image
     * @param string $path
     * @return mixed|string
     */
    public static function getDimensions($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $ImageSize = getimagesize($path);

        if (in_array($ext, self::getImageExtensions())) {
            $width = (isset($ImageSize[0]) ? $ImageSize[0] : '0');
            $height = (isset($ImageSize[1]) ? $ImageSize[1] : '0');
            $dimensions = $width . ' x ' . $height;
            return $dimensions;
        }
    }

    /**
     * Get width of an image
     * @param string $path
     * @return mixed|string
     */
    public static function getImageWidth($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $ImageSize = getimagesize($path);

        if (in_array($ext, self::getImageExtensions())) {
            $width = (isset($ImageSize[0]) ? $ImageSize[0] : '0');
            return $width;
        }
    }

    /**
     * Get height of an image
     * @param string $path
     * @return mixed|string
     */
    public static function getImageHeight($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $ImageSize = getimagesize($path);

        if (in_array($ext, self::getImageExtensions())) {
            $height = (isset($ImageSize[1]) ? $ImageSize[1] : '0');
            return $height;
        }
    }

    /**
     * Get image files extensions
     * @return array
     */
    public static function getImageExtensions()
    {
        return array('jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif');
    }

    /**
     * @param $bytes
     * @return string|void
     */
    protected function readableBytes($bytes)
    {
        $i = floor(log($bytes) / log(1024));
        $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
    }

    /**
     * Clean path
     * @param string $path
     * @return string
     */
    public static function cleanPath($path)
    {
        $path = trim($path);
        $path = trim($path, '\\/');
        $path = str_replace(array('../', '..\\'), '', $path);
        if ($path == '..') {
            $path = '';
        }
        return str_replace('\\', '/', $path);
    }
}