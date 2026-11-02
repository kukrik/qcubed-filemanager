# FileManager for QCubed-4

A file management solution built specifically for the **QCubed-4 framework**.

This plugin centralizes all uploaded files into a single location while automatically generating and maintaining derived image versions for efficient usage.

> ⚠️ This is **not a generic PHP file manager**. It is designed specifically for QCubed-4.

---

## ✨ Features

* Chunk upload (large file support)
* Regular upload (fallback mode)
* Automatic image resizing:

    * large (max 1500px)
    * medium (max 480px)
    * thumbnail (max 320px)
* WEBP support (GD-based)
* File usage locking system (green / red / yellow indicators)
* Multi-select file handling
* Drag selection (mouse-based selection of multiple rows)
* Toolbar-based file operations:

    * Rename
    * Crop
    * Copy
    * Move
    * Delete
    * Download
* Breadcrumb navigation
* Quick search
* Multiple views:

    * ImageListView
    * ListView
    * BoxView
* CKEditor integration
* MediaFinder integration
* Database-backed file metadata

---

## 🧱 Architecture

The FileManager uses a predictable and maintainable structure:

```text
upload/           → original files (source of truth)

tmp/_files/
    ├── large/       → resized images (≤1500px)
    ├── medium/      → resized images (≤480px)
    ├── thumbnail/   → resized images (≤320px)
    ├── temp/        → temporary chunk upload files
    └── zip/         → temporary archives for download
```

### Principles

* All original files are stored in one central location
* Resized files are generated separately
* Temporary folders must remain empty outside processing
* File operations stay synchronized across directories

---

## 🖼 Image handling

Uploaded images are automatically resized:

| Folder    | Max size |
| --------- | -------- |
| large     | 1500px   |
| medium    | 480px    |
| thumbnail | 320px    |

Original files are always preserved in the `upload/` directory.

---

## 🗄 Database

Required tables:

* `folders`
* `files`

These are included in the package.

---

## ⚙️ Requirements

* PHP **8.3+**
* QCubed-4
* GD extension
* WEBP support recommended

---

## 📁 Configuration

Verify directory paths in:

```
install/project/includes/configuration/active/2directories.cfg.php
```

Incorrect configuration will break uploads and file handling.

---

## 🧪 Usage

### Navigation

* Double-click → open folder
* Single-click → select file or folder

### Multi-selection

You can select multiple files in two ways:

* Drag the mouse across rows (selection only)
* OR use:

    * `Cmd` (Mac)
    * `Ctrl` (Windows)

### Operations

After selecting files, use toolbar buttons:

* Rename
* Copy
* Move
* Delete
* Download

---

## 🔒 File usage indicators

| Color     | Meaning                            |
| --------- | ---------------------------------- |
| 🟢 Green  | Free file                          |
| 🔴 Red    | Locked (in use by another service) |
| 🟡 Yellow | Reserved / activity locked         |

Controlled via `activities_locked` in the `files` table.

---

## 🔌 Integrations

### CKEditor

Allows:

* inserting images
* linking files

### MediaFinder

Provides quick selection of files for content areas.

---

## 📦 Upload system

### Chunk upload (recommended)

* Handles large files
* Works better on slow connections
* Uses `.part*` temporary files
* Automatically merges chunks

### Regular upload

* Simpler fallback
* Suitable for small files

---

## 🧠 Design philosophy

* Centralize all files
* Keep structure predictable
* Separate original and generated data
* Reflect real usage state in UI
* Avoid hidden file duplication

---

## 📂 Examples

Located in `examples`:

* `dialoog.php`
* `finder.php`

Used mainly for popup integrations.

---

## 🖼 Screenshots

### FileManager

![Image of kukrik](screenshot/filemanager.png?raw=true)

### MediaFinder

![Image of kukrik](screenshot/mediafinder.png?raw=true)

### CKEditor Integration

![Image of kukrik](screenshot/ckeditor3.png?raw=true)

---

## 📥 Installation

```
composer require twbs/bootstrap v3.3.7
```

```
composer require kukrik/qcubed-filemanager
composer require qcubed-4/plugin-bootstrap
composer require kukrik/bootstrap-filecontrol
composer require qcubed-4/plugin-ckeditor
composer require ckeditor/ckeditor
```

---

## ⚠️ Notes

* Built specifically for QCubed-4
* Not framework-agnostic
* `upload/` is the source of truth
* Temporary folders must remain clean

---

## 💬 Feedback

Suggestions and improvements are welcome.
