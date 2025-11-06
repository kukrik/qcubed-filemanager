<?php
require('qcubed.inc.php');

use QCubed as Q;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Project\Control\Button;
use QCubed\Control\Panel;
use QCubed\Event\Click;
use QCubed\Action\Ajax;
use QCubed\Action\ActionParams;

use QCubed\Project\Application;

/**
 * Class SampleForm3
 *
 * This example demonstrates how to call an initialization function to customize the ck editor
 */

class SampleForm3 extends Form
{
    protected Q\Plugin\CKEditor $txtEditor;
    protected Button $btnSubmit;
    protected Panel $pnlResult;

    /**
     * Configures the form by initializing UI components and their properties.
     *
     * Initializes a CKEditor instance for text editing along with a Submit button
     * and result display panel. The editor is pre-filled with data loaded from
     * a specific database entry. The Submit button is configured to trigger an
     * AJAX callback on a click.
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    protected function formCreate(): void
    {
        // This is one possible example, suppose you have created a database table "example"
        // with one column "ids_ids" next to other columns.

        $objExample = Example::load(2);

        $this->txtEditor = new Q\Plugin\CKEditor($this);
        $this->txtEditor->Text = $objExample->getContent() ? $objExample->getContent() : null;
        $this->txtEditor->Configuration = 'ckConfig';
        $this->txtEditor->Rows = 15;

        $this->btnSubmit = new Button($this);
        $this->btnSubmit->Text = "Submit";
        $this->btnSubmit->PrimaryButton = true;
        $this->btnSubmit->AddAction(new Click(), new Ajax('submit_click'));

        $this->pnlResult = new Panel($this);
        $this->pnlResult->HtmlEntities = true;
    }

    /**
     * Handles the submit button click event, processes the editor's content, and updates the result panel.
     *
     * @param ActionParams $params Parameters associated with the action triggering this method.
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    protected function submit_click(ActionParams $params): void
    {
        $objExample = Example::loadById(2);
        $objExample->setContent($this->txtEditor->Text);

        $objExample->save();
        $this->referenceValidation();

        $this->pnlResult->Text = $objExample->getContent();
    }

    // Special attention must be given here when you wish to delete the selected example. It is necessary
    // to inform FileHandler to first decrease the count of locked files ("locked_file").
    // Finally, delete this example.

    // The approximate example below:

    /**
     * Handles the delete button click event, processes and unlocks associated file references, and deletes the main object.
     *
     * @param ActionParams $params Parameters associated with the action triggering this method.
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    protected function delete_Click(ActionParams $params): void
    {
        $objExample = Example::loadById(2);
        $references = $objExample->getFilesIds();

        // The string must be converted to an array
        $nativeFilesIds = [];
        $updatedFilesIds = explode(',', $references);

        foreach ($updatedFilesIds as $filesId) {
            $nativeFilesIds[] = $filesId;
        }

        foreach ($nativeFilesIds as $value) {
            $lockedFile = Files::loadById($value);
            $lockedFile->setLockedFile($lockedFile->getLockedFile() - 1);
            $lockedFile->save();
        }

        $objExample->delete();
    }

    // This function referenceValidation(), which checks and ensures that the data is up to date both when adding and
    // deleting a file. Everything is commented in the code.

    /**
     * Validates and updates references to files linked within the content of an example object.
     * Synchronizes the file locks and updates the example's file IDs with the current content references.
     * Manages the locking/unlocking of files depending on additions or removals in the example's content.
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    protected function referenceValidation(): void
    {
        $objExample = Example::loadById(2);
        $references = $objExample->getFilesIds();
        $content = $objExample->getContent();

        // Regular expression to find the img id attribute
        $patternImgId = '/<img[^>]*\s(?:id=["\']?([^"\'>]+)["\']?)[^>]*>/i';

        // Regular expression to find an id attribute
        $patternAId = $patternAId = '/<a[^>]*\s(?:id=["\']?([^"\'>]+)["\']?)[^>]*>/i';

        $matchesImg = [];
        $matchesA = [];

        // Search for a pattern
        preg_match_all($patternImgId, $content, $matchesImg);
        preg_match_all($patternAId, $content, $matchesA);

        // Merge arrays into one
        $combinedArray = array_merge($matchesImg[1], $matchesA[1]);

        if (!$references) {
            $saveFilesIds = implode(',', $combinedArray);
            $objExample->setFilesIds($saveFilesIds);
            $objExample->save();

            foreach ($combinedArray as $value) {
                $lockedFile = Files::loadById($value);
                $lockedFile->setLockedFile($lockedFile->getLockedFile() + 1);
                $lockedFile->save();
            }
        } else {
            // The string must be converted to an array
            $nativeFilesIds = [];
            $updatedFilesIds = explode(',', $references);
            foreach ($updatedFilesIds as $filesId) {
                $nativeFilesIds[] = $filesId;
            }

            // Equal values are proven
            $result = array_intersect($combinedArray, $nativeFilesIds);

            // Content has more IDs than FilesIds fewer references.
            // Then call back to FileHandler to lock that file (+ 1).
            $lockFiles = array_diff($combinedArray, $nativeFilesIds);

            // Content has fewer IDs than FilesIds, has more references.
            // Then call back to FileHandler to unclog that file (- 1).
            $unlockFiles = array_diff($nativeFilesIds, $combinedArray);

//            Application::displayAlert("RESULT: " . json_encode($result));
//            Application::displayAlert("LockFiles: " . json_encode($lockFiles));
//            Application::displayAlert("UnlockFiles: " . json_encode($unlockFiles));

            // Here it is always necessary to report to the "files" table to either lock or release an image or file.
            // In the first order, this table should be updated, then the content should be updated.
            // If this is not done, the FileHandler will not report the correct information about whether the files
            // are free or not. This is so that files occupied by others cannot be accidentally deleted in the FileHandler.

            if (count($lockFiles)) {
                foreach ($lockFiles as $value) {
                    $lockedFile = Files::loadById($value);
                    $lockedFile->setLockedFile($lockedFile->getLockedFile() + 1);
                    $lockedFile->save();
                }

                // Overwriting example data
                $updatedFilesIds = implode(',', $combinedArray);
                $objExample->setFilesIds($updatedFilesIds);
                $objExample->save();
            }

            if (count($unlockFiles)) {
                foreach ($unlockFiles as $value) {
                    $unlockFile = Files::loadById($value);
                    $unlockFile->setLockedFile($unlockFile->getLockedFile() - 1);
                    $unlockFile->save();
                }

                // Overwriting example data
                $updatedFilesIds = implode(',', $combinedArray);
                $objExample->setFilesIds($updatedFilesIds);
                $objExample->save();
            }
        }

    }

}
SampleForm3::run('SampleForm3');
