<?php

namespace QCubed\Plugin;

use QCubed\Project\Application;

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
            'TempFolders' => ['thumbnail', 'medium', 'large'],
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
     * Set HTTP headers to prevent caching.
     * @return void
     */
    protected function header()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/json');
    }

    /**
     * Main upload process.
     */
    public function upload()
    {
        if (isset($_POST["cropImage"])) {
            $this->options['Data'] = $_POST['cropImage'];
            $this->options['Name'] = $_POST["fileName"];
            $this->options['RelativePath'] = $_POST["relativePath"];
            $this->options['FolderId'] = $_POST["folderId"];

            $path = $_POST["relativePath"];

            $associatedParameters = array_combine($this->options['TempFolders'], $this->options['ResizeDimensions']);

            // Decode the base64 image data
            list($type, $this->options['Data']) = explode(';', $this->options['Data']);
            list(, $this->options['Data']) = explode(',', $this->options['Data']);
            $this->options['Data'] = base64_decode($this->options['Data']);

            // Generate the original file name and path
            $this->options['OriginalImageName'] = $this->options['RootPath'] . $this->options['RelativePath'] . '/' . 'crop_' . $this->options['Name'] . '.png';

            // Handle duplicate file names
            if (file_exists($this->options['OriginalImageName'])) {
                $inc = 1;
                $basePath = $this->options['RootPath'] . $this->options['RelativePath'];
                while (file_exists($basePath . '/' . 'crop_' . $this->options['Name'] . '-' . $inc . '.png')) $inc++;
                $this->options['OriginalImageName'] = $basePath . '/' . 'crop_' . $this->options['Name'] . '-' . $inc . '.png';
            }

            // Save the original file
            file_put_contents($this->options['OriginalImageName'], $this->options['Data']);

            // Generate resized images
            foreach ($associatedParameters as $tempFolder => $resizeDimension) {
                $relativePath = $this->options['RelativePath'];
                $newPath = $this->options['FullStoragePath'] . '/' . $tempFolder . ($relativePath ? $relativePath : '') . '/' . basename($this->options['OriginalImageName']);
                $this->resizeImage($this->options['OriginalImageName'], $resizeDimension, $newPath);
            }

            $this->uploadInfo();
        }
    }

    /**
     * Resize an image and save it.
     *
     * @param string $file
     * @param int $width
     * @param string $output
     * @return void
     * @throws \Exception
     */
    protected function resizeImage($file, $width, $output)
    {
        list($originalWidth, $originalHeight) = getimagesize($file);

        if (!$originalWidth || !$originalHeight) {
            throw new \Exception("Invalid image file: $file");
        }

        $aspectRatio = $originalWidth / $originalHeight;

        // Calculate height and force integer conversion
        $height = (int) ($width / $aspectRatio);
        $width = (int) $width;

        $src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($width, $height);

        // Handle transparency
        imagesavealpha($dst, true);
        $trans_colour = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $trans_colour);

        // Resample the image
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

        // Save the resized image
        $output = $output;
        imagepng($dst, $output);

        // Free memory
        imagedestroy($dst);
        imagedestroy($src);
    }

    /**
     * Send file data after processing.
     * @return void
     */
    protected function uploadInfo()
    {
        print json_encode(array(
            'folderId' => $this->options['FolderId'],
            'filename' => basename($this->options['OriginalImageName']),
            'path' => $this->getRelativePath($this->replaceDoubleSlashWithSlash($this->options['OriginalImageName'])),
            'extension' => $this->getExtension($this->options['OriginalImageName']),
            'type' => $this->getMimeType($this->options['OriginalImageName']),
            'size' => filesize($this->options['OriginalImageName']),
            'mtime' => filemtime($this->options['OriginalImageName']),
            'dimensions' => $this->getDimensions($this->options['OriginalImageName']),
            'width' => $this->getImageWidth($this->options['OriginalImageName']),
            'height' => $this->getImageHeight($this->options['OriginalImageName']),
        ));
    }

    /**
     * Get width of an image
     * @param string $path
     * @return int
     */
    public static function getImageWidth($path)
    {
        $ImageSize = getimagesize($path);
        return isset($ImageSize[0]) ? $ImageSize[0] : 0;
    }

    /**
     * Get height of an image
     * @param string $path
     * @return int
     */
    public static function getImageHeight($path)
    {
        $ImageSize = getimagesize($path);
        return isset($ImageSize[1]) ? $ImageSize[1] : 0;
    }

    /**
     * Get file path relative to RootPath.
     * @param string $path
     * @return string
     */
    public function getRelativePath($path)
    {
        return substr($path, strlen($this->options['RootPath']));
    }

    /**
     * Get file extension.
     * @param string $path
     * @return string
     */
    public static function getExtension($path)
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * Get file MIME type.
     * @param string $path
     * @return string|false
     */
    public static function getMimeType($path)
    {
        if (function_exists('mime_content_type')) {
            return mime_content_type($path);
        } else if (function_exists('finfo_file')) {
            return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
        } else {
            return false;
        }
    }

    /**
     * Get image dimensions in "width x height" format.
     * @param string $path
     * @return string|null
     */
    public static function getDimensions($path)
    {
        $ImageSize = getimagesize($path);
        if ($ImageSize) {
            return $ImageSize[0] . ' x ' . $ImageSize[1];
        }
        return null;
    }

    /**
     * Replace any occurrence of double slashes (//) in a given path with a single slash (/).
     * @param string $path The input path that may contain double slashes.
     * @return string The processed path with double slashes replaced by single slashes.
     */
    protected function replaceDoubleSlashWithSlash(string $path): string
    {
        // Replace any occurrence of double slashes (//) with a single slash (/)
        return preg_replace('#/{2,}#', '/', $path);
    }


    /**
     * Clean and sanitize a path string.
     * @param string $path
     * @return string
     */
    public static function cleanPath($path)
    {
        $path = trim($path);
        $path = trim($path, '\\/');
        $path = str_replace(array('../', '..\\'), '', $path);
        return str_replace('\\', '/', $path);
    }
}
