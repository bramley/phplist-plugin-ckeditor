# CKEditor Plugin #

## Description ##
This plugin provides CKEditor 4.1 for editing messages and templates within phplist. 

It also integrates the KCFinder file manager to provide file upload and selection.
## Compatibility ###

CKEditor and KCFinder are compatible with all the major browsers, see the CKEditor site <http://ckeditor.com/about>
and the KCFinder site <http://kcfinder.sunhater.com/>

## Installation ##

### Set the plugin directory ###
The default plugin directory is `plugins` within the admin directory.

You can use a directory outside of the web root by changing the definition of `PLUGIN_ROOTDIR` in config.php.
The benefit of this is that plugins will not be affected when you upgrade phplist.
### Install through phplist ###
Install on the Plugins page (menu Config > Plugins) using the package URL `https://github.com/bramley/phplist-plugin-ckeditor/archive/master.zip`.

### Install manually ###
Download the plugin zip file from <https://github.com/bramley/phplist-plugin-ckeditor/archive/master.zip>

Expand the zip file, then copy the contents of the plugins directory to your phplist plugins directory.
This should contain

* the file CKEditorPlugin.php
* the directory CKEditorPlugin

### Location of the CKEditor and KCFinder directories ###
The CKEditor and KCFinder directories must be within the web root. 

If you have the default plugin location then the plugin will use the correct paths automatically.

But if your plugin directory is outside of the web root then you must move or copy the `ckeditor` and `kcfinder` directories from
the plugin's directory to somewhere within the web root.  
Then use the Settings page (menu Config > Settings) to specify the path to each directory.
In the Composition Settings section enter

* the path to CKeditor
* the path to KCFinder 

Similarly, if you already use CKEditor on your web site then you can use that version by specifying the path on the Settings page.
## Configuration ##
The width and height of the editor window can be specified on the Settings page.

The location of a directory where KCFinder can store uploaded images can be specified on the Settings page. The location entered here 
will override the UPLOADIMAGES\_DIR value, if set, in config.php. The directory must be writable by the web server.

Note that KCFinder will create two subdirectories within the specified directory to store the full-size and thumbnail images.
The sub-directory for full-size images can be configured on the Settings page. The default value of `image` is fine for a new installation and for upgrading from FCKEditor.

If the UPLOADIMAGES\_DIR value in config.php is set to `false` then kcFinder will be disabled and image uploading will not be possible.
## Custom configuration ##
Other settings for the editor can be placed in a custom configuration file. This file needs to be within the web root and its
location specified on the Settings page.
A sample custom configuration file is provided which can be used as the basis for your own settings.

See <http://docs.ckeditor.com/#!/api/CKEDITOR.config> for how to specify configuration settings.
## Styles ##

Style definitions can be specified in the custom configuration file, and they will then appear in the Styles drop-down list when editing a message.
Additionally, a CSS stylesheet file can be parsed to provide the style definitions.
See <http://docs.ckeditor.com/#!/guide/dev_styles>

## Custom build of CKEditor ##

You can expand the functionality of CKEditor by adding plugins and creating a custom build, see <http://ckeditor.com/addons/plugins/all>.

You should then install the new CKEditor on your web site and specify the path to the directory on the Settings page.

## Upgrade from phplist 2.10.x with FCKEditor ##

In phplist 2.10 the FCKIMAGES_DIR value in config.php defines the directory into which images will be uploaded.
The value is relative to the phplist root directory.

In phplist 2.11 and later a different value, UPLOADIMAGES_DIR, is used to define the directory. This value is relative to the web root,
not to the phplist root directory.

To continue using the same upload directory you must either set UPLOADIMAGES\_DIR correctly or override UPLOADIMAGES\_DIR by setting
the upload directory on the Settings page.

## Version history ##

    version     Description
    2013-04-27  Fix for GitHub issue 2
    2013-04-22  Fixes for GitHub issues 1 and 3
    2013-04-11  Initial version for phplist 2.11.x releases
