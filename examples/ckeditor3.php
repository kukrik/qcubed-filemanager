<?php
require('qcubed.inc.php');

use QCubed as Q;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Project\Control\Button;
use QCubed\Control\Panel;
use QCubed\Event\Click;
use QCubed\Action\Ajax;
use QCubed\Action\ActionParams;

/**
 * Class SampleForm3
 *
 * This example demonstrates how to call an initialization function to customize the ck editor
 */

class SampleForm3 extends Form
{
	protected $txtEditor;
	protected $btnSubmit;
	protected $pnlResult;

	protected function formCreate()
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

	protected function submit_click(ActionParams $params)
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

    // Approximate example below:

    /*protected function delete_Click(ActionParams $params)
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
    }*/

    // This function referenceValidation(), which checks and ensures that the data is up-to-date both when adding and
    // deleting a file. Everything is commented in the code.

    private function referenceValidation()
    {
        $objExample = Example::loadById(2);
        $references = $objExample->getFilesIds();
        $content = $objExample->getContent();

        // Regular expression to find the img id attribute
        $patternImgId = '/<img[^>]*\s(?:id=["\']?([^"\'>]+)["\']?)[^>]*>/i';

        // Regular expression to find the a id attribute
        $patternAId = $patternAId = '/<a[^>]*\s(?:id=["\']?([^"\'>]+)["\']?)[^>]*>/i';

        $matchesImg = [];
        $matchesA = [];

        // Search for a pattern
        preg_match_all($patternImgId, $content, $matchesImg);
        preg_match_all($patternAId, $content, $matchesA);

        // Merge arrays into one
        $combinedArray = array_merge($matchesImg[1], $matchesA[1]);

        if (!strlen($references)) {
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

            // Content has more ids than FilesIds less references. TULEMUS: test 1 annab vastuse 1124, test 2 tühja massiivi
            // Then call back to FileHandler to lock that file (+ 1 ).
            $lockFiles = array_diff($combinedArray, $nativeFilesIds);

            // Content has fewer IDs than FilesIds, has more references. TULEMUS: test 1 annab tühja massiivi, test 2 annab vastuse
            // Then call back to FileHandler to unclog that file ( - 1 ).
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
SampleForm3::Run('SampleForm3');
