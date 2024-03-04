<?php require(QCUBED_CONFIG_DIR . '/header.inc.php'); ?>

<?php
// https://ckeditor.com/docs/ckeditor4/latest/guide/dev_file_browse_upload.html
?>
	<script>
		ckConfig = {
			skin: 'moono',
            extraPlugins: 'colorbutton,font,justify,print,tableresize,pastefromword,liststyle',
            filebrowserImageBrowseUrl: 'dialog.php',
            filebrowserBrowseUrl: 'dialog.php',
            filebrowserWindowWidth: '95%',
            filebrowserWindowHeight: '95%',
			//language: 'en',
			//uiColor: '#9AB8F3'
			toolbar: [
				{ name: 'clipboard', items: [ 'PasteFromWord', '-', 'Undo', 'Redo' ] },
				{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'Subscript', 'Superscript' ] },
				{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
				{ name: 'insert', items: [ 'Image', 'Table' ] },
                { name: 'links', items: [ 'Link', 'Unlink' ] },
				'/',

				{ name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
				{ name: 'colors', items: [ 'TextColor', 'BGColor', 'CopyFormatting' ] },
				{ name: 'align', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
				{ name: 'document', items: [ 'Print', 'Source' ] }
			],

			// Enabling extra plugins, available in the full-all preset: https://ckeditor.com/cke4/presets
			/*extraPlugins: 'colorbutton,font,justify,print,tableresize,pastefromword,liststyle',
			removeButtons: '',

			// Make the editing area bigger than default.
			height: 350//,
			//width: 940*/
		};


	</script>
<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<h1 class="instruction_title">CKEditor: Implementing CKEditor HTML editor and connecting with FileManager</h1>
		<p>
			<b>CKEditor</b> implements the <a href="https://ckeditor.com/ckeditor-4/">CKEditor HTML editor</a>. It allows you
			to create a text editing block with full HTML editing capabilities. The text returned from it is HTML.
		</p>
        <p>A custom file manager has been integrated here, allowing for linking files and images.</p>
        <p>If necessary, add another column to a second table in the database (for example, "content," etc.), such as
            "files_id," and save the records.</p>

        <p>A problem may arise, for example, when attempting to delete a file or image from the content. Currently,
            it is not possible to check in CKEditor, and as a result, it is not possible to reduce the "locked_file"
            count in the "files" table by one.</p>

        <p>There are several possible solutions. The one option currently provided is to use the function referenceValidation(),
            which checks and ensures that the data is up-to-date both when adding and deleting a file.
            Everything is commented in the code.</p>

		<?= _r($this->txtEditor); ?>
		<p><?= _r($this->btnSubmit); ?></p>
		<h4>The HTML you typed:</h4>
		<?= _r($this->pnlResult); ?>
	</div>

<?php $this->RenderEnd(); ?>
<?php require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>
