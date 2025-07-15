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
                    <span class="center-button"><button class="btn btn-orange" type="button" onclick="history.go(-1)"><?= t('Back'); ?></button></span>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

    use QCubed\Exception\Caller;

    /**
     * Scans and processes folder data.
     *
     * This method retrieves all folder paths, modifies the resulting array by
     * removing the first element, and sorts the remaining paths in ascending order.
     *
     * @return array An array of sorted folder paths with the first element removed.
     * @throws Caller
     */

    function dataScan(): array
    {
        $folders = Folders::loadAll();

        // Use an array map to extract paths.
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
     * Recursively scans a directory to retrieve relative paths of all subfolders.
     * This method traverses through the given directory, and for each folder, it fetches
     * its relative path and recursively processes its subdirectories.
     *
     * @param string $path The absolute path of the directory to be scanned.
     *
     * @return array An array of relative paths for all folders within the given directory, sorted alphabetically.
     */
    function scan(string $path): array
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
     * Retrieves the relative path of a given absolute path.
     *
     * This method calculates the relative path by removing the base uploads directory
     * path portion from the provided absolute path.
     *
     * @param string $path The absolute path to process.
     *
     * @return string The relative path derived from the provided absolute path.
     */
    function getRelativePath(string $path): string
    {
        return substr($path, strlen(APP_UPLOADS_DIR));
    }

?>

<?php require('footer.inc.php');