# QCubed FileManager Plugin

## FileManager for QCubed v4

The FileManager plugin includes itself, UploadHandler, MediaFinder, and the ckeditor image and file plugin.

* The plugin author maintains that the correct approach is to consolidate all uploaded images and files into a specific 
location rather than scattering them throughout the file system. In line with this, FileManager has been developed. 
* Upon the initial deployment of FileManager, an "upload" folder is automatically created, where original images and 
files are consolidated. Simultaneously, within the "tmp" folder, the first folder "_files" is automatically created, 
containing subfolders "large," "medium," "thumbnail," and "zip".
* The UploadHandler automatically generates images in folders "large," "medium," and "thumbnail" with suitable dimensions: 
up to 1500 px, up to 480 px, up to 320 px, respectively.
* The "zip" folder is intended for compressing and sending downloaded files and folders to the user's browser. 
This folder must always be empty.
* Various file operations within the "upload" folder are automatically reflected in the temporary folders as well. 
FileHandler takes care of all these aspects.
* The files in the "examples" folder, namely dialoog.php and finder.php, are copies of filemanager.php with added 
specify functionalities. These operate in pop-up windows.

Before putting it into use, please check the constants:
https://github.com/qcubed-4/application/blob/master/install/project/includes/configuration/active/2directories.cfg.php#L29

![Image of kukrik](screenshot/filemanager.png?raw=true)
![Image of kukrik](screenshot/mediafinder.png?raw=true)
![Image of kukrik](screenshot/ckeditor3.png?raw=true)

### FileManager Usage Options:

* Upload, add folder, refresh page, rename, copy, delete, move, download
* 3 different views: ImageListView, ListView, BoxView
* Breadcrumbs for quick navigation
* Quick search
* Language support, currently supporting the following languages: English, Estonian, and Russian. If you wish to contribute 
to language translation, everyone is welcome!


### Use cases for FileManager:

* To navigate between directories, use double-click, otherwise use a single click.
* If you want to use multiple files and folders, simply drag the mouse over the rows in the table.
Or, if you want to select files and folders, depending on your computer's operating system (Mac or Windows), 
hold down the "Command" or "Ctrl" key, respectively, and choose with the mouse.
* In the table, you'll see small round icons in 3 different colors (green, red, and yellow). 
These indicate the usage status of files by other services (e.g., articles, news, etc.):
  * Green small icon - free image or file. 
  * Red small icon - image or file used by other services, meaning it's locked. In this case, you cannot accidentally 
delete or move it in FileManager.
  * Yellow small icon - this is intended, for example, if you want to create a separate gallery plugin, you could put 
all uploaded images in the "upload" folder; just add a value of 1 to the "activities_locked" column in the "files" 
table in the database. This way, you can't delete, rename, or move these images in FileManager, and the gallery plugin 
has exclusive rights. Other services can still use gallery images. If you want to use a gallery image in an article, 
news, or elsewhere, it is advisable to copy the image to another location and use it there.
  
    * To create a gallery plugin, you can use, for example, the UploadHandler plugin and supplement it with other 
necessary things: https://github.com/kukrik/qcubed-fileupload.

**NOTE! For FileManager to function correctly and display accurate information, the codes in "filehandler.php," "mediafinder.php," and "ckeditor3.php" are equipped with comments or instructions. These comments/guidelines will help keep you on the right track.**

**Any feedback is highly welcome and will contribute to improving or enhancing FileManager!**

If you have not previously installed QCubed Bootstrap and twitter bootstrap, run the following actions on the command 
line of your main installation directory by Composer:
```
    composer require twbs/bootstrap v3.3.7
```
and

```
    composer require kukrik/qcubed-filemanager
    composer require kukrik/bootstrap
    composer require kukrik/bootstrap-filecontrol
    composer require kukrik/select2
```

