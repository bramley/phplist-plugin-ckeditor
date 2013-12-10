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

There is a bug that can cause a plugin to be incompletely installed on some configurations (<https://mantis.phplist.com/view.php?id=16865>). 
Check that these files are in the plugin directory. If not then you will need to install manually.

* the file CKEditorPlugin.php
* the directory CKEditorPlugin

### Install manually ###
Download the plugin zip file from <https://github.com/bramley/phplist-plugin-ckeditor/archive/master.zip>

Expand the zip file, then copy the contents of the plugins directory to your phplist plugins directory.
This should contain

* the file CKEditorPlugin.php
* the directory CKEditorPlugin

### Enable the plugin ###
Click the small orange icon to enable the plugin. Note that only one editor should be enabled, otherwise phplist will choose the first
that it finds.

### Location of the CKEditor and KCFinder directories ###
The CKEditor and KCFinder directories must be within the web root. 

If you have the default plugin location then the plugin will use the correct paths automatically.

But if your plugin directory is outside of the web root then you must move or copy the `ckeditor` and `kcfinder` directories from
the plugin's directory to somewhere within the web root.  
Then use the Settings page (menu Config > Settings) to specify the path to each directory.
In the `CKEditor settings` section enter

* the path to CKeditor
* the path to KCFinder 

Similarly, if you already use CKEditor on your web site then you can use that version by specifying the path on the Settings page.
## Configuration ##
The width and height of the editor window can be specified on the Settings page.

The UPLOADIMAGES\_DIR value in config.php must be set to the location of a directory where KCFinder can store uploaded images.
The directory must be writable by the web server. Note that the value is relative to the web root and must not contain a leading '/'.

KCFinder will create two subdirectories within the specified directory to store the full-size and thumbnail images.
The name of the sub-directory for full-size images can be configured on the Settings page. The default value of `image` is fine for a new installation and for upgrading from FCKEditor.

If the UPLOADIMAGES\_DIR value in config.php is set to `false` then kcFinder will be disabled and image uploading will not be possible.
## Custom configuration ##
Other settings for the editor can be placed in a custom configuration file. This file needs to be within the web root and its
location specified on the Settings page. A sample custom configuration file `CKEditorPlugin/sample.ckconfig.js` is provided which can be used as the basis for your own settings.

See <http://docs.ckeditor.com/#!/api/CKEDITOR.config> for how to specify configuration settings.
## Styles ##

Style definitions can be specified in the custom configuration file, and they will then appear in the Styles drop-down list when editing a message.
Additionally, a CSS stylesheet file can be parsed to provide the style definitions. The sample custom configuration file has an example of how to
define styles.

See <http://docs.ckeditor.com/#!/guide/dev_styles>

## Custom build of CKEditor ##

The plugin provides the Standard build of CKEditor. You can use the Basic or Full builds instead, or add plugins to create a custom build, see <http://ckeditor.com/download>.

To install the build, expand the zip file, copy the ckeditor directory to your web site, and specify the path to the directory on the Settings page. It is recommended to use a new directory rather than overwriting the version in the plugin's directory, so that it will not be affected when you upgrade the plugin.

## Upgrade from phplist 2.10.x with FCKEditor ##

In phplist 2.10 the FCKIMAGES_DIR value in config.php defines the directory into which images will be uploaded.
The value is relative to the phplist root directory.

In phplist 3.x a different value, UPLOADIMAGES\_DIR, is used to define the directory. This value is relative to the web root,
not to the phplist root directory. To continue using the same upload directory you must set UPLOADIMAGES\_DIR correctly.
So, for example, if the existing image upload directory is /lists/uploadimages then the FCKIMAGES\_DIR would be `uploadimages` but the 
value for UPLOADIMAGES\_DIR would be `lists/uploadimages`.

## Donation ##

This plugin is free but if you install and find it useful then a donation to support further development is greatly appreciated.

[![Donate](https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=W5GLX53WDM7T4)

## Version history ##

    version     Description
    2013-12-10  Correct example for allowed content
    2013-11-26  Use full URL in links
    2013-09-13  Moved settings into their own CKEditor category
    2013-09-01  Removed setting for upload files, now UPLOADIMAGES_DIR must be specified
    2013-04-27  Fix for GitHub issue 2
    2013-04-22  Fixes for GitHub issues 1 and 3
    2013-04-11  Initial version for phplist 2.11.x releases
