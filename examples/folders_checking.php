<?php require ('qcubed.inc.php'); ?>
<?php $strPageTitle = 'Comparative check of database and file system synchronization'; ?>
<?php require('header.inc.php'); ?>
<?php $path = APP_UPLOADS_DIR; ?>

<div class="page-content-wrapper">
    <div class="page-content">
        <div class="content-body">
            <div class="files-heading">
                <div class="vauu-title-3"><?= t('Comparative check of database and file system synchronization'); ?></div>
            </div>
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6" style="margin-bottom: 10px;"><strong><?= t('Table "folders" in the database'); ?></strong></div>
                    <div class="col-md-6" style="margin-bottom: 10px;"><strong><?= t('The folder "upload" in the file system'); ?></strong></div>
                </div>
                <div class="row equal">
                    <div class="col-md-6"><?php print '<pre>' ?><?php print_r(dataScan()); ?><?php print '</pre>' ?></div><div class="col-md-6"><?php print '<pre>' ?><?php print_r(scan($path)); ?><?php print '</pre>' ?></div>
                </div>
                <div class="row">
                <div class="col-md-12" style="margin: 10px 0;">
                    <span class="center-button"><button class="btn btn-orange" type="button" onclick="javascript:history.go(-1)"><?= t('Back'); ?></button></span>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

/**
 * Check the synchronicity of folders and database.
 * If they don't match, Filemanager is broken.
 * The reason for this can be either the "folders" table is corrupted or the file system of the "upload" folder is corrupted or an empty folder.
 * In this case, help should be asked from the developer or webmaster.
 *
 * Here is one way to immediately kontrol with the code below (with example):
 *
 * $path = APP_UPLOADS_DIR;
 * print "<pre>";
 * print "<br>DATASCAN:<br>";
 * print_r(dataScan());
 * print "<br>SCAN:<br>";
 * print_r(scan($path));
 * print "</pre>";
 *
 */

function dataScan()
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

function scan($path)
{
    $folders = [];

    if (file_exists($path)) {
        foreach (scandir($path) as $f) {
            if ($f[0] == '.') {
                continue;
            }

            $fullPath = $path . DIRECTORY_SEPARATOR . $f;

            if (is_dir($fullPath)) {
                $folders[] = getRelativePath($fullPath);
                array_push($folders, ...scan($fullPath));
            }
        }
    }

    sort($folders);
    return $folders;
}

/**
 * Get file path without RootPath
 * @param $path
 * @return string
 */
function getRelativePath($path)
{
    return substr($path, strlen(APP_UPLOADS_DIR));
}

?>

<?php require('footer.inc.php');