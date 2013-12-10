/**
 *
 * define a set of styles that will appear in the Styles drop-down
 * The 
 */
CKEDITOR.stylesSet.add( 'ECS', [
    // Block-level styles
    { name: 'Title',  element: 'h2',
    styles: { 'margin-bottom': '0.5em', 'font-size': '1.6em', 'color': 'rgb(0, 36, 75)', 'font-family': 'Georgia,Times New Roman,Times,serif'}
    },
    { name: 'ArticleTitle',  element: 'h3',
    styles: { 'margin-bottom': '0.5em', 'font-size': '1.4em', 'color': 'rgb(0, 36, 75)', 'font-family': 'Georgia,Times New Roman,Times,serif'}
    },
    { name: 'ArticleSubTitle',  element: 'h4',
    styles: { 'margin-bottom': '0.5em', 'font-size': '1.2em', 'color': 'rgb(0, 36, 75)', 'font-family': 'Georgia,Times New Roman,Times,serif'}
    },
    { name: 'BodyPara',  element: 'p',
    styles: { 'margin-bottom': '0.5em', 'font-size': '1.1em', 'color': 'rgb(102, 102, 102)', 'font-family': 'Tahoma,Verdana,Arial,sans-serif'}
    },
    { name: 'BodyParaSmall',  element: 'p',
    styles: { 'margin-bottom': '0.5em', 'font-size': '1em', 'color': 'rgb(102, 102, 102)', 'font-family': 'Tahoma,Verdana,Arial,sans-serif'}
    },
    // Inline styles
    { name: 'CSS Style', element: 'span', attributes: { 'class': 'my_style' } },
    { name: 'Marker: Yellow', element: 'span', styles: { 'background-color': 'Yellow' } }
]);


CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];
    // Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	config.removeButtons = 'Subscript,Superscript';

	// Se the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Make dialogs simpler.
//	config.removeDialogTabs = 'image:advanced;link:advanced';
//  enable custom styles
    config.stylesSet = 'ECS';
    config.filebrowserWindowWidth = '600';
    config.filebrowserWindowHeight = '600';
    config.image_previewText = CKEDITOR.tools.repeat( '___ ', 100 );
    config.resize_enabled = true;
    config.resize_minWidth = 500;
    config.resize_dir = 'both';
    config.allowedContent = true;
};
