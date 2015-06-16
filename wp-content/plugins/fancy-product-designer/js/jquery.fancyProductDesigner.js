/*
 * Fancy Product Designer v2.0.6
 *
 * Copyright 2013, Rafael Dery
 *
 */

;(function($) {

	var FancyProductDesigner = function( elem, args) {

		var options = $.extend({}, $.fn.fancyProductDesigner.defaults, args);
		options.labels = $.extend({}, $.fn.fancyProductDesigner.defaults.labels, options.labels);
		options.labels.modificationTooltips = $.extend({}, $.fn.fancyProductDesigner.defaults.labels.modificationTooltips, options.labels.modificationTooltips);
		options.labels.colorpicker = $.extend({}, $.fn.fancyProductDesigner.defaults.labels.colorpicker, options.labels.colorpicker);
		options.customImagesParameters = options.uploadedDesignsParameters == undefined ? options.customImagesParameters : options.uploadedDesignsParameters;
		options.customImagesParameters = $.extend({}, $.fn.fancyProductDesigner.defaults.customImagesParameters, options.customImagesParameters);
		options.dimensions = $.extend({}, $.fn.fancyProductDesigner.defaults.dimensions, options.dimensions);

		var thisClass = this,
			stage,
			$elem,
			$sidebar,
			$sidebarNavi,
			$sidebarContent,
			$productContainer,
			$menubar,
			$toolbar,
			$toolbarButtons,
			$colorPicker,
			$fontsDropdown,
			$patternWrapper,
			$modificationTooltip,
			$products,
			$designs,
			$viewSelection,
			$editorBox,
			$productLoader,
			$designCategories = null,
			currentBoundingObject = null,
			viewsLength = 0,
			currentProductIndex = -1,
			currentProductTitle = null,
			currentViewIndex = 0,
			currentViews = null,
			currentElement = null,
			currentPrice = 0,
			designCategories = {},
			rotateIcon = new Image(),
			resizeIcon = new Image();

		//set data url rotate icon
		rotateIcon.src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBNYWNpbnRvc2giIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QkNGMzVGRDE0NENBMTFFMzlDNTlBQzY1MzlBMkNBNzMiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QkNGMzVGRDI0NENBMTFFMzlDNTlBQzY1MzlBMkNBNzMiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpCQ0YzNUZDRjQ0Q0ExMUUzOUM1OUFDNjUzOUEyQ0E3MyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpCQ0YzNUZEMDQ0Q0ExMUUzOUM1OUFDNjUzOUEyQ0E3MyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pj80kqoAAAHdSURBVHjalFO7agJREB2Xa6Hio1CIEbSIKIidptJOENJYCraBlGnyCX5BiF8gCUEhWClYhQULIaRUFlQEX4VKCp/gczJ3yW5iYsLmwHL3vs7MnDmX1Wo1IJyYTKYrvV5/hgT4AzqdTtjtdm+LxeJxv9+/AhE4x+PxC03wP5jP52NJkqLMaDRe2u328++RVqsV9Ho9oGhgsVjA6XQe7FPGdqvVegPdbjfzlXk6nWI6nUa/349UEgqCgDabDePxOJbLZflMoVDAVquFk8lEhE6nc6tcpn8MhUJcg1+/ZDKJZrMZ6/U6L+NZJaCUMRKJqAcZYxiLxTCVSqHP5/tB1Gw2cTabfRJks1l10+12Y6VSUcvabreYSCQOCBqNhkzAqC2yKLlcTmkTZDIZiEajqmClUgmq1SqQHvJcGTkYn1ALod1uywsulwtIsAPFA4EAiKKoXuRBPB4PkODAjhgFlKwUeL3eo6binhN4dM5MdcuLg8EAqF2gFYLiXGqPPHLCfD6vmYB34Y4rvVwuMRgMYjgcxuFwqMnOo9FIZDTOOJHBYIBisQhkbXA4HJqC87gCvaoH6ueQL3BltV7ebDZAVr7nGUj9fv+CHsY1ue/0wyh/vmjSaU5Bc+v1+uldgAEAQbiBjYjV1d0AAAAASUVORK5CYII=';

		//set data url resize icon
		resizeIcon.src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBNYWNpbnRvc2giIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RkY3RjhCMTI0NENEMTFFMzlDNTlBQzY1MzlBMkNBNzMiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RkY3RjhCMTM0NENEMTFFMzlDNTlBQzY1MzlBMkNBNzMiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpCQ0YzNUZENzQ0Q0ExMUUzOUM1OUFDNjUzOUEyQ0E3MyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpCQ0YzNUZEODQ0Q0ExMUUzOUM1OUFDNjUzOUEyQ0E3MyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pj7soO0AAAG9SURBVHjalFM9awJBEJ07TwstlPMKL4WFVlEsU5lGEMHKQizSSSBlmoA/wH+QP2AlhBSptLESBCtJIyqCH6BgETwFNeeBH+dmdskdhiR3yYMtdnf2zbw3s1y32wVEwOPx3DmdzjBBgAU4juN1XV9ut9un0+n0CkggK4rSIv8APiSqqir9fv9acLvdt5IkXU0mE2g0GsDzPOz3e/D5fJDJZEAQhJ+qAKxYwpgHvBcC9LDZbEI+n2cBsixDuVwGh8NhpYaSizxWpNONy+UyL7AiiEajLJMVqBre2GiaBsFgENLpNHQ6HYjH49But8EOJoEoilAqlaBarUIymYTpdArL5dKWgAY+Gs4aWCwWpNWyb8x8Pq8L584a8Pv9bJ0DE7AO/SrBCrVaDRKJBIxGI7Yfj8es1SypIcFqaHK5HJ1OEolESKVSIalUiux2O4Ie1W0JKFarFclms4yErnCYTTwj4P8iw+v1QqFQMAcL/4JpgYBE73YEw+EQisUixGIxOjwQCoUMYzXo9XqXm83mzUrC4XAgx+PxyxmaSAaDwQ2toD+bzdJY5j3O9sWnzm+f56zNHGZW1+v1M5K8fAgwAKZZlVqllD6aAAAAAElFTkSuQmCC';


		$elem = $(elem).addClass('fpd-container fpd-clearfix fpd-'+options.layout+'').before('<p class="fpd-initiliazing">'+options.labels.initText+'</p>');
		$products = $elem.children('.fpd-product').remove();
		$designs = $elem.children('.fpd-design');

		if(options.layout == 'icon-sb-top' || options.layout == 'icon-sb-bottom') {
			$elem.addClass('fpd-horizontal');
		}

		//test if canvas is supported
		var canvasTest = document.createElement('canvas'),
			canvasIsSupported = Boolean(canvasTest.getContext && canvasTest.getContext('2d'));

		if(!canvasIsSupported) {
			$.post(options.templatesDirectory+'canvaserror.php', function(html) {
				$elem.append($.parseHTML(html));

				$elem.trigger('templateLoad', [this.url]);
			});
			$elem.trigger('canvasFail');
			return false;
		}

		//execute this because of a ff issue with localstorage
		window.localStorage.length;
		//window.localStorage.clear();


		//----------------------------------
		// ------- LOAD TEMPLATES ----------
		//----------------------------------

		var sidebarHtml;

		//load sidebar html
		$.post(options.templatesDirectory+'sidebar.php',
			function(html){

				sidebarHtml = html;
				$elem.trigger('templateLoad', [this.url]);

				//load product stage html
				$.post(options.templatesDirectory+'productstage.php',
					function(html){

						$elem.prev('.fpd-initiliazing').remove();

						if(options.layout == 'icon-sb-left' || options.layout == 'icon-sb-top') {
							$elem.append($.parseHTML(sidebarHtml)).append($.parseHTML(html));
						}
						else {
							$elem.append($.parseHTML(html)).append($.parseHTML(sidebarHtml));
						}

						$elem.fadeIn(500);

						$sidebar = $elem.children('.fpd-sidebar');
						$sidebarNavi = $sidebar.children('.fpd-navigation');
						$sidebarContent = $sidebar.children('.fpd-content');
						$toolbar = $sidebarContent.find('.fpd-toolbar');
						$toolbarButtons = $toolbar.children('.fpd-element-buttons');
						$colorPicker = $toolbar.children('.fpd-color-picker');
						$patternWrapper = $toolbar.children('.fpd-patterns-wrapper');

						if($elem.hasClass('fpd-horizontal')) {
							$sidebar.css('width', options.dimensions.sidebarHeight);
							$sidebarNavi.find('li').css('line-height', options.dimensions.sidebarNavWidth + 'px');
							$sidebarNavi.css('height', options.dimensions.sidebarNavWidth);
						}
						else {
							$sidebar.css('height', options.dimensions.sidebarHeight);
							$sidebarNavi.css('width', options.dimensions.sidebarNavWidth);
							$sidebarContent.css('width', options.dimensions.sidebarContentWidth);
						}

						$productContainer = $elem.children('.fpd-product-container')
						.css({width: options.dimensions.productStageWidth});

						$productContainer.children('.fpd-product-stage').height(options.dimensions.productStageHeight);
						$menubar = $productContainer.children('.fpd-menu-bar');
						$productLoader = $productContainer.append('<div class="fpd-product-loader"><div class="fpd-loading-gif"></div></div>').children('.fpd-product-loader');

						$elem.trigger('templateLoad', [this.url]);

						setTimeout(_init, 1000);

					}
				);
			}
		);


		//----------------------------------
		// ------- PRIVATE METHODS ---------
		//----------------------------------

		var _init = function() {
			//create fabric stage
			var canvas = $productContainer.children('.fpd-product-stage').children('canvas').get(0);
			stage = new fabric.Canvas(canvas, {
				selection: false,
				hoverCursor: 'pointer',
				rotationCursor: 'default'
			});
			stage.setDimensions({width: options.dimensions.productStageWidth, height: options.dimensions.productStageHeight})

			//modification tooltip
			$modificationTooltip = $productContainer.append('<div class="fpd-modification-tooltip"></div>').children('.fpd-modification-tooltip')
			.tooltipster({offsetY: -3, theme: '.fpd-tooltip-theme', delay: 0, speed: 0, touchDevices: false});

			//attach handlers to stage
			stage.on({
				'mouse:down': function(opts) {
					if(opts.target == undefined) {
						_deselectElement();
					}
				},
				'text:changed': function(opts) {
					currentElement.params.text = opts.target.text;

					//check containment
					if(_outOfContainment(currentElement)) {
						_updateTooltip();
					}
					else {
						$modificationTooltip.tooltipster('hide');
					}
				},
				'object:moving': function(opts) {
					currentElement.params.x = Math.round(opts.target.left);
					currentElement.params.y = Math.round(opts.target.top);
					_outOfContainment(opts.target);
					if(!currentElement.lockMovementX) {
						_updateTooltip(options.labels.modificationTooltips.x+Math.round(opts.target.left) + options.labels.modificationTooltips.y+Math.round(opts.target.top), 'moved');
						_updateEditorBox(currentElement);
					}
				},
				'object:scaling': function(opts) {
					currentElement.params.scale = Number(opts.target.scaleX).toFixed(2);
					currentElement.params.x = Math.round(opts.target.left);
					currentElement.params.y = Math.round(opts.target.top);
					opts.target.setCoords();
					_outOfContainment(opts.target);
					if(!currentElement.lockScalingX) {
						_updateTooltip(options.labels.modificationTooltips.width+Math.round(currentElement.getWidth()) + options.labels.modificationTooltips.height+Math.round(currentElement.getHeight()), 'scaled');
						_updateEditorBox(currentElement);
					}
				},
				'object:rotating': function(opts) {
					currentElement.params.degree = Math.round(opts.target.angle);
					_outOfContainment(opts.target);
					if(!currentElement.lockRotation) {
						_updateTooltip(options.labels.modificationTooltips.angle+Math.round(opts.target.angle) % 360, 'rotated');
						_updateEditorBox(currentElement);
					}
				},
				'object:selected': function(opts) {
					_deselectElement(false);

					currentElement = opts.target;
					var elemParams = currentElement.params;

					_updateEditorBox(currentElement);

					currentElement.set({
						borderColor: 'white',
						cornerColor: 'transparent',
						cornerSize: 16,
						rotatingPointOffset: 0,
						padding: 7
					});

					$sidebarContent.find('.fpd-elements-dropdown').children('option[value="'+currentElement.id+'"]').prop('selected', true).parent().trigger('chosen:updated');;

					//toggle colorpicker
					if(Array.isArray(elemParams.colors) && _elementIsColorizable(currentElement) != false) {
						$colorPicker.children('input').val(elemParams.currentColor ? elemParams.currentColor : elemParams.colors[0]);
						//color list

						if(elemParams.colors.length > 1) {
							$colorPicker.children('input').spectrum({
								preferredFormat: "hex",
								showPaletteOnly: true,
								showPalette: true,
								palette: elemParams.colors,
								chooseText: options.labels.colorpicker.change,
								cancelText: options.labels.colorpicker.cancel,
								change: function(color) {
									_changeColor(currentElement, color.toHexString());
								}
							});
						}
						//palette
						else {
							$colorPicker.children('input').spectrum("destroy").spectrum({
								preferredFormat: "hex",
								showInput: true,
								chooseText: options.labels.colorpicker.change,
								cancelText: options.labels.colorpicker.cancel,
								change: function(color) {
									_changeColor(currentElement, color.toHexString());
								}
							});
						}
						$colorPicker.show();
					}
					else {
						$colorPicker.hide();
					}


					//toggle fonts dropdown
					if(currentElement.type == "i-text") {
						$fontsDropdown.children('option[value="'+elemParams.font+'"]').prop('selected', 'selected').trigger('chosen:updated');
						$fontsDropdown.parent().show();
						$toolbar.children('.fpd-customize-text').show();

						if(elemParams.patternable) {
							$patternWrapper.show();
						}
					}

					//toggle center buttons
					elemParams.draggable ? $toolbarButtons.children('.fpd-center-horizontal, .fpd-center-vertical').show() : $toolbarButtons.children('.fpd-center-horizontal, .fpd-center-vertical').hide();

					//check if z-position is changeable
					elemParams.zChangeable ? $toolbarButtons.children('.fpd-move-down, .fpd-move-up').show() : $toolbarButtons.children('.fpd-move-down, .fpd-move-up').hide();

					//check if it can be removed
					elemParams.removable ? $toolbarButtons.children('.fpd-trash').show() : $toolbarButtons.children('.fpd-trash').hide();

					//check for a boundingbox

					if(elemParams.boundingBox && !options.editorMode) {
						//custom boundingbox
						if(typeof elemParams.boundingBox == "object") {
							currentBoundingObject = new fabric.Rect({
								left: elemParams.boundingBox.x,
								top: elemParams.boundingBox.y,
								width: elemParams.boundingBox.width,
								height: elemParams.boundingBox.height,
								stroke: 'blue',
								strokeWidth: 1,
								fill: false,
								selectable: false,
								isBoundingRect: true,
								originX: 'center',
								originY: 'center'
							});
							stage.add(currentBoundingObject);
							currentBoundingObject.moveTo(stage.getObjects().indexOf(currentElement));
						}
						//boundingbox by another element
						else {
							var objects = stage.getObjects();
							for(var i=0; i < objects.length; ++i) {
								//get all layers from first view
								var object = objects[i];
								if(object.viewIndex == currentViewIndex) {

									if(elemParams.boundingBox == object.title && currentBoundingObject == null) {
										currentBoundingObject = object;
										object.stroke = 'blue';
										object.strokeWidth = 1;
										break;
									}
								}
							}
						}
					}

					stage.renderAll();

					$toolbar.show();
					$sidebarNavi.find('li[data-target=".fpd-edit-elements"]').click();
					_outOfContainment(currentElement);

					$elem.trigger('elementSelect', [currentElement]);
				}
			});

			//create view array from DOM
			var views = [];

			for(var i=0; i < $products.length; ++i) {
				//get other views
				views = $($products.get(i)).children('.fpd-product');
				//get first view
				views.splice(0,0,$products.get(i));

				var viewsArr = [];
				views.each(function(i, view) {
					var $view = $(view);
					var viewObj = {title: view.title, thumbnail: $view.data('thumbnail'), elements: []};

					$view.children('img,span').each(function(j, element) {
						var $element = $(element);
						var elementObj = {
							type: $element.is('img') ? 'image' : 'text', //type
							source: $element.is('img') ? $element.attr('src') : $element.text(), //source
							title: $element.attr('title'),  //title
							parameters: $element.data('parameters') == undefined ? {} : $element.data('parameters')  //parameters
						};
						viewObj.elements.push(elementObj);
					});

					viewsArr.push(viewObj);
				});
				thisClass.addProduct(viewsArr);
			}

			//load all designs
			if($designs.size() > 0) {
				//check if categories are used
				if($designs.children('.fpd-category').length > 0) {

					//create design chosen dropdown
					$designCategories = $sidebarContent.find('.fpd-designs').prepend('<select class="fpd-design-categories" style="width: 100%;" tabindex="1"></select>')
					.children('.fpd-design-categories').change(function() {

						//get designs from selected category
						var designsInCat = designCategories[this.value];

						//empty list
						$sidebarContent.find('.fpd-designs ul').empty();

						//add designs from category to list
						for(var i=0; i < designsInCat.length; ++i) {
							_addDesignToList(designsInCat[i]);
						}

					})

					//browse through all categories
					$designs.children('.fpd-category').each(function(i, cat) {

						$(cat).children('img').each(function(j, design) {
							var $design = $(design);
							thisClass.addDesign($design.attr('src'), $design.attr('title'), $design.data('parameters'), cat.title);
						});

					});

					if(options._useChosen) {
						$designCategories.chosen({width: '100%'});
					}

					$designCategories.change();

				}
				else {
					//append designs to list
					var $designImgs = $designs.children('img');
					for(var i=0; i < $designImgs.length; ++i) {
						thisClass.addDesign($($designImgs[i]).attr('src'), $designImgs[i].title, $($designImgs[i]).data('parameters'));
					}
				}

				$designs.remove();
			}

			//show edit elements navi
			$sidebarNavi.find('li[data-target=".fpd-edit-elements"]').show();

			//check if custom text is supported
			if(options.customTexts) {
				var $customText = $sidebarContent.children('.fpd-custom-text'),
					placeholder = $customText.children('textarea').val();

				$sidebarNavi.find('li[data-target=".fpd-custom-text"]').show();
				$customText.children('.fpd-button-submit').click(function(evt) {
					evt.preventDefault();
					var text = $customText.children('textarea').val();
					thisClass.addElement('text', text, text, options.customTextParameters, currentViewIndex);
					$customText.children('textarea').removeClass('fpd-active').val(placeholder);
				});

				$customText.children('textarea').focusin(function() {
					var $this = $(this);
					if(this.value == placeholder) {
						$this.addClass('fpd-active');
						this.value = '';
					}
				}).focusout(function() {
					var $this = $(this);
					if(this.value == '') {
						this.value = placeholder;
						$this.removeClass('fpd-active');
					}
				});
			}

			//check if upload designs is supported
			if(options.uploadDesigns) {
				var $uploadDesigns = $sidebarContent.children('.fpd-upload-designs');
				$sidebarNavi.find('li[data-target=".fpd-upload-designs"]').show();

				//trigger click on input upload
				$uploadDesigns.children('.fpd-button-submit').click(function(evt) {
					evt.preventDefault();
					$uploadDesigns.find('.fpd-input-design').click();
				});

				//listen when input upload changes
				$uploadDesigns.find('.fpd-upload-form').change(function(evt) {
					if(window.FileReader) {
						var reader = new FileReader();
						var designTitle = evt.target.files[0].name;
				    	reader.readAsDataURL(evt.target.files[0]);

				    	reader.onload = function (evt) {

				    		var image = new Image;
				    		image.src = evt.target.result;

				    		image.onload = function() {

				    			var imageH = this.height,
				    				imageW = this.width,
				    				scaling = 1;

				    			if(imageW > options.customImagesParameters.maxW ||
				    			imageW < options.customImagesParameters.minW ||
				    			imageH > options.customImagesParameters.maxH ||
				    			imageH < options.customImagesParameters.minH) {
					    			alert(options.labels.uploadedDesignSizeAlert);
					    			return false;
				    			}

								//check if its too big
								options.customImagesParameters.scale = _getScalingByDimesions(imageW, imageH, options.customImagesParameters.resizeToW, options.customImagesParameters.resizeToH);

					    		thisClass.addElement(
					    			'image',
					    			evt.target.result,
					    			designTitle,
						    		options.customImagesParameters
					    		);

				    		}

				    		image.onerror = function(evt) {
				    			alert('Image could not be loaded!');
				    		}

						}
					}
					else {
						alert('FileReader API is not supported in your browser, please use Firefox, Safari, Chrome or IE10!')
					}

				});
			}

			//check if user can add photos from facebook
			if(options.facebookAppId && options.facebookAppId.length > 0) {

				var $fbUserPhotos = $sidebarContent.children('.fpd-fb-user-photos'),
					$fbSelects = $fbUserPhotos.find('select'),
					$fbUserPhotosList = $fbUserPhotos.find('.fpd-fb-user-photos-list'),
					$fbLoaderGif = $fbUserPhotos.find('.fpd-loading-gif');

				$sidebarNavi.find('li[data-target=".fpd-fb-user-photos"]').show();

				if(options._useChosen) {
					$fbSelects.chosen({width: '100%'});
				}

				//friend changed
				var $friendsSelect = $fbUserPhotos.find('.fpd-fb-friends-select').change(function() {

					$fbLoaderGif.show();

					var friendId = $(this).children('option:selected').val();

					$fbUserPhotosList.empty().css('visibility', 'hidden');
					//remove all options instead of first
					$fbAlbumsSelect.children('option:not(:first)').remove();

					//get albums from fb user
					FB.api('/'+friendId+'/albums', function(response) {

						var albums = response.data;
						//add all albums to select
						for(var i=0; i < albums.length; ++i) {
							var album = albums[i];
							$fbAlbumsSelect.append('<option value="'+album.id+'">'+album.name+'</option>');
						}

						$fbAlbumsSelect.trigger('chosen:updated').next('.chosen-container').show();
						$fbLoaderGif.hide();

					});

				});

				var $fbAlbumsSelect = $fbUserPhotos.find('.fpd-fb-user-albums').change(function() {

					$fbLoaderGif.show();

					var albumId = $(this).children('option:selected').val();

					$fbUserPhotosList.css('visibility', 'hidden').empty();

					//get photos from fb album
					FB.api('/'+albumId, function(response) {

						var albumCount = response.count;

						FB.api('/'+albumId+'?fields=photos.limit('+albumCount+')', function(response) {

							if(!response.error) {
								var photos = response.photos.data;

								for(var i=0; i < photos.length; ++i) {
									var photo = photos[i],
										photoImg = photo.images[photo.images.length-1] ? photo.images[photo.images.length-1].source : photo.source;

									$fbUserPhotosList.append('<li><span class="fpd-loading-gif"></span><img src="'+photoImg+'" title="'+photo.id+'" data-picture="'+photo.source+'" style="display: none;" /></li>')
									.children('li:last').click(function(evt) {

										evt.preventDefault();
										$productLoader.show();

										var $img = $(this).children('img');

										$.post(options.phpDirectory + 'get_image_data_url.php', { url: $img.data('picture') }, function(data) {

											if(data && data.error == undefined) {

												var picture = new Image();
												picture.src = data.data_url;
												picture.onload = function() {

													options.customImagesParameters.scale = _getScalingByDimesions(
														this.width,
														this.height,
														options.customImagesParameters.resizeToW,
														options.customImagesParameters.resizeToH
													);

													thisClass.addElement('image', this.src, $img.attr('title'), options.customImagesParameters, currentViewIndex);
												};

											}
											else {
												alert(data.error);
											}

											$productLoader.hide();

										}, 'json')
										.fail(function(evt) {

											$productLoader.hide();
											alert(evt.statusText);

										});



									})
									.children('img').load(function() {

										//fade in photo and remove loading gif
										$(this).fadeIn(500).prev('span').fadeOut(300, function() {
											$(this).remove();
										});

									})
									.error(function() {
										//image not found, remove associated list item
										$(this).parent().remove();
									});
								}

								_createScrollbar($fbUserPhotosList);
								$fbUserPhotosList.css('visibility', 'visible');
							}

							$fbLoaderGif.hide();

						});
					});
				});

				$.ajaxSetup({ cache: true });
				$.getScript('//connect.facebook.net/en_UK/all.js', function(){

					//init facebook
					FB.init({
						appId: options.facebookAppId,
						status: true,
						cookie: true,
						xfbml: true
					});

					FB.Event.subscribe('auth.statusChange', function(response) {

						if (response.status === 'connected') {
							// the user is logged in and has authenticated your

							$fbLoaderGif.show();
							FB.api('/me', function(response) {

								//add me
								$friendsSelect.append('<option value="'+response.id+'">'+response.name+'</option>');

								//get my friends
								FB.api('/me/friends', function(response) {
									var myFriends = response.data.sort(_sortByName);
									for(var i=0; i < myFriends.length; ++i) {
										var friend = myFriends[i];
										$friendsSelect.append('<option value="'+friend.id+'">'+friend.name+'</option>');
									}
									$friendsSelect.trigger('chosen:updated').next('.chosen-container').show();
								});

								$fbLoaderGif.hide();
							});

						}
						else {
							// the user isn't logged in to Facebook.
							$fbUserPhotosList.empty().css('visibility', 'hidden');
							$fbSelects.children('option:not(:first)').remove();
							$fbSelects.trigger('chosen:updated').next('.chosen-container').hide();
						}

					});

				});
			}

			//allow user to save products in a list
			if(options.allowProductSaving && $elem.attr('id')) {

				$sidebarNavi.find('li[data-target=".fpd-saved-products"]').show();

				$menubar.find('.fpd-save-product').click(function(evt) {
					evt.preventDefault();
					_deselectElement();

					//get key and value
					var product = thisClass.getProduct(false),
						thumbnail = thisClass.getViewsDataURL()[0];

					//check if there is an existing products array
					var savedProducts = _getSavedProducts();
					if(savedProducts == null) {
						//create new
						savedProducts = new Array();
					}

					savedProducts.push({thumbnail: thumbnail, product: product});
					window.localStorage.setItem($elem.attr('id'), JSON.stringify(savedProducts));

					_addSavedProduct(thumbnail, product);

				});

				//load all saved products into list
				var savedProducts = _getSavedProducts();
				if(savedProducts != null) {
					for(var i=0; i < savedProducts.length; ++i) {
						var savedProduct = savedProducts[i];
						_addSavedProduct(savedProduct.thumbnail, savedProduct.product);
					}
				}


			}
			else {
				$menubar.find('.fpd-save-product').parent().hide();
			}

			//check if jsPDF is included
			if(options.saveAsPdf && window.jsPDF) {

				$menubar.find('.fpd-save-pdf').click(function(evt) {
					evt.preventDefault();
					_deselectElement();

					var doc = new jsPDF(),
						viewsDataURL = thisClass.getViewsDataURL('jpeg', 'white');

					for(var i=0; i < viewsDataURL.length; ++i) {
						doc.addImage(viewsDataURL[i], 'JPEG', 0, 0);
						if(i < viewsDataURL.length-1) {
							doc.addPage();
						}
					}

					doc.save('Product.pdf');

				});
			}
			else {
				$menubar.find('.fpd-save-pdf').parent().hide();
			}

			//click handler for the tabs navigation
			$sidebarNavi.find('ul li').click(function(evt) {
				evt.preventDefault();
				var $this = $(this);
				if(!$this.hasClass('fpd-content-color')) {

					$this.parent().children('li').removeClass('fpd-content-color');
					$this.addClass('fpd-content-color');

					$sidebarContent.children('div').hide().children('ul').getNiceScroll().remove();
					var $currentContentBox = $sidebarContent.children($this.data('target')).show();

					if($currentContentBox.children('ul').size() > 0) {
						_createScrollbar($currentContentBox.children('ul'));
					}

				}

			}).filter(':visible:first').click();

			//set active object
			$sidebarContent.find('.fpd-elements-dropdown').change(function() {

				if(this.value == 'none') {
					_deselectElement();
				}
				else {
					var objects = stage.getObjects();
					for(var i=0; i < objects.length; ++i) {
						if(objects[i].id == this.value) {
							stage.setActiveObject(objects[i]);
							break;
						}
					}
				}
			});

			if(options.fonts.length > 0 && options.fontDropdown) {

				//change font family when dropdown changes
				$fontsDropdown = $toolbar.find('.fpd-fonts-dropdown').change(function() {
					var fallbackFontWidth = currentElement.getWidth();
					currentElement.setFontFamily(this.value);
					currentElement.params.font = this.value;

					_renderOnFontLoaded(this.value);

					_outOfContainment(currentElement);
					stage.renderAll();
					$fontsDropdown.trigger('chosen:updated');
				});

				if(options._useChosen) {
					$fontsDropdown.chosen({width: '100%'});
				}

				options.fonts.sort();
				if(options.defaultFont  == false) {
					options.defaultFont = options.fonts[0];
				}
				for(var i=0; i < options.fonts.length; ++i) {
					$fontsDropdown.append('<option value="'+options.fonts[i]+'" style="font-family: '+options.fonts[i]+';">'+options.fonts[i]+'</option>').trigger("chosen:updated");
				}
				$fontsDropdown.children('option[value="'+options.defaultFont+'"]').prop('selected', 'selected');
				$fontsDropdown.parent().show();

			}

			//edit text
			$toolbar.find('.fpd-text-styles').children('button').click(function(evt) {
				evt.preventDefault();
				var $this = $(this);
				if($this.hasClass('fpd-align-left')) {
					currentElement.setTextAlign('left');
					currentElement.params.textAlign = 'left';
				}
				else if($this.hasClass('fpd-align-center')) {
					currentElement.setTextAlign('center');
					currentElement.params.textAlign = 'center';
				}
				else if($this.hasClass('fpd-align-right')) {
					currentElement.setTextAlign('right');
					currentElement.params.textAlign = 'right';
				}
				else if($this.hasClass('fpd-bold')) {
					if(currentElement.getFontStyle() == 'bold') {
						currentElement.setFontStyle('normal');
						currentElement.params.fontStyle = 'normal';
					}
					else {
						currentElement.setFontStyle('bold');
						currentElement.params.fontStyle = 'bold';
					}

				}
				else if($this.hasClass('fpd-italic')) {
					if(currentElement.getFontStyle() == 'italic') {
						currentElement.setFontStyle('normal');
						currentElement.params.fontStyle = 'normal';
					}
					else {
						currentElement.setFontStyle('italic');
						currentElement.params.fontStyle = 'italic';
					}
				}
				stage.renderAll();
			});

			if(options.patterns && options.patterns.length > 0) {


				for(var i=0; i < options.patterns.length; ++i) {
					var patternUrl = options.patterns[i];
					$patternWrapper.children('ul').append('<li><img src="'+patternUrl+'" class="" /></li>')
					.children('li:last').click(function() {
						_setPattern($(this).children('img').attr('src'), currentElement);
					});
				}

				$patternWrapper.children('ul').niceScroll({cursorcolor:"#2E3641"});

			}

			//center element
			$toolbarButtons.children('.fpd-center-horizontal, .fpd-center-vertical').click(function(evt) {
				evt.preventDefault();
				var $this = $(this);
				_centerObject(currentElement, $this.hasClass('fpd-center-horizontal'), $this.hasClass('fpd-center-vertical'), currentBoundingObject ? currentBoundingObject.getBoundingRect() : false);
			});

			//change z position
			$toolbarButtons.children('.fpd-move-down, .fpd-move-up').click(function(evt) {
				evt.preventDefault();
				var objects = stage.getObjects(),
					currentZIndex,
					lowestZIndex = null,
					highestZIndex = null;

				for(var i=0; i <objects.length; ++i) {
					//get lowest and highest z index of view
					if(objects[i].viewIndex == currentViewIndex) {
						if(lowestZIndex == null) {
							lowestZIndex = i;
						}
						if(highestZIndex == null) {
							highestZIndex = i;
						}
						else {
							highestZIndex++;
						}
					}
					//get z-index of the current element
					if(objects[i] == currentElement) {
						currentZIndex = i;
					}
				}

				if(currentZIndex > highestZIndex) {
					currentZIndex = highestZIndex;
				}
				if(currentZIndex < lowestZIndex) {
					currentZIndex = lowestZIndex;
				}

				if($(this).hasClass('fpd-move-down')) {
					if(currentZIndex != lowestZIndex) {
						currentElement.moveTo(currentZIndex-1);
					}
				}
				else {
					if(currentZIndex != highestZIndex) {
						currentElement.moveTo(currentZIndex+1);
					}
				}
			});

			//reset element to his origin
			$toolbarButtons.children('.fpd-reset').click(function(evt) {
				evt.preventDefault();
				if(currentElement) {
					var params = currentElement.params;

					if(currentElement.type == 'i-text') {
						currentElement.fontSize = params.textSize;
						currentElement.setText(params.source);
						currentElement.setFontFamily(params.originFont);
						currentElement.setTextAlign('left');
						currentElement.setFontStyle('normal');
						$fontsDropdown.children('option[value="'+options.defaultFont+'"]').prop('selected', 'selected');
					}

					currentElement.left = params.originX;
					currentElement.top = params.originY;
					currentElement.scaleX = params.originScale;
					currentElement.scaleY = params.originScale;
					currentElement.angle = 0;

					if(params.colors && params.currentColor != params.colors[0]) {
						_changeColor(currentElement, params.colors[0]);
				    }

				    _outOfContainment(currentElement);

					stage.renderAll();

					params.x = params.originX;
					params.y = params.originY;
					params.degree = 0;
					currentElement.params = params;
				}
			});

			//remove object
			$toolbarButtons.children('.fpd-trash').click(function(evt) {
				evt.preventDefault();
				if(currentElement.params.price != 0) {
					currentPrice -= currentElement.params.price;
					$elem.trigger('priceChange', [currentElement.params.price, currentPrice]);
				}

				$sidebarContent.find('.fpd-elements-dropdown').children('option[value="'+currentElement.id+'"]').remove().trigger("chosen:updated");
				currentElement.remove();

				$elem.trigger('elementRemove', [currentElement]);

				_deselectElement();
			});

			//download image handler
			$menubar.find('.fpd-download-image').click(function(evt) {
				$(this).attr('href', thisClass.getProductDataURL()).attr('download', 'Product.png');
			});

			//print product handler
			$menubar.find('.fpd-print').click(function(evt) {
				evt.preventDefault();
				thisClass.print();
			});

			//reset product handler
			$menubar.find('.fpd-reset-product').click(function(evt) {
				evt.preventDefault();
				thisClass.loadProduct(currentViews);
			});

			if(options._useChosen) {
				$sidebarContent.find('.fpd-elements-dropdown').chosen({width: '100%'});
			}

			$elem.find('.chosen-container').addClass('fpd-border-color');

			if(options.editorMode) {
				$.post(
					options.templatesDirectory+'editorbox.php',
					function(html){
						$elem.after($.parseHTML(html));
						$editorBox = $elem.next('.fpd-editor-box');
					}
				)
			}

			$elem.trigger('ready');

			//load first product
			if($sidebarContent.find('.fpd-products ul li:first').size() > 0) {
				$sidebarContent.find('.fpd-products ul li:first').click();
			}
			else {
				$productLoader.hide();
			}

		}

		//if an object is editable, set the modification parameters
		var _setModification = function(element) {

			var elemParams = element.params;

			if(typeof elemParams.colors === 'object' || elemParams.removable || elemParams.draggable || elemParams.resizable || elemParams.rotatable ||elemParams.zChangeable) {

				element.isEditable = element.evented = true;
				element.set('selectable', true);

				if(element.viewIndex == currentViewIndex) {
					$sidebarContent.find('.fpd-elements-dropdown')
					.append('<option value="'+element.id+'">'+element.title+'</option>')
					.trigger("chosen:updated");
				}

			}

			if(options.editorMode) {
				return false;
			}

			if(elemParams.draggable) {
				element.lockMovementX = element.lockMovementY = false;
			}

			if(elemParams.rotatable) {
				element.lockRotation = false;
			}

			if(elemParams.resizable) {
				element.lockScalingX = element.lockScalingY = false;
			}

			if(elemParams.resizable || elemParams.rotatable) {
				element.hasControls = true;
			}

		};

		var _createSingleView = function(title, elements) {

			var element = elements[0];
			//check if view contains at least one element
			if(element) {
				var countElements = 0;
				//iterative function when element is added, add next one
				function _onElementAdded(evt, addedElement) {

					countElements++;
					//add all elements of a view
					if(countElements < elements.length) {
						var element = elements[countElements];
						thisClass.addElement( element.type, element.source, element.title, element.parameters, viewsLength-1);
					}
					//all elements are added
					else {
						$elem.off('elementAdded', _onElementAdded);
						$elem.trigger('viewCreate', [elements, title]);
					}

				};
				//listen when element is added
				$elem.on('elementAdded', _onElementAdded);
				//add first element of view
				thisClass.addElement( element.type, element.source, element.title, element.parameters, viewsLength-1);
			}
			//no elements in view, view is created without elements
			else {
				$elem.trigger('viewCreate', [elements, title]);
			}

		}

		var _updateTooltip = function(text, type) {

			var left = currentElement.left,
				top = currentElement.top;

			currentElement.setCoords();

			if(type == 'moved')  {
				left = currentElement.oCoords.tl.x,
				top = currentElement.oCoords.tl.y;
			}
			else if(type == 'scaled')  {
				left = currentElement.oCoords.br.x,
				top = currentElement.oCoords.br.y;
			}
			else {
				left = currentElement.oCoords.mt.x,
				top = currentElement.oCoords.mt.y;
			}

			if(currentElement.isOut) {
				text = '"'+currentElement.title+'"' + options.labels.outOfContainmentAlert;
			}

			$modificationTooltip.css({left: left, top: top+$menubar.height()}).tooltipster('reposition').tooltipster('show').tooltipster('update', text);
			stage.on({
				'mouse:up': function() {
					$modificationTooltip.tooltipster('hide');
					stage.off('mouseup');
				}
			})
		};

		var _changeColor = function(element, hex) {

			if(hex.length == 4) {
				hex += hex.substr(1, hex.length);
			}

			if(element.type == 'i-text') {
				//set color of a text element
				element.setFill(hex);
				stage.renderAll();
				element.params.pattern = null;
			}
			else {

				var colorizable = _elementIsColorizable(element);
				if(colorizable == 'png' || colorizable == 'dataurl') {
					element.filters.push(new fabric.Image.filters.Tint({color: hex, opacity: 1}));
					element.applyFilters(stage.renderAll.bind(stage));
				}
				else if(colorizable == 'svg') {
					element.setFill(hex);
				}

			}

			element.params.currentColor = hex;
			$colorPicker.children('input').spectrum("set", hex);

			_checkColorControl(element, hex);
			_updateEditorBox(element);

		}

		//checks if the color of another element is controlled by the current element color
		var _checkColorControl = function(object, color) {
			if(object.colorControlFor) {

				var objects = object.colorControlFor;
				for(var i=0; i < objects.length; ++i) {
					_changeColor(objects[i], color);
				}
			}
		}

		//deselect all element
		var _deselectElement = function(discardActiveObject) {

			discardActiveObject = typeof discardActiveObject == 'undefined' ? true : discardActiveObject;

			if(currentBoundingObject) {
				if(currentBoundingObject.isBoundingRect) {
					currentBoundingObject.remove();
				}
				currentBoundingObject.stroke = null;
				currentBoundingObject = null;
			}

			if(discardActiveObject) {
				stage.discardActiveObject();
			}
			stage.renderAll();

			$sidebarContent.find('.fpd-elements-dropdown').children('option:first').prop('selected', true).trigger('chosen:updated');
			$toolbar.hide().children('.fpd-color-picker, .fpd-fonts-dropdown-wrapper, .fpd-customize-text').hide();
			$patternWrapper.hide();
			currentElement = null;

			if($editorBox) {
				$editorBox.find('i').text('');
			}
		};

		//check if element is in the containment
		var _outOfContainment = function(target) {

			if(currentBoundingObject) {
				target.setCoords();
				var objectBoundingBox = target.getBoundingRect(),
					targetBoundingBox = currentBoundingObject.getBoundingRect(),
					isOut = false,
					tempIsOut = target.isOut;

				var isOut = !target.isContainedWithinRect(
					{x: currentBoundingObject.oCoords.tl.x+target.padding, y: currentBoundingObject.oCoords.tl.y+target.padding},
					{x: currentBoundingObject.oCoords.br.x-target.padding, y: currentBoundingObject.oCoords.br.y-target.padding}
				);

				if(isOut) {
					target.borderColor = 'red';
					target.opacity = 0.5;
					target.isOut = true;
				}
				else {
					target.borderColor = 'white';
					target.opacity = 1;
					target.isOut = false;
				}

				if(tempIsOut != target.isOut && tempIsOut != undefined) {
					if(isOut) {
						$elem.trigger('elementOut');
					}
					else {
						$elem.trigger('elementIn');
					}
				}
				return isOut;
			}
			else {
				return false;
			}

		};

		//returns an object with the saved products for the current showing product
		var _getSavedProducts = function() {

			return JSON.parse(window.localStorage.getItem($elem.attr('id')));

		};

		//check if key is valid and available
		var _checkStorageKey = function(key) {

			//check if a key is set
			if(key == null) { return -1; }
			//check if key is not empty
			else if(key == "") { return 0; }

			//everything is fine
			return 1;

		};

		var _doCentering = function(object) {

			if(object.params.boundingBox) {
				_getBoundingBoxAndCenter(object);
			}
			else {
				_centerObject(object, true, true, false);
			}

			object.params.autoCenter = false;
		};

		//center object
		var _centerObject = function(object, hCenter, vCenter, boundingBox) {

			if(hCenter) {
				if(boundingBox) {
					object.left = boundingBox.left + boundingBox.width * 0.5;
				}
				else {
					object.centerH();
				}

			}

			if(vCenter) {
				if(boundingBox) {
					object.top = boundingBox.top + boundingBox.height * 0.5;
				}
				else {
					object.centerV();
				}
			}

			_outOfContainment(object);

			stage.renderAll();
			object.setCoords();
			object.params.x = object.left;
			object.params.y = object.top;

		}

		//get the bounding box and center object in it
		var _getBoundingBoxAndCenter = function(targetObject) {

			var params = targetObject.params;
			if(params.boundingBox) {
				var boundingBox;
				if(typeof params.boundingBox == "object") {
					boundingBox = {left: params.boundingBox.x - params.boundingBox.width*0.5, top: params.boundingBox.y - params.boundingBox.height * 0.5, width: params.boundingBox.width, height: params.boundingBox.height};
				}
				else {
					var objects = stage.getObjects();
					for(var i=0; i < objects.length; ++i) {
						//get all layers from first view
						var object = objects[i];
						if(object.viewIndex == currentViewIndex) {
							if(params.boundingBox == object.title) {
								boundingBox = object.getBoundingRect();
								break;
							}
						}
					}
				}
				if(boundingBox) {
					_centerObject(targetObject, true, true, boundingBox);
					targetObject.params.originX = targetObject.left;
					targetObject.params.originY = targetObject.top;
				}
			}

		};

		var _addSavedProduct = function(thumbnail, product) {

			var $list = $sidebarContent.children('.fpd-saved-products').children('ul');

			//create new list item
			$list.append('<li><button><span>&times;</span></button><img src="'+thumbnail+'" /></li>')
			//select stored product
			.children('li:last').children('img').click(function(evt) {
				//load product
				evt.preventDefault();
				thisClass.loadProduct($(this).data('product'));
				currentProductIndex = -1;
			}).data('product', product)
			//remove stored product
			.parent().children('button').click(function(evt) {
				evt.preventDefault();
				//confirm delete
				var result = confirm(options.labels.confirmProductDelete);
				if(!result) { return false; }

				var index = $list.children('li').index($(this).parent('li').remove()),
					savedProducts = _getSavedProducts();

				savedProducts.splice(index, 1);

				window.localStorage.setItem($elem.attr('id'), JSON.stringify(savedProducts));
			});

		};

		var _createScrollbar = function($target) {

			$target.height($target.parent().height()-$target.position().top-1)
			.niceScroll({cursorcolor:"#2E3641"});

		};

		var _getScalingByDimesions = function(imgW, imgH, resizeToW, resizeToH) {

			var scaling = 1;
			if(imgW > imgH) {
				if(imgW > resizeToW) { scaling = resizeToW / imgW; }
				if(scaling * imgH > resizeToH) { scaling = resizeToH / imgH; }
			}
			else {
				if(imgH > resizeToH) { scaling = resizeToH / imgH; }
				if(scaling * imgW > resizeToW) { scaling = resizeToW / imgW; }
			}

			return scaling;

		};

		var _sortByName = function (a, b) {

		    var x = a.name.toLowerCase();
		    var y = b.name.toLowerCase();
		    return ((x < y) ? -1 : ((x > y) ? 1 : 0));

		};

		var _updateEditorBox = function(element) {

			if($editorBox == undefined) {
				return false;
			}

			var params = element.params;
			$editorBox.find('.fpd-current-element').text(element.title);
			$editorBox.find('.fpd-position').text('x: ' + params.x + ' y: '+ params.y);
			$editorBox.find('.fpd-dimensions').text('width: ' + Math.round(element.getWidth()) + ' height: '+ Math.round(element.getHeight()));
			$editorBox.find('.fpd-scale').text(Number(params.scale) % 360);
			$editorBox.find('.fpd-degree').text(params.degree);
			$editorBox.find('.fpd-color').text(params.currentColor);

		};

		var _renderOnFontLoaded = function(fontName) {

			WebFont.load({
				custom: {
				  families: [fontName]
				},
				fontactive: function(familyName, fvd) {
					stage.renderAll();
				}
			});

		};

		var _elementIsColorizable = function(element) {

			if(element.type == 'i-text') {
				return 'text';
			}

			//check if url is a png or base64 encoded
			var imageParts = element.source.split('.');
			//its base64 encoded
			if(imageParts.length == 1) {
				if(imageParts[0].search('data:image/png;') == -1) {
					element.params.currentColor = element.params.colors = false;
					return false;
				}
				else {
					return 'dataurl';
				}
			}
			//its a url
			else {
				//not a png, not colorization
				if($.inArray('png', imageParts) == -1 && $.inArray('svg', imageParts) == -1) {
					element.params.currentColor = element.params.colors = false;
					return false;
				}
				else {
					if($.inArray('svg', imageParts) == -1) {
						return 'png';
					}
					else {
						return 'svg';
					}
				}
			}
		};

		var _setPattern = function(url, element) {

			if(element.type == 'image') {

				/*fabric.Image.fromURL(url, function(img) {

					img.scaleToWidth(100);
					var patternSourceCanvas = new fabric.StaticCanvas();
					patternSourceCanvas.add(img);

					var pattern = new fabric.Pattern({
						source: function() {
							patternSourceCanvas.setDimensions({
								width: img.getWidth(),
								height: img.getHeight()
							});
							return patternSourceCanvas.getElement();
						},
						repeat: 'repeat'
					});

					element.setFill(pattern);
					stage.renderAll();
				});*/

			}
			else if(element.type == 'i-text') {

				fabric.util.loadImage(url, function(img) {

					element.fill = new fabric.Pattern({
						source: img,
						repeat: 'repeat'
					});
					stage.renderAll();
					element.params.pattern = url;

				});

			}

		};

		//set the z-index of an element
		var _setZIndex = function(element, z) {

			var objects = stage.getObjects(),
				viewZIndexes = 0;

			for(var i=0; i < objects.length; ++i) {
				var object = objects[i];
				//only objects of the current view
				if(object.visible) {
					//detect when required z-index of view reached
					if(viewZIndexes == z) {
						element.moveTo(i);
						break;
					}
					viewZIndexes++;
				}
			}
		};

		var _addDesignToList = function(design) {

			var $designsList = $sidebarContent.find('.fpd-designs ul');

			$designsList.append('<li><span class="fpd-loading-gif"></span><img src="'+design.source+'" title="'+design.title+'" style="display: none;" /></li>')
			.children('li:last').click(function(evt) {
				evt.preventDefault();

				var $this = $(this),
					designParams = $this.data('parameters');

				thisClass.addElement('image', $this.children('img').attr('src'), $this.children('img').attr('title'), designParams, currentViewIndex);

			}).data('parameters', design.parameters)
			.children('img').load(function() {

				$(this).fadeIn(500).prev('span').fadeOut(300, function() {
					$(this).remove();
				});

			});

			$designsList.getNiceScroll().resize();

		};


		//----------------------------------
		// ------- PUBLIC METHODS ----------
		//----------------------------------

		//Returns JSON representation of canvas
		this.getFabricJSON = function() {

			_deselectElement();
			var json = stage.toJSON(['viewIndex']);
			json.width = stage.width;
			json.height = stage.height;

			return json;

		};

		/*
		*	Returns the current price for the product
		*
		*/
		this.getPrice = function() {

			return currentPrice;

		};

		/*
		*	Returns the current product with all views
		*	onlyEditableElements (boolean): If set to true, it will only returns the editable elements in a view
		*
		* 	Returns an array with all views
		*/
		this.getProduct = function(onlyEditableElements) {
			 onlyEditableElements = typeof onlyEditableElements !== 'undefined' ? onlyEditableElements : false;

			_deselectElement();

			var objects = stage.getObjects();
			for(var i=0; i<objects.length;++i) {
				var object = objects[i];
				if(object.isOut) {
					alert('"'+object.title+'"' + options.labels.outOfContainmentAlert);
					return false;
				}
			}

			var product = [];
			//add views
			for(var i=0; i<currentViews.length; ++i) {
				var view = currentViews[i];
				product.push({title: view.title, thumbnail: view.thumbnail, elements: []});
			}

			for(var i=0; i<objects.length;++i) {
				var object = objects[i];
				var jsonItem = {title: object.title, source: object.source, parameters: object.params, type: object.type};

				if(onlyEditableElements) {
					if(object.isEditable) {
						product[object.viewIndex].elements.push(jsonItem);
					}
				}
				else {
					product[object.viewIndex].elements.push(jsonItem);
				}


			}

			//returns an array with all views
			return product;

		};

		/*
		*	Returns an array with the image data urls of the current views in the stage
		*
		*/
		this.getViewsDataURL = function(format, backgroundColor) {

			format = typeof format !== 'undefined' ? format : 'png';
			backgroundColor = typeof backgroundColor !== 'undefined' ? backgroundColor : 'transparent';

			_deselectElement();

			var dataURLs = [];

			stage.setBackgroundColor(backgroundColor, function() {

				var objects = stage.getObjects();

				for(var i=0; i<viewsLength;++i) {
					for(var j=0; j<objects.length; ++j) {
						var object = objects[j];
						object.visible = object.viewIndex == i;
					}
					dataURLs.push(stage.toDataURL({format: format}));
				}

				//hide elements again that are not in the current view index
				for(var i=0; i<objects.length; ++i) {
					var object = objects[i];
					object.visible = object.viewIndex == currentViewIndex;
				}
				stage.setBackgroundColor('transparent').renderAll();

			});

			return dataURLs;

		}

		/*
		*	Returns the base64 data url of all views
		*
		*/
		this.getProductDataURL = function(format, backgroundColor) {

			format = typeof format !== 'undefined' ? format : 'png';
			backgroundColor = typeof backgroundColor !== 'undefined' ? backgroundColor : 'transparent';

			_deselectElement();

			$viewSelection.children('li:first').click();
			var dataUrl;
			stage.setBackgroundColor(backgroundColor, function() {

				//increase stage height and reposition objects
				stage.setDimensions({height: options.dimensions.productStageHeight * viewsLength});
				var objects = stage.getObjects();
				for(var i=0; i<objects.length; ++i) {
					var object = objects[i];
					object.visible = true;
					object.top = object.top + (object.viewIndex * options.dimensions.productStageHeight);
				}

				//get data url
				dataUrl = stage.toDataURL({format: format});

				//set stage height to default
				stage.setDimensions({height: options.dimensions.productStageHeight});
				for(var i=0; i<objects.length; ++i) {
					var object = objects[i];
					object.visible = object.viewIndex == 0;
					object.top = object.top - (object.viewIndex * options.dimensions.productStageHeight);
				}
				stage.setBackgroundColor('transparent').renderAll();

			});

			return dataUrl;

		};

		/*
		*	Returns the amount of products in the products sidebar
		*
		*/
		this.getProductsLength = function() {

			return $sidebarContent.find('.fpd-products ul li').size();

		};

		/*
		*	Returns the current view object
		*
		*/
		this.getView = function() {

			return currentViews[currentViewIndex];

		};

		/*
		*	Returns the base64 data url of the current view
		*
		*/
		this.getViewDataURL = function(format) {

			format = typeof format !== 'undefined' ? format : 'png';

			_deselectElement();

			return stage.toDataURL({format: format});

		};

		/*
		*	Returns the current view index
		*
		*/
		this.getViewIndex = function() {

			return currentViewIndex;

		};

		/*
		*	Returns the fabric canvas
		*/
		this.getStage = function() {

			return stage;

		};

		/*
		*	Adds a new product to products sidebar
		*	views (array): An array containing all views for the product. A view element is an object with title,thumbnail and elements attributes, where the elements
		*	attribute is also an object {type, source, title, parameters}
		*
		*/
		this.addProduct = function(views) {

			//load product by click
			$sidebarContent.find('.fpd-products ul').append('<li><span class="fpd-loading-gif"></span><img src="'+views[0].thumbnail+'" title="'+views[0].title+'" style="display:none;" /></li>').children('li:last').click(function(evt) {
				evt.preventDefault();
				var $this = $(this),
					index = $sidebarContent.find('.fpd-products ul li').index($this);

				thisClass.selectProduct(index);
			}).data('views', views)
			.children('img').load(function() {
				$(this).fadeIn(500).prev('span').fadeOut(300, function() {
					$(this).remove();
				});
			});

			$sidebarContent.find('.fpd-designs ul').getNiceScroll().resize();

			//show products in sidebar when there is more than 1
			if($sidebarContent.find('.fpd-products ul').children('li').length > 1) {
				$sidebarNavi.find('li[data-target=".fpd-products"]').show();
			}

		};

		/*
		*	Loads a new product into the stage
		*	views (array): An array containing all views for the product. A view element is an object with title,thumbnail and elements attributes, where the elements
		*	attribute is also an object {type, source, title, parameters}
		*
		*/
		this.loadProduct = function(views) {

			thisClass.clear();
			$productLoader.stop().fadeIn(300);

			currentViews = views;

			currentProductTitle = currentViews[0].title;

			$productContainer.append('<ul class="fpd-views-selection"></ul>');

			$elem.on('viewCreate', _onViewCreated);

			function _onViewCreated() {
				//add all views of product till views end is reached
				if(viewsLength < currentViews.length) {
					thisClass.addView(currentViews[viewsLength]);
				}
				//all views added
				else {
					$elem.off('viewCreate', _onViewCreated);
					$elem.trigger('productCreate', [currentProductTitle]);
					$productLoader.stop().fadeOut(300);
				}

			};

			thisClass.addView(currentViews[0]);

		};

		/*
		*	Selects a product from the products sidebar by index
		*	index (number): A value between 0 and n-1, at which n is the amount of products in the products sidebar. If the set index is bigger than the products length, it will select the last product
		*
		*/
		this.selectProduct = function(index) {
			if(index == currentProductIndex) {	return false; }

			currentProductIndex = index;
			if(index < 0) { currentProductIndex = 0; }
			else if(index > thisClass.getProductsLength()-1) { currentProductIndex = thisClass.getProductsLength()-1; }

			var views = $sidebarContent.find('.fpd-products ul li').eq(currentProductIndex).data('views');
			thisClass.loadProduct(views);

		};

		/*
		*	Selects a view from the current showing product
		*	index (number): A value between 0 and n-1, at which n is the amount of views in the current showing product. If the set index is bigger than the views length, it will select the last view
		*
		*/
		this.selectView = function(index) {
			if(index == currentViewIndex) { return false; }

			currentViewIndex = index;
			if(index < 0) { currentViewIndex = 0; }
			else if(index > $viewSelection.children('li').size()-1) { currentViewIndex = $viewSelection.children('li').size()-1; }

			_deselectElement();
			$sidebarContent.find('.fpd-elements-dropdown').children('option:not([value="none"])').remove();
			var objects = stage.getObjects();
			for(var i=0; i < objects.length; ++i) {
				var object = objects[i];
				object.visible = object.viewIndex == currentViewIndex;
				if(object.viewIndex == currentViewIndex && object.isEditable) {
					$sidebarContent.find('.fpd-elements-dropdown').append('<option value="'+object.id+'">'+object.title+'</option>');
				}
			}
			$sidebarContent.find('.fpd-elements-dropdown').trigger("chosen:updated");
			stage.renderAll();

		};

		/*
		*	Removes a product from the products sidebar by index
		*	index (number): A value between 0 and n-1, where n is the length of products in the products sidebar. If the set index is bigger than the products length, it will select the last product
		*
		*/
		this.removeProduct = function(index) {

			if(index < 0) { index = 0; }
			else if(index > thisClass.getProductsLength()-1) { index = thisClass.getProductsLength()-1; }

			$sidebar.children('li').eq(index).remove();

			if(index == currentProductIndex) {
				thisClass.clear();
				currentProductIndex = -1;
			}

		};

		/*
		*	Adds a view to the current selected product
		*	view (object): A object containing title, thumbnail and elements[]
		*
		*/
		this.addView = function(view) {

			viewsLength++;

			_createSingleView(view.title, view.elements);

			$viewSelection = $productContainer.children('.fpd-views-selection');
			$viewSelection.append('<li class="fpd-content-color"><img src="'+view.thumbnail+'" title="'+view.title+'" class="fpd-tooltip" /></li>')
			.children('li:last').click(function(evt) {

				evt.preventDefault();
				thisClass.selectView($viewSelection.children('li').index($(this)));

			});

			//enable tooltips
			$('.fpd-tooltip').tooltipster({offsetY: -3, theme: '.fpd-tooltip-theme', touchDevices: false});

			viewsLength > 1 ? $viewSelection.show() : $viewSelection.hide();

		};

		/*
		*	Adds an new element to the view
		*	type (string): image or text
		*	source (string): URL to the png when type is "image" or the text when type is "text"
		*	title (string): The title for the element
		*	params (object): An object containing the element parameters
		*	containerIndex (number): The view index where the element should be added to
		*
		* 	Returns an array with all views
		*/
		this.addElement = function(type, source, title, params, containerIndex) {

			containerIndex = typeof containerIndex !== 'undefined' ? containerIndex : currentViewIndex;

			_deselectElement();

			if(typeof params != "object") {
				alert("The element "+title+" has not a valid JSON object as parameters!");
				return false;
			}

			params = $.extend({}, options.elementParameters, params);
			params.originX = params.x;
			params.originY = params.y;
			params.originScale = params.scale;
			params.source = source;

			var pushTargetObject = false,
				targetObject = null;
			//store current color and convert colors in string to array
			if(params.colors && typeof params.colors == 'string') {
				//check if string contains hex color values
				if(params.colors.indexOf('#') == 0) {
					//convert string into array
					var colors = params.colors.replace(/\s+/g, '').split(',');
					params.colors = colors;
				}
				else if(viewsLength > 1) {
					//get all layers from first view
					var objects = stage.getObjects();
					for(var i=0; i < objects.length; ++i) {
						var object = objects[i];
						if(object.viewIndex == 0) {
							//look for the target object where to get the color from
							if(params.colors == object.title && targetObject == null) {
								targetObject = object;
								//push all objects in array that should have a color control from the target object
								pushTargetObject = true;
							}
						}
					}
				}
			}

			var objParams = {
				source: source,
				title: title,
				top: params.y,
				left: params.x,
				originX: 'center',
				originY: 'center',
				scaleX: params.scale,
				scaleY: params.scale,
				angle: params.degree,
				id: String(new Date().getTime()),
				visible: containerIndex == currentViewIndex,
				viewIndex: containerIndex,
				lockUniScaling: true,
				lineHeight: 1.2
			};

			if(options.editorMode) {
				params.removable = params.resizable = params.rotatable = params.zChangeable = true;
			}
			else {
				$.extend(objParams, {
					selectable: false,
					lockRotation: true,
					lockScalingX: true,
					lockScalingY: true,
					lockMovementX: true,
					lockMovementY: true,
					hasControls: false,
					evented: false
				});
			}

			if(type == 'image' || type == 'path') {

				var _fabricImageLoaded = function(fabricImage, params) {

					var w = fabricImage.width * params.scale,
						h = fabricImage.height * params.scale;

					$.extend(objParams, {scaleX: params.scale, scaleY: params.scale, params: params, crossOrigin: "anonymous"});
					fabricImage.set(objParams);

					if(pushTargetObject) {
						if(targetObject.colorControlFor) {
							targetObject.colorControlFor.push(fabricImage);
						}
						else {
							targetObject.colorControlFor = [];
							targetObject.colorControlFor.push(fabricImage);
						}
					}

					_setModification(fabricImage);

					stage.add(fabricImage);

					if(params.autoCenter) {
						_doCentering(fabricImage);
					}

					//change element color
					if(params.currentColor) {
						_changeColor(fabricImage, params.currentColor);
					}

					if(params.z !== false) {
						_setZIndex(fabricImage, params.z);
					}

					$elem.trigger('elementAdded', [fabricImage]);

				};

				var imageParts = source.split('.');
				if($.inArray('svg', imageParts) != -1) {
					fabric.loadSVGFromURL(source, function(fabricSvg) {

						fabricSvg = fabricSvg[0];
						_fabricImageLoaded(fabricSvg, params);

					});
				}
				else {
					new fabric.Image.fromURL(source, function(fabricImg) {

						_fabricImageLoaded(fabricImg, params);

					});
				}

			}
			else if(type == 'text' || type == 'i-text') {

				params.text = params.text ? params.text : params.source;
				params.font = params.font ? params.font : options.defaultFont;
				params.fontStyle = params.fontStyle ? params.fontStyle : 'normal';
				params.textAlign = params.textAlign ? params.textAlign : 'left';
				params.originFont = params.font;

				$.extend(objParams, {
					fontSize: params.textSize,
					fontFamily: params.font,
					fontStyle: params.fontStyle,
					textAlign: params.textAlign,
					fill: params.colors[0] ? params.colors[0] : "#000000",
					editable: params.textEditable,
					params: params
				});

				var fabricText = new fabric.IText(params.text.replace(/\\n/g, '\n'), objParams);
				stage.add(fabricText);

				_setModification(fabricText);

				if(params.autoCenter) {
					_doCentering(fabricText);
				}

				//change element color
				if(params.currentColor) {
					_changeColor(fabricText, params.currentColor);
				}

				if(params.pattern) {
					_setPattern(params.pattern, fabricText);
				}

				if(params.z !== false) {
					_setZIndex(fabricText, params.z);
				}

				_renderOnFontLoaded(params.font);

				$elem.trigger('elementAdded', [fabricText]);
			}
			else {
				alert('Sorry. This type of element is not allowed!');
				return false;
			}

			if(params.price) {
				currentPrice += params.price;
				$elem.trigger('priceChange', [params.price, currentPrice]);
			}

		};

		/*
		* Adds a new design to the design sidebar
		*
		*/
		this.addDesign = function(source, title, parameters, category) {

			//categories are not used
			if($designCategories == null) {
				_addDesignToList({source: source, title: title, parameters: parameters});
			}
			//categories are used
			else {
				category = typeof category == 'undefined' ? $designCategories.val() : category;

				//check if category exists, otherwise create one
				if(designCategories[category] == undefined) {
					designCategories[category] = new Array();
					$designCategories.append('<option value="'+category+'">'+category+'</option>').trigger("chosen:updated");
				}

				//push design in category object
				designCategories[category].push({source: source, title: title, parameters: parameters});

				//add design to list if category is the same like the current showing one
				if($designCategories.val() == category) {
					_addDesignToList({source: source, title: title, parameters: parameters});
				}
			}

			//show designs in sidebar
			$sidebarNavi.find('li[data-target=".fpd-designs"]').show();

		};


		/*
		* Prints the current product
		*
		*/
		this.print = function() {

			var image = new Image();
			image.src = thisClass.getProductDataURL();
			image.onload = function() {
				var popup = window.open('','','width='+this.width+',height='+this.height+',location=no,menubar=no,scrollbars=yes,status=no,toolbar=no');
				popup.document.title = "Print Image";
				$(popup.document.body).append('<img src="'+this.src+'" />');

				setTimeout(function() {
					popup.print();
				}, 1000);
			}

			return false;

		};


		/*
		* Creates an image, that can be opened in a pop-up and downloaded
		*
		*/
		this.createImage = function(openInPopup, forceDownload) {

			if(typeof(openInPopup)==='undefined') openInPopup = true;
			if(typeof(forceDownload)==='undefined') forceDownload = false;

			var dataUrl = thisClass.getProductDataURL();
			var image = new Image();
			image.src = dataUrl;

			image.onload = function() {
				if(openInPopup) {

					var popup = window.open('','','width='+this.width+',height='+this.height+',location=no,menubar=no,scrollbars=yes,status=no,toolbar=no');
					popup.document.title = "Product Image";
					$(popup.document.body).append('<img src="'+this.src+'" />');

					if(forceDownload) {
						window.location.href = popup.document.getElementsByTagName('img')[0].src.replace('image/png', 'image/octet-stream');
					}
				}
				$elem.trigger('imageCreate', [dataUrl]);
			}

			return image;

		};

		//removes all elements from the product container
		this.clear = function() {

			_deselectElement();
			if($viewSelection) { $viewSelection.remove(); }
			$sidebarContent.find('.fpd-elements-dropdown').children('option:not([value="none"])').remove();
			$sidebarContent.find('.fpd-elements-dropdown').trigger("chosen:updated");
			stage.clear();
			viewsLength = currentViewIndex = currentPrice = 0;
			currentViews = currentElement = null;
			$elem.trigger('stageClear');

		};


		//----------------------------------
		// ------- Fabric.js Methods ----------
		//----------------------------------

		fabric.Object.prototype.drawControls = function(ctx) {
			if (!this.hasControls) return this;

			var size = this.cornerSize,
			  size2 = size / 2,
			  strokeWidth2 = ~~(this.strokeWidth / 2), // half strokeWidth rounded down
			  left = -(this.width / 2),
			  top = -(this.height / 2),
			  _left,
			  _top,
			  sizeX = size / this.scaleX,
			  sizeY = size / this.scaleY,
			  paddingX = this.padding / this.scaleX,
			  paddingY = this.padding / this.scaleY,
			  scaleOffsetY = size2 / this.scaleY,
			  scaleOffsetX = size2 / this.scaleX,
			  scaleOffsetSizeX = (size2 - size) / this.scaleX,
			  scaleOffsetSizeY = (size2 - size) / this.scaleY,
			  height = this.height,
			  width = this.width,
			  methodName = this.transparentCorners ? 'strokeRect' : 'fillRect',
			  transparent = this.transparentCorners,
			  isVML = typeof G_vmlCanvasManager !== 'undefined';

			ctx.save();

			ctx.lineWidth = 1 / Math.max(this.scaleX, this.scaleY);

			ctx.globalAlpha = this.isMoving ? this.borderOpacityWhenMoving : 1;
			ctx.strokeStyle = ctx.fillStyle = this.cornerColor;

			// top-left
			_left = left - scaleOffsetX - strokeWidth2 - paddingX;
			_top = top - scaleOffsetY - strokeWidth2 - paddingY;

			isVML || transparent || ctx.clearRect(_left, _top, sizeX, sizeY);
			//ctx[methodName](_left, _top, sizeX, sizeY);

			// top-right
			_left = left + width - scaleOffsetX + strokeWidth2 + paddingX;
			_top = top - scaleOffsetY - strokeWidth2 - paddingY;

			isVML || transparent || ctx.clearRect(_left, _top, sizeX, sizeY);
			//ctx[methodName](_left, _top, sizeX, sizeY);

			// bottom-left
			_left = left - scaleOffsetX - strokeWidth2 - paddingX;
			_top = top + height + scaleOffsetSizeY + strokeWidth2 + paddingY;

			isVML || transparent || ctx.clearRect(_left, _top, sizeX, sizeY);
			//ctx[methodName](_left, _top, sizeX, sizeY);

			// bottom-right
			_left = left + width + scaleOffsetSizeX + strokeWidth2 + paddingX;
			_top = top + height + scaleOffsetSizeY + strokeWidth2 + paddingY;

			isVML || transparent || ctx.clearRect(_left, _top, sizeX, sizeY);
			if(!this.lockScalingX) {
				ctx.drawImage(resizeIcon, _left, _top, sizeX, sizeY);
				ctx[methodName](_left, _top, sizeX, sizeY);
			}

			if (!this.get('lockUniScaling')) {
			// middle-top
			_left = left + width/2 - scaleOffsetX;
			_top = top - scaleOffsetY - strokeWidth2 - paddingY;

			isVML || transparent || ctx.clearRect(_left, _top, sizeX, sizeY);
			ctx[methodName](_left, _top, sizeX, sizeY);

			// middle-bottom
			_left = left + width/2 - scaleOffsetX;
			_top = top + height + scaleOffsetSizeY + strokeWidth2 + paddingY;

			isVML || transparent || ctx.clearRect(_left, _top, sizeX, sizeY);
			ctx[methodName](_left, _top, sizeX, sizeY);

			// middle-right
			_left = left + width + scaleOffsetSizeX + strokeWidth2 + paddingX;
			_top = top + height/2 - scaleOffsetY;

			isVML || transparent || ctx.clearRect(_left, _top, sizeX, sizeY);
			ctx[methodName](_left, _top, sizeX, sizeY);

			// middle-left
			_left = left - scaleOffsetX - strokeWidth2 - paddingX;
			_top = top + height/2 - scaleOffsetY;

			isVML || transparent || ctx.clearRect(_left, _top, sizeX, sizeY);
			ctx[methodName](_left, _top, sizeX, sizeY);
			}

			// middle-top-rotate
			if (this.hasRotatingPoint && !this.lockRotation) {

				_left = left + width/2 - scaleOffsetX;
				_top = this.flipY ?
				  (top + height + (this.rotatingPointOffset / this.scaleY) - sizeY/2 + strokeWidth2 + paddingY)
				  : (top - (this.rotatingPointOffset / this.scaleY) - sizeY/2 - strokeWidth2 - paddingY);

				isVML || transparent || ctx.clearRect(_left, _top, sizeX, sizeY);
				ctx.drawImage(rotateIcon, _left, _top, sizeX, sizeY);
				ctx[methodName](_left, _top, sizeX, sizeY);
			}

			ctx.restore();

			return this;
		};

	}; //plugin class ends

	jQuery.fn.fancyProductDesigner = function( args ) {

		return this.each(function() {

			var element = $(this);

            // Return early if this element already has a plugin instance
            if (element.data('fancy-product-designer')) { return };

            var fpd = new FancyProductDesigner(this, args);

            // Store plugin object in this element's data
            element.data('fancy-product-designer', fpd);

		});
	};

	$.fn.fancyProductDesigner.defaults = {
		_useChosen: true,
		fontDropdown: true, //enable the font dropdown for texts
		fonts: ['Arial', 'Helvetica', 'Times New Roman', 'Verdana', 'Geneva'], //an array containing all available fonts
		customTextParameters: {}, //the parameters for the custom text
		customTexts: true, //user can add custom text
		editorMode: false, //enable the editor mode
		elementParameters: {
			x: 0, //the x-position
			y: 0, //the y-position
			colors: false, //false, a string with hex colors separated by commas for static colors or a single color value for enabling the colorpicker
			removable: false, //false or true
			draggable: false,  //false or true
			rotatable: false, // false or true
			resizable: false,  //false or true
			zChangeable: false, //false or true
			scale: 1, // the scale factor
			degree: 0, //the degree for the rotation
			price: 0, //how much does the element cost
			boundingBox: false, //false, an element by title or an object with x,y,width,height
			autoCenter: false, //when the element is added to stage, center it automatically
			font: false, //Only for text elements. If false it will use the font from the defaultFont option or set a font name from the fonts array
			textSize: 18, //Only for text elements.
			patternable: false, //Only for text elements. User can choose a pattern for the text element
			z: false, //Allows to set the z-index of an element
			textEditable: true //Only for text elements
		}, //the default parameters for all elements (img, span)
		labels: {
			outOfContainmentAlert: ' is out of his containment!', //the alert when a element is moving out of his containment
			confirmProductDelete: "Delete saved product?",
			modificationTooltips: {x: "x: ", y: " y: ", width: "width: ", height: " height: ", angle: "angle: "},
			colorpicker : {cancel: "Cancel", change: "Change Color"},
			uploadedDesignSizeAlert: "Sorry! But the uploaded image size does not conform our indication of size.",
			initText: "Initiliazing product designer..."
		}, //Set custom labels for the titles
		allowProductSaving: true, //Allows the users to save products in a list
		centerInBoundingbox: true, //center added elemets with a bounding box automatically
		templatesDirectory: 'templates/', //the directory that contains the templates
		saveAsPdf: true, //shows the button in the product stage to save product as pdf
		uploadDesigns: true, //users can upload own designs from the sidebar
		customImagesParameters: {
			minW: 100, //the minimum upload size width
			minH: 100, //the minimum upload size height
			maxW: 1500, //the maximum upload size width
			maxH: 1500, //the maximum upload size height
			resizeToW: 300, //resizes the uploaded image to this width, when width is larger than height
			resizeToH: 300 //resizes the uploaded image to this height, when height is larger than width
		}, //additional parameters for uploaded designs, will merge with the elementParameters
		defaultFont: false, //if false, it will choose the first font from the fonts dropdown. If you would like to choose one from the list, set the font name.
		dimensions: {
			sidebarNavWidth: 50,
			sidebarContentWidth: 200,
			sidebarHeight: 650,
			productStageWidth: 600,
			productStageHeight: 600
		},//the dimensions for the product designer
		facebookAppId: '', //to add photos from facebook, you have to set your own facebook api key
		phpDirectory: 'php/', //the path to the directory that contains php scripts that are used for some functions of the plugin
		patterns: [], //an array with the urls to the patterns
		layout: 'icon-sb-left' //icon-sb-left,icon-sb-top,icon-sb-right,icon-sb-bottom
	};

})(jQuery);