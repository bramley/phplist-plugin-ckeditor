<?php
/**
 * CKEditorPlugin for phplist
 * 
 * This file is a part of CKEditorPlugin.
 *
 * This plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * @category  phplist
 * @package   CKEditorPlugin
 * @author    Duncan Cameron
 * @copyright 2013 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */
class CKEditorPlugin extends phplistPlugin
{
    const VERSION_FILE = 'version.txt';
    const CODE_DIR = '/CKEditorPlugin/';
    /*
     *  Private variables
     */
    private $kcEnabled;

    /*
     *  Inherited variables
     */
    public $name = 'CKEditor plugin';
    public $editorProvider = true;
    public $authors = 'Duncan Cameron';
    public $enabled = 1;

    function __construct()
    {
        $this->kcEnabled = !(defined('UPLOADIMAGES_DIR') && UPLOADIMAGES_DIR === false);
        $this->coderoot = dirname(__FILE__) . self::CODE_DIR;
        $this->version = (is_file($f = $this->coderoot . self::VERSION_FILE))
            ? file_get_contents($f)
            : '';
        $this->settings = array(
            'ckeditor_width' => array (
              'value' => 600,
              'description' => 'Width in px of CKeditor Area',
              'type' => 'integer',
              'allowempty' => 0,
              'min' => 100,
              'max' => 800,
              'category'=> 'composition',
            ),
            'ckeditor_height' => array (
              'value' => 600,
              'description' => 'Height in px of CKeditor Area',
              'type' => 'integer',
              'allowempty' => 0,
              'min' => 100,
              'max' => 800,
              'category'=> 'composition',
            ),
            'ckeditor_path' => array (
              'value' => PLUGIN_ROOTDIR . self::CODE_DIR . 'ckeditor',
              'description' => 'path to CKeditor',
              'type' => 'text',
              'allowempty' => 0,
              'category'=> 'composition',
            ),
            'ckeditor_config_path' => array (
              'value' => '',
              'description' => 'path to CKeditor custom configuration file',
              'type' => 'text',
              'allowempty' => 1,
              'category'=> 'composition',
            )
        );

        if ($this->kcEnabled) {
            $this->settings += array(
                'kcfinder_path' => array (
                  'value' =>  PLUGIN_ROOTDIR . self::CODE_DIR . 'kcfinder',
                  'description' => 'path to KCFinder',
                  'type' => 'text',
                  'allowempty' => 0,
                  'category'=> 'composition',
                ),
                'kcfinder_upload_path' => array (
                  'value' => '',
                  'description' => 'path to file upload directory (overrides UPLOADIMAGES_DIR in config.php)',
                  'type' => 'text',
                  'allowempty' => 1,
                  'category'=> 'composition',
                ),
                'kcfinder_image_directory' => array (
                  'value' => 'image',
                  'description' => 'name of the image subdirectory of the file upload directory',
                  'type' => 'text',
                  'allowempty' => 0,
                  'category'=> 'composition',
                ),
            );
        }
        parent::__construct();
    }

    function adminmenu()
    {
        return array();
    }
  
    function editor($fieldname, $content)
    {
        if ($this->kcEnabled) {
            $_SESSION['KCFINDER'] = array(
                'disabled' => false
            );
            $upload = getConfig('kcfinder_upload_path');

            if ($upload != '' || (defined('UPLOADIMAGES_DIR') && (($upload = UPLOADIMAGES_DIR) != ''))) {
                $upload = ltrim($upload, '/');
                $_SESSION['KCFINDER']['uploadURL'] = "/$upload";
            }
            $kcPath = htmlspecialchars(rtrim(getConfig('kcfinder_path'), '/'));
            $kcImageDir = htmlspecialchars(getConfig('kcfinder_image_directory'));
            $kcBrowserUrls = <<<END
    filebrowserBrowseUrl: '$kcPath/browse.php?type=files',
    filebrowserImageBrowseUrl: '$kcPath/browse.php?type=$kcImageDir',
    filebrowserFlashBrowseUrl: '$kcPath/browse.php?type=flash',
    filebrowserUploadUrl: '$kcPath/upload.php?type=files',
    filebrowserImageUploadUrl: '$kcPath/upload.php?type=$kcImageDir',
    filebrowserFlashUploadUrl: '$kcPath/upload.php?type=flash',
END;
       } else {
            $kcBrowserUrls = '';
       }
        $content = htmlspecialchars($content);
        $path = htmlspecialchars(rtrim(getConfig('ckeditor_path'), '/'));
        $ckConfigPath = htmlspecialchars(rtrim(getConfig('ckeditor_config_path'), '/'));
        $customConfig = $ckConfigPath ? "customConfig: '$ckConfigPath'," : '';

        if ($fieldname == 'footer') {
            $width = getConfig('ckeditor_width');
            $height = 100;
        } else {
            $width = getConfig('ckeditor_width');
            $height = getConfig('ckeditor_height');
        }
        $html = <<<END
<script type="text/javascript" src="$path/ckeditor.js"></script>
<textarea name="$fieldname">$content</textarea>
<script>
CKEDITOR.replace('$fieldname', {
    $customConfig
    $kcBrowserUrls
    width: $width,
    height: $height
});
</script>
END;
        return $html;
    }
}
