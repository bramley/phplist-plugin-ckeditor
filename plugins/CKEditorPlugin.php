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
    public $description = 'Provides the CKEditor for editing messages and templates.';
    public $enabled = 1;

    private function kcFinderScript($function)
    {
    //  see http://kcfinder.sunhater.com/docs/integrate Custom Applications
        $kcPath = rtrim(getConfig('kcfinder_path'), '/');
        $kcImageDir = getConfig('kcfinder_image_directory');
        $kcUrl = htmlspecialchars("$kcPath/browse.php?type=$kcImageDir");
        $html = <<<END
<script type='text/javascript'>
$function = function(callback) {
    window.KCFinder = {};
    window.KCFinder.callBack = function(url) {
        callback(url);
        window.KCFinder = null;
    };
    window.open('$kcUrl', '', 'width=600,height=500');
}
</script>
END;
        return $html;
    }

    private function editorScript($fieldname, $width, $height, $toolbar)
    {
        global $website, $public_scheme, $systemroot;

        $file =  rtrim(getConfig('ckeditor_path'), '/') . '/ckeditor.js';

        if ($file[0] == '/') {
            $file = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $file;
        } else {
            $file = $systemroot . '/' . $file;
        }
        if (!is_file($file) ) {
            return sprintf(
                '<div class="note error">CKEditor is not available because the ckeditor file "%s" does not exist. Check your setting for the path to ckeditor.</div>',
                $file
            );
        }

        $html = '';
        $settings = array();
        $settings[] = 'allowedContent: true';

        $fullTemplate = getConfig('ckeditor_fulltemplate');
        $fullMessage = getConfig('ckeditor_fullmessage');

        if ($fieldname == 'template' && $fullTemplate) {
            $settings[] = 'fullPage: true';
        }

        if ($fieldname == 'message' && $fullMessage) {
            $settings[] = 'fullPage: true';
        }

        if ($this->kcEnabled) {
            $session = array(
                'disabled' => false,
                'uploadURL' => sprintf('%s://%s/%s', $public_scheme, $website, ltrim(UPLOADIMAGES_DIR, '/'))
            );
            $kcUpload = false;
            $kcUploadDir = getConfig('kcfinder_uploaddir');
            
            if ($kcUploadDir) {
                if (is_writeable($kcUploadDir)) {
                    $session['uploadDir'] = $kcUploadDir;
                    $kcUpload = true;
                }
            } elseif (is_writeable($kcUploadDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . trim(UPLOADIMAGES_DIR, '/'))) {
                $kcUpload = true;
            }

            if ($kcUpload) {
                $kcImageDir = getConfig('kcfinder_image_directory');
                $kcFilesDir = getConfig('kcfinder_files_directory');
                $kcFlashDir = getConfig('kcfinder_flash_directory');
                $session['types'] = array(
                    $kcFilesDir   =>  "",
                    $kcFlashDir   =>  "swf",
                    $kcImageDir  =>  "*img",
                );

                $_SESSION['KCFINDER'] = $session;
                $kcPath = rtrim(getConfig('kcfinder_path'), '/');
                $settings[] = <<<END
filebrowserBrowseUrl: '$kcPath/browse.php?opener=ckeditor&type=$kcFilesDir',
filebrowserImageBrowseUrl: '$kcPath/browse.php?opener=ckeditor&type=$kcImageDir',
filebrowserFlashBrowseUrl: '$kcPath/browse.php?opener=ckeditor&type=$kcFlashDir',
filebrowserUploadUrl: '$kcPath/upload.php?opener=ckeditor&type=$kcFilesDir',
filebrowserImageUploadUrl: '$kcPath/upload.php?opener=ckeditor&type=$kcImageDir',
filebrowserFlashUploadUrl: '$kcPath/upload.php?opener=ckeditor&type=$kcFlashDir'
END;
            } else {
                $html .= sprintf(
                    '<div class="note error">Image browsing is not available because directory "%s" does not exist or is not writeable.</div>',
                    htmlspecialchars($kcUploadDir)
                );
            }
        }

        $path = htmlspecialchars(rtrim(getConfig('ckeditor_path'), '/'));
        $ckConfigPath = rtrim(getConfig('ckeditor_config_path'), '/');

        if ($ckConfigPath) {
            $settings[] = "customConfig: '$ckConfigPath'";
        }

        if ($width) {
            $settings[] = "width: $width";
        }

        if ($height) {
            $settings[] = "height: $height";
        }

        if ($toolbar) {
            $settings[] = "toolbar: '$toolbar'";
        }
        $configSettings = implode(",\n", $settings);
        $html .= <<<END
<script type="text/javascript" src="$path/ckeditor.js"></script>
<script><!--
CKEDITOR.replace('$fieldname', {
$configSettings
});
--></script>
END;
        return $html;
    }

    public function __construct()
    {
        $this->kcEnabled = defined('UPLOADIMAGES_DIR') && UPLOADIMAGES_DIR !== false;
        $this->coderoot = dirname(__FILE__) . self::CODE_DIR;
        $this->version = (is_file($f = $this->coderoot . self::VERSION_FILE))
            ? file_get_contents($f)
            : '';
        $this->settings = array(
            'ckeditor_path' => array (
              'value' => PLUGIN_ROOTDIR . self::CODE_DIR . 'ckeditor',
              'description' => 'Path to CKeditor',
              'type' => 'text',
              'allowempty' => 0,
              'category'=> 'CKEditor',
            ),
            'ckeditor_config_path' => array (
              'value' => '',
              'description' => 'Path to CKeditor custom configuration file',
              'type' => 'text',
              'allowempty' => 1,
              'category'=> 'CKEditor',
            ),
            'ckeditor_width' => array (
              'value' => 600,
              'description' => 'Width in px of CKeditor Area',
              'type' => 'integer',
              'allowempty' => 0,
              'min' => 100,
              'max' => 800,
              'category'=> 'CKEditor',
            ),
            'ckeditor_height' => array (
              'value' => 600,
              'description' => 'Height in px of CKeditor Area',
              'type' => 'integer',
              'allowempty' => 0,
              'min' => 100,
              'max' => 800,
              'category'=> 'CKEditor',
            ),
            'ckeditor_fulltemplate' => array (
              'description' => 'Allow templates to be edited as full HTML pages',
              'type' => 'boolean',
              'value' => '1',
              'allowempty' => false,
              'category'=> 'CKEditor',
            ),
            'ckeditor_fullmessage' => array (
              'description' => 'Allow messages to be edited as full HTML pages',
              'type' => 'boolean',
              'value' => '0',
              'allowempty' => false,
              'category'=> 'CKEditor',
            )
        );

        if ($this->kcEnabled) {
            $this->settings += array(
                'kcfinder_path' => array (
                  'value' =>  PLUGIN_ROOTDIR . self::CODE_DIR . 'kcfinder',
                  'description' => 'Path to KCFinder',
                  'type' => 'text',
                  'allowempty' => 0,
                  'category'=> 'CKEditor',
                ),
                'kcfinder_uploaddir' => array (
                  'value' => '',
                  'description' => 'File system path to the upload image directory. Usually leave this emtpy.',
                  'type' => 'text',
                  'allowempty' => 1,
                  'category'=> 'CKEditor',
                ),
                'kcfinder_image_directory' => array (
                  'value' => 'image',
                  'description' => 'Name of the image subdirectory of the file upload directory',
                  'type' => 'text',
                  'allowempty' => 0,
                  'category'=> 'CKEditor',
                ),
                'kcfinder_files_directory' => array (
                  'value' => 'files',
                  'description' => 'Name of the files subdirectory of the file upload directory',
                  'type' => 'text',
                  'allowempty' => 0,
                  'category'=> 'CKEditor',
                ),
                'kcfinder_flash_directory' => array (
                  'value' => 'flash',
                  'description' => 'Name of the flash subdirectory of the file upload directory',
                  'type' => 'text',
                  'allowempty' => 0,
                  'category'=> 'CKEditor',
                ),
            );
        }
        parent::__construct();
    }

    public function adminmenu()
    {
        return array();
    }

    public function editor($fieldname, $content)
    {
        $width = getConfig('ckeditor_width');
        $height = getConfig('ckeditor_height');
        return $this->createEditor($fieldname, $content, $width, $height);
    }

    public function createEditor($fieldname, $content, $width = null, $height = null, $toolbar = null)
    {
        $fieldname = htmlspecialchars($fieldname);
        $content = htmlspecialchars($content);
        $html = <<<END
<textarea name="$fieldname">$content</textarea>
END;
        $html .= $this->editorScript($fieldname, $width, $height, $toolbar);
        return $html;
    }

    public function createImageBrowser($function)
    {
        static $firstTime = true;

        if ($firstTime) {
            $firstTime = false;
            $html = $this->kcFinderScript($function);
        } else {
            $html = '';
        }
        return $html;
    }
}
