<?php
/**
 * CKEditorPlugin for phplist.
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
 *
 * @author    Duncan Cameron
 * @copyright 2013-2018 Duncan Cameron
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
    public $documentationUrl = 'https://resources.phplist.com/plugin/ckeditor';
    public $enabled = 1;

    /**
     * Work-out the upload directory.
     * The plugin setting kcfinder_uploaddir takes priority over UPLOADIMAGES_DIR.
     *
     * @return array [0] whether the directory exists and is writeable
     *               [1] the directory path
     */
    private function deriveUploadDir()
    {
        $uploadDirIsValid = false;
        $kcUploadDir = getConfig('kcfinder_uploaddir');

        if ($kcUploadDir) {
            if (is_writeable($kcUploadDir)) {
                $uploadDirIsValid = true;
            }
        } else {
            $kcUploadDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . trim(UPLOADIMAGES_DIR, '/');
            $realUploadDir = realpath($kcUploadDir);

            if ($realUploadDir) {
                $kcUploadDir = $realUploadDir;

                if (is_writeable($kcUploadDir)) {
                    $uploadDirIsValid = true;
                }
            }
        }

        return array($uploadDirIsValid, $kcUploadDir);
    }

    /**
     * Generate the script for kcfinder.
     *
     * See http://kcfinder.sunhater.com/docs/integrate Custom Applications
     *
     * @param string $function Name to be used for the callback function
     *
     * @return string the script element
     */
    private function kcFinderScript($function)
    {
        $kcPath = rtrim(getConfig('kcfinder_path'), '/');
        $kcImageDir = getConfig('kcfinder_image_directory');
        $kcUrl = "$kcPath/browse.php?type=$kcImageDir&cms=phplist";
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

    /**
     * Generate the textarea element.
     *
     * @param string $fieldname Name to be used for the textarea element
     * @param string $content   The content for the element
     *
     * @return string the textarea element
     */
    private function textArea($fieldname, $content)
    {
        $fieldname = htmlspecialchars($fieldname);
        $content = htmlspecialchars($content);
        $textArea = <<<END
<textarea name="$fieldname">$content</textarea>
END;

        return $textArea;
    }

    /**
     * Generate a script element wrapping the CKEditor javascript.
     * ckeditor.js is loaded synchronously in a script element.
     *
     * @param string $ckeditorUrl URL for ckeditor.js
     * @param string $ckScript    The CKEditor javascript to be wrapped
     *
     * @return string the script element
     */
    private function scriptForSyncLoad($ckeditorUrl, $ckScript)
    {
        $script = <<<END
<script type="text/javascript" src="$ckeditorUrl"></script>
<script><!--
$ckScript
--></script>
END;

        return $script;
    }

    /**
     * Generate a script element wrapping the CKEditor javascript.
     * ckeditor.js is loaded dynamically by jQuery and asynchronously.
     *
     * @param string $ckeditorUrl URL for ckeditor.js
     * @param string $ckScript    The CKEditor javascript to be wrapped
     *
     * @return string the script element
     */
    private function scriptForAsyncLoad($ckeditorUrl, $ckScript)
    {
        $script = <<<END
<script><!--
jQuery(document).ready(function() {
    options = {
        dataType: "script",
        cache: true,
        url: "$ckeditorUrl"
    };
    jQuery.ajax(options).done(
        function(script, textStatus ) {
            $ckScript
        }
    )
})
--></script>
END;

        return $script;
    }

    /**
     * Generate the javascript to configure CKEditor.
     *
     * @param string $fieldname Name to be used on the textarea field
     * @param int    $width     Width of the editor area
     * @param int    $height    Width of the editor area
     * @param string $toolbar   The toolbar to use
     *
     * @return array [0] the javascript
     *               [1] additional html for warning messages
     */
    private function editorScript($fieldname, $width, $height, $toolbar = null)
    {
        global $website, $public_scheme, $systemroot;

        $html = '';
        $ckeditorUrl = getConfig('ckeditor_url');

        if (substr($ckeditorUrl, -12) != '/ckeditor.js') {
            $html .= sprintf(
                '<div class="note error">CKEditor is not available because the setting for the URL of ckeditor.js "%s" is incorrect.</div>',
                $ckeditorUrl
            );
        }
        $settings = array();
        $settings[] = 'allowedContent: true';

        $fullTemplate = getConfig('ckeditor_fulltemplate');
        $fullMessage = getConfig('ckeditor_fullmessage');

        if ($fieldname == 'template' && $fullTemplate) {
            $settings[] = 'fullPage: true';
        }

        if ($fieldname == 'message' && $fullMessage && !$fullTemplate) {
            $settings[] = 'fullPage: true';
        }
        unset($_SESSION['KCFINDER']);

        if ($this->kcEnabled) {
            list($uploadDirIsValid, $kcUploadDir) = $this->deriveUploadDir();

            if (!extension_loaded('GD')) {
                $html .= <<<END
<div class="note">
Image browsing is not available because the GD extension is not installed.
</div>
END;
            } elseif (!$uploadDirIsValid) {
                $format = <<<END
<div class="note">
Image browsing is not available because directory "%s" does not exist or is not writeable.<br/>
<a href="https://resources.phplist.com/plugin/ckeditor#issues" target="_blank">How to resolve this problem.</a>
</div>
END;
                $html .= sprintf($format, htmlspecialchars($kcUploadDir));
            } else {
                $uploadUrl = sprintf('%s://%s/%s', $public_scheme, $website, ltrim(UPLOADIMAGES_DIR, '/'));

                if (defined('IMAGE_DIR_PER_ADMIN') && IMAGE_DIR_PER_ADMIN) {
                    $directory = $_SESSION['logindetails']['id'];
                    $kcUploadDir .= "/$directory";
                    $uploadUrl .= "/$directory";
                }
                $kcImageDir = getConfig('kcfinder_image_directory');
                $kcFilesDir = getConfig('kcfinder_files_directory');
                $kcFlashDir = getConfig('kcfinder_flash_directory');
                $_SESSION['KCFINDER'] = array(
                    'disabled' => false,
                    'uploadURL' => $uploadUrl,
                    'uploadDir' => $kcUploadDir,
                    'types' => array(
                        $kcFilesDir => '',
                        $kcFlashDir => 'swf',
                        $kcImageDir => '*img',
                    ),
                    'imageDriversPriority' => 'gd',
                );

                $kcPath = rtrim(getConfig('kcfinder_path'), '/');
                $settings[] = <<<END
filebrowserUploadMethod: 'form',
filebrowserBrowseUrl: '$kcPath/browse.php?opener=ckeditor&type=$kcFilesDir&cms=phplist',
filebrowserImageBrowseUrl: '$kcPath/browse.php?opener=ckeditor&type=$kcImageDir&cms=phplist',
filebrowserFlashBrowseUrl: '$kcPath/browse.php?opener=ckeditor&type=$kcFlashDir&cms=phplist',
filebrowserUploadUrl: '$kcPath/upload.php?opener=ckeditor&type=$kcFilesDir&cms=phplist',
filebrowserImageUploadUrl: '$kcPath/upload.php?opener=ckeditor&type=$kcImageDir&cms=phplist',
filebrowserFlashUploadUrl: '$kcPath/upload.php?opener=ckeditor&type=$kcFlashDir&cms=phplist'
END;
            }
        }

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
        $script = <<<END
            CKEDITOR.replace(
                '$fieldname',
                {
                    $configSettings
                }
            );
END;

        return array($script, $html);
    }

    public function __construct()
    {
        $this->kcEnabled = defined('UPLOADIMAGES_DIR') && UPLOADIMAGES_DIR !== false;
        $this->coderoot = dirname(__FILE__) . self::CODE_DIR;
        $this->version = (is_file($f = $this->coderoot . self::VERSION_FILE))
            ? file_get_contents($f)
            : '';
        $this->settings = array(
            'ckeditor_url' => array(
                'value' => '//cdn.ckeditor.com/4.14.1/full/ckeditor.js',
                'description' => 'URL of ckeditor.js',
                'type' => 'text',
                'allowempty' => 0,
                'category' => 'CKEditor',
            ),
            'ckeditor_config_path' => array(
                'value' => '',
                'description' => 'Path to CKeditor custom configuration file',
                'type' => 'text',
                'allowempty' => 1,
                'category' => 'CKEditor',
            ),
            'ckeditor_width' => array(
                'value' => 600,
                'description' => 'Width in px of CKeditor Area',
                'type' => 'integer',
                'allowempty' => 0,
                'min' => 100,
                'max' => 800,
                'category' => 'CKEditor',
            ),
            'ckeditor_height' => array(
                'value' => 600,
                'description' => 'Height in px of CKeditor Area',
                'type' => 'integer',
                'allowempty' => 0,
                'min' => 100,
                'max' => 800,
                'category' => 'CKEditor',
            ),
            'ckeditor_fulltemplate' => array(
                'description' => 'Allow templates to be edited as full HTML pages',
                'type' => 'boolean',
                'value' => '1',
                'allowempty' => true,
                'category' => 'CKEditor',
            ),
            'ckeditor_fullmessage' => array(
                'description' => 'Allow messages to be edited as full HTML pages',
                'type' => 'boolean',
                'value' => '0',
                'allowempty' => true,
                'category' => 'CKEditor',
            ),
        );

        if ($this->kcEnabled) {
            $this->settings += array(
                'kcfinder_path' => array(
                    'value' => PLUGIN_ROOTDIR . self::CODE_DIR . 'kcfinder',
                    'description' => 'Path to KCFinder',
                    'type' => 'text',
                    'allowempty' => 0,
                    'category' => 'CKEditor',
                ),
                'kcfinder_uploaddir' => array(
                    'value' => '',
                    'description' => 'File system path to the upload image directory. Usually leave this empty.',
                    'type' => 'text',
                    'allowempty' => 1,
                    'category' => 'CKEditor',
                ),
                'kcfinder_image_directory' => array(
                    'value' => 'image',
                    'description' => 'Name of the image subdirectory of the file upload directory',
                    'type' => 'text',
                    'allowempty' => 0,
                    'category' => 'CKEditor',
                ),
                'kcfinder_files_directory' => array(
                    'value' => 'files',
                    'description' => 'Name of the files subdirectory of the file upload directory',
                    'type' => 'text',
                    'allowempty' => 0,
                    'category' => 'CKEditor',
                ),
                'kcfinder_flash_directory' => array(
                    'value' => 'flash',
                    'description' => 'Name of the flash subdirectory of the file upload directory',
                    'type' => 'text',
                    'allowempty' => 0,
                    'category' => 'CKEditor',
                ),
            );
        }
        parent::__construct();
    }

    public function dependencyCheck()
    {
        global $editorplugin;

        return array(
            'No other editor enabled' => empty($editorplugin) || $editorplugin == __CLASS__,
            'phpList version 3.5.5 or later' => version_compare(VERSION, '3.5.5-RC1') >= 0,
        );
    }

    public function adminmenu()
    {
        return array();
    }

    /**
     * Hook called to generate the editor html.
     * That includes the input textarea field and the ckeditor javascript.
     *
     * This method loads ckeditor.js in a script element.
     *
     * @param string $fieldname Name to be used on the textarea field
     * @param string $content   The content to be displayed
     *
     * @return string the complete html and script to display the editor
     */
    public function editor($fieldname, $content)
    {
        $width = getConfig('ckeditor_width');
        $height = getConfig('ckeditor_height');
        list($ckScript, $html) = $this->editorScript($fieldname, $width, $height);

        $ckeditorUrl = getConfig('ckeditor_url');

        return $this->textArea($fieldname, $content)
            . $html
            . $this->scriptForSyncLoad($ckeditorUrl, $ckScript);
    }

    /**
     * Alternative method to generate the html for the editor.
     * This method loads ckeditor.js asynchronously to allow use of its CDN when
     * the requesting page is being loaded by jquery (in Content Areas plugin).
     *
     * @param string $fieldname Name to be used on the textarea field
     * @param string $content   The content to be displayed
     * @param int    $width     Width of the editor area
     * @param int    $height    Width of the editor area
     * @param string $toolbar   The toolbar to use
     *
     * @return string the complete html and script to display the editor
     */
    public function createEditor($fieldname, $content, $width = null, $height = null, $toolbar = null)
    {
        list($ckScript, $html) = $this->editorScript($fieldname, $width, $height, $toolbar);

        $ckeditorUrl = getConfig('ckeditor_url');
        $host = parse_url($ckeditorUrl, PHP_URL_HOST);

        $script = ($host == null || $host == $_SERVER['HTTP_HOST'])
            ? $this->scriptForSyncLoad($ckeditorUrl, $ckScript)
            : $this->scriptForAsyncLoad($ckeditorUrl, $ckScript);

        return $this->textArea($fieldname, $content)
            . $html
            . $script;
    }

    public function createImageBrowser($function)
    {
        return $this->kcFinderScript($function);
    }
}
