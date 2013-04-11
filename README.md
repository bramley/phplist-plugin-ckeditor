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
Install on the Plugins page (menu Config > Plugins) using the package URL `https://github.com/bramley/phplist-ckeditorplugin/archive/master.zip`.

### Install manually ###
Download the plugin zip file from <https://github.com/bramley/phplist-ckeditorplugin/archive/master.zip>

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
will over-ride the UPLOADIMAGES_DIR value, if set, in config.php.
Note that KCFinder will create two subdirectories, images and .thumbs, within the specified directory to store the full-size and thumbnail images.

## Custom configuration ##
Other settings for the editor can be placed in a custom configuration file. This file needs to be within the web root and its
location specified on the Settings page.
A sample custom configuration file is provided which can be used as the basis for your own settings.

See <http://docs.ckeditor.com/#!/api/CKEDITOR.config> for how to specify configuration settings.
## Styles ##

Style definitions can be specified in the custom configuration file, and they will then appear in the Styles drop-down list when editing a message.
Additionally, a CSS stylesheet file can be parsed to provide the style definitions.
See <http://docs.ckeditor.com/#!/guide/dev_styles>
## Version history ##

    version     Description
    2013-04-11  Initial version for phplist 2.11.x releases
