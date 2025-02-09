<?php

if (!defined('PHPLISTINIT')) {
    die('Access denied');
}

class CKEditorPlugin extends phplistPlugin
{
    const VERSION_FILE = 'version.txt';
    const CODE_DIR = '/CKEditorPlugin/';
    const CDN =  '//cdn.ckeditor.com/ckeditor5/34.0.0/classic/ckeditor.js';

    /*
     *  Inherited variables
     */
    public $name = 'CKEditor plugin';
    public $editorProvider = true;
    public $authors = 'Duncan Cameron';
    public $description = 'Provides the CKEditor for editing messages and templates.';
    public $documentationUrl = 'https://resources.phplist.com/plugin/ckeditor';
    public $enabled = 1;

    public function editor($fieldName, $content): string
    {
        $width = getConfig('ckeditor_width') ?? 900;
        $height = getConfig('ckeditor_height') ?? 450;
        $licenseKey = getConfig('ckeditor_license_key');
        $licenseKeyScript = "licenseKey: '$licenseKey'";
        $editorUrl = getConfig('ckeditor_url') ? getConfig('ckeditor_url') : self::CDN;

        $script = $this->editorScript($fieldName, $width, $height, $licenseKeyScript, $editorUrl);
        $fieldName = htmlspecialchars($fieldName);
        $content = htmlspecialchars($content);

        return $this->textArea($fieldName, $content)
            . $this->scriptForSyncLoad($editorUrl, $script);
    }

    private function scriptForSyncLoad(string $editorUrl, $ckScript): string
    {
        return <<<END
<script type="text/javascript" src="$editorUrl"></script>
<script>
$ckScript
</script>
END;
    }

    private function textArea(string $fieldName, string $content): string
    {
        return "<textarea id=\"$fieldName\" name=\"$fieldName\">$content</textarea>";
    }

    private function editorScript(string $fieldName, $width, $height, $licenseKeyScript, $editorUrl): string
    {
        $pluginUrl = substr(PLUGIN_ROOTDIR, 0, 1) == '/' ? PLUGIN_ROOTDIR : $GLOBALS['pageroot'] . '/admin/' . PLUGIN_ROOTDIR;

        $script = <<<END
<script src="$editorUrl"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    ClassicEditor
        .create(document.querySelector('textarea#$fieldName'), {
            $licenseKeyScript,
            toolbar: [
                'heading', 'undo', 'redo', '|', 
                'bold', 'italic',  'bulletedList', 'numberedList', '|',
                'imageUpload', 'link', 'mediaEmbed',  '|',
                'blockQuote', 'insertTable'
            ]
        })
        .then(editor => {
            editor.ui.view.toolbar.element.addEventListener("click", (event) => {
                if (event.target.accept && event.target.accept.includes("image")) {
                    openElFinder(editor, 'image');
                }
            });

            function openElFinder(editor, fileType) {
                const fileManager = window.open(
                    '$pluginUrl/CKEditorPlugin/elFinder/elfinder.html',
                    'File Manager',
                    'width=$width,height=$height'
                );

                fileManager.addEventListener('message', function handleFile(event) {
                    if (event.origin !== window.location.origin) return;

                    const data = event.data;
                    if (data.mceAction === 'fileSelected') {
                        const fileUrl = new URL(data.data.url, window.location.origin).href;

                        if (fileType === 'image') {
                            editor.model.change(writer => {
                                const imageElement = writer.createElement('imageBlock', {
                                    src: fileUrl,
                                    alt: data.data.name
                                });
                                editor.model.insertContent(imageElement, editor.model.document.selection);
                            });
                        } else if (fileType === 'media') {
                            editor.model.change(writer => {
                                const mediaElement = writer.createElement('mediaEmbed', {
                                    url: fileUrl
                                });
                                editor.model.insertContent(mediaElement, editor.model.document.selection);
                            });
                        }

                        fileManager.close();
                        window.removeEventListener('message', handleFile);
                    }
                });
            }
        })
        .catch(error => {
            console.error('CKEditor initialization failed:', error);
        });
});
</script>


END;
        return $script;
    }

    public function adminMenu()
    {
        return array(
            "ckeditor_settings" => "Ckedotor Settings",
        );
    }

    public function display($action)
    {
        switch ($action) {
            case "ckeditor_settings":
                echo '<h1>Ckedotor Configuration</h1>';
                echo '<p>Configure your Ckedotor integration here.</p>';
                break;
        }
    }
}
