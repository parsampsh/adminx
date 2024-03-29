# Adminx File Manager
You can provide a file manager in your admin panel with a lot of features for customizing things using this builtin plugin.

### Getting started
To enable the pulgin:

```php
// ...

use Adminx\Plugins\Builtins\FileManager\FileManagerPlugin;
$admin->addPlugin(new FileManagerPlugin);

// ...
```

### Access middleware
option `access_middleware` must be a closure that authorizes users to get access to the file manager:

```php
$admin->addPlugin(new FileManagerPlugin, [
    'access_middleware' => (function ($user) {
        return $user->canAccessFiles === true; // or something
    })
]);
```

### Page slug
You can set slug of the file manager page:

```php
$admin->addPlugin(new FileManagerPlugin, [
    'page_slug' => 'my-file-manager',
]);
```

Then the url will be `/admin/page/my-file-manager`.

The default url is `/admin/page/file-manager`.

### Including directories in file manager
To set which directories and files must be included in your file manager:

```php
$admin->addPlugin(new FileManagerPlugin, [
    'dirs' => [
        '/foo/bar',
        '/hello/world',
    ],
]);
```

## Permissions on operations on files
There are 4 kinds of operation on files: See, Read, Delete, Write.

You can handle them using `can_see`, `can_read`, `cen_delete`, `cen_write`:

```php
use Adminx\Plugins\Builtins\FileManager\FileItem;
$admin->addPlugin(new FileManagerPlugin, [
    // determines that can user see this file in files list or not
    'can_see' => (function (User $user, FileItem $file) { return true; }),

    // determines that can user read the content of the file or not (or download it)
    'can_read' => (function (User $user, FileItem $file) { return true; }),

    // determines that can user delete this file or not
    'can_delete' => (function (User $user, FileItem $file) { return true; }),

    // determines that can user edit/replace this file or not
    'can_write' => (function (User $user, FileItem $file) { return true; }),
]);
```

#### Class FileItem
This class is used as a model for files.

You can access the file path using `$file->path`.

Also there are some more methods that you can check.

#### Cut & Copy & Rename operations
A file/directory can be **copied** to somewhere else when user can read that file and also has write permission on the target directory that file is going to be copied in.

A file/directory can be **cuted** when user has permission for reading and deleting it and also permission for writing in the target directory.

A file/directory can be **renamed** when user has permission to read it and delete it. same as cut.

#### Directory download
File manager can make directories downloadable.
It just makes a zip file from directory and user can download it.

There is another middleware named `can_download_directory`.
You can handle exceptions about downloading the whole directory using this:

```php
$admin->addPlugin(new FileManagerPlugin, [
    'can_download_directory' => (function (User $user, FileItem $file) { return true; }),
]);
```

### Upload middleware
You can set a middleware for upload process to validate the files that are going to be uploaded somewhere:

```php
$admin->addPlugin(new FileManagerPlugin, [
    'upload_middleware' => (function (FileItem $directory, $file) { return $file->getClientOriginalExtension() !== 'php'; }),
]);
```

As you can see, there are 2 arguments for this closure, first one is the directory that file is gonna be uploaded to, and the second one is the uploaded file object.

If your closure returns true, file will be uploaded and if this returns false, file won't be uploaded.

### Customizing language
You can customize language of the file manager.
You can use word system of the Adminx.

Example:

```php
$admin->setWord('adminx.filemanager.main_title', 'Admin panel');
```

All the keys for the file manager start with `adminx.filemanager.`.
You can see list of all of them here:

- `adminx.filemanager.main_title`: Main title of the admin panel
- `adminx.filemanager.delete`: Title of the delete button
- `adminx.filemanager.delete_btn_confirmation`: Delete confirmation message
- `adminx.filemanager.copy`: Title of the copy button
- `adminx.filemanager.download`: Title of the download button
- `adminx.filemanager.cut`: Title of the cut button
- `adminx.filemanager.rename`: Title of the rename button
- `adminx.filemanager.edit`: Title of the edit button
- `adminx.filemanager.main_title`: Main title of the panel
- `adminx.filemanager.back`: Title of the back button
- `adminx.filemanager.are_you_sure`: Message "Are you sure" at rename process
- `adminx.filemanager.change_current_name`: Message "You must change current name" at rename process
- `adminx.filemanager.not_found`: Message "file not found"
- `adminx.filemanager.paste`: Title of the paste button
- `adminx.filemanager.upload`: Title of the upload button
- `adminx.filemanager.cant_read_file`: Message "You can't read this file"
- `adminx.filemanager.file`: Word "File" which is written before path of the current file
- `adminx.filemanager.current_location`: "current location" which is written before path of the current directory
- `adminx.filemanager.new_file`: Title of new file button
- `adminx.filemanager.create_new_file`: Placeholder of the new file input
- `adminx.filemanager.file_already_exists`: Message "File already exists" which is used in rename, upload and... processes
- `adminx.filemanager.cant_upload_file`: Message "Cannot upload the file" whoch will be shown when the upload middleware returns false response
