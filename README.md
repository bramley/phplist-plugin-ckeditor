# CKEditor Plugin #

## Description ##
This plugin provides CKEditor for editing messages and templates within phplist. It also integrates the KCFinder file manager to provide file upload and selection.

The plugin has been included in phplist since release 3.2.0 of phplist, so you should not normally need to install the plugin yourself.

## Compatibility ###

CKEditor and KCFinder are compatible with all the major browsers, see the CKEditor site <http://ckeditor.com/about>
and the KCFinder site <http://kcfinder.sunhater.com/>

## Installation ##

### Set the plugin directory ###
The default plugin directory is `plugins` within the admin directory.

You can use a directory outside of the web root by changing the definition of `PLUGIN_ROOTDIR` in config.php.
The benefit of this is that plugins will not be affected when you upgrade phplist.

### Install through phplist ###
Install on the Manage Plugins page (menu Config > Manage Plugins) using the package URL `https://github.com/bramley/phplist-plugin-ckeditor/archive/master.zip`.

### Install manually ###
If installation through phplist does not work then you can install the plugin manually.

Download the plugin zip file from <https://github.com/bramley/phplist-plugin-ckeditor/archive/master.zip>

Expand the zip file, then copy the contents of the plugins directory to your phplist plugins directory.
This should contain

* the file CKEditorPlugin.php
* the directory CKEditorPlugin

### Enable the plugin ###
Click the Enable action button to enable the plugin. Note that only one editor can be enabled.

### Enabling KCFinder ###
The php GD extension is required to use KCFinder. If that extension is not installed then the CKEditor image control will still be available
but without the ability to upload and browse the image directory.

### Location of the KCFinder directory ###
The KCFinder directory must be within the web root.
If you have the default plugin location, `define("PLUGIN_ROOTDIR","plugins")` in config.php, then the plugin will use the correct path automatically.

If you have placed the plugin directory outside of the web root then you must move or copy the `kcfinder` directory from the plugin's
directory to somewhere within the web root.

Then use the Settings page (menu Config > Settings) to specify the path to the KCFinder directory.

The path should be from the web root, such as `/kcfinder`, not the filesystem path.

Also, if you move or rename the phplist directory or the plugin directory after installing the plugin, then you will need
to modify the path to KCFinder as it will not change automatically.

## Usage ##

For guidance on configuring the plugin see the plugin's page within the phplist documentation site <https://resources.phplist.com/plugin/ckeditor>.

## Donation ##

This plugin is free but if you install and find it useful then a donation to support further development is greatly appreciated.

[![Donate](https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=W5GLX53WDM7T4)

## Version history ##

    version     Description
    2.4.0+20200503  Support ckeditor drag and paste an image
    2.3.2+20200202  Undo previous change to fix width of drop-down menus
    2.3.1+20191116  Fix problem with trevelin theme of drop-down menus extending across the window
    2.3.0+20191019  Limit image extension to GD only
    2.2.0+20190206  Avoid KCFinder js file being reported as malware by ClamAV
    2.1.5+20181125  Add ckeditor config entry for file browser upload method
    2.1.4+20180930  Use CKEditor version 4.10.1
    2.1.3+20160603  Improve derivation of path to upload directory
    2.1.2+20160514  Update KCFinder .htaccess file
    2.1.1+20160513  Move configuration documentation to the phplist site
    2.1.0+20160207  Load ckeditor from its CDN
    2.0.2+20160105  Minor internal change
    2.0.1+20151211  Added documentation URL to Manage Plugins page
    2.0.0+20150815  Added dependencies
    2015-07-17      Upgrade to CKEditor 4.5.1 full package
    2015-07-11      Clarify editing as full html page
    2014-12-21      Remove htmlspecialchars() calls on javascript
    2014-10-24      Configurable file directories
    2014-10-04      Upgrade to CKEditor 4.4.5 full package and KCFinder 3.12
    2014-10-02      Config setting for path to image upload directory
    2014-08-14      Display warning when ckeditor path is incorrect. Allow full-page HTML for messages.
    2014-04-30      Display warning when image directory is not writeable
    2014-03-07      Upgraded to CKEditor 4.3.3
    2013-12-22      Allow full HTML page editing for templates
    2013-12-10      Correct example for allowed content
    2013-11-26      Use full URL in links
    2013-09-13      Moved settings into their own CKEditor category
    2013-09-01      Removed setting for upload files, now UPLOADIMAGES_DIR must be specified
    2013-04-27      Fix for GitHub issue 2
    2013-04-22      Fixes for GitHub issues 1 and 3
    2013-04-11      Initial version for phplist 2.11.x releases
