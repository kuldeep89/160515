/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

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
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];

	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';
	
	config.imageBrowser_listUrl			= sub+'/media/image-browser/academy';
	config.extraPlugins 				= 'imagebrowser';
	config.filebrowserBrowseURL			= sub+'/media/';
	config.filebrowserImageBrowseUrl 	= sub+'/assets/plugins/ckfinder/ckfinder.html';
	config.filebrowserUploadUrl			= sub+'/media/file-uploader/academy';
	config.filebrowserImageUploadUrl 	= sub+'/media/image-uploader/academy';
	
	// Make dialogs simpler.
	config.removeDialogTabs = 'image:advanced;link:advanced';
	
	// Plugins
	config.extraPlugins = 'youtube';		// YouTube embedder
	
};