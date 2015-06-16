<div class="wrap" id="edit-fancy-products">
	<h2><i class="fa fa-pencil-square-o"></i> <?php _e('Product Builder', 'radykal'); ?></h2>
	<?php

	global $woocommerce;

	$request_view_id = isset($_GET['view_id']) ? $_GET['view_id'] : NULL;
	$fancy_products = $wpdb->get_results("SELECT * FROM ".Fancy_Product_Designer::$views_table_name." GROUP BY product_id");
	$first_views_ids = array();

	if(sizeof($fancy_products) == 0) {
		echo '<div class="updated"><p><strong>'.__('There are no fancy products!', 'radykal').'</strong></p></div></div>';
		exit;
	}

	//save elements of view
	if(isset($_POST['save_elements'])) {

		check_admin_referer( 'fpd_save_elements' );

		$elements = array();
		for($i=0; $i < sizeof($_POST['element_types']); $i++) {

			$element = array();

			$element['type'] = $_POST['element_types'][$i];
			$element['title'] = $_POST['element_titles'][$i];
			$element['source'] = $_POST['element_sources'][$i];

			$parameters = array();
			parse_str($_POST['element_parameters'][$i], $parameters);

			if(is_array($parameters)) {
				foreach($parameters as $key => $value) {
					if($value == '') {
						$parameters[$key] = NULL;
					}
					else {
						$parameters[$key] = preg_replace('/\s+/', '', $value);
					}
				}
			}

			$element['parameters'] = $parameters;

			array_push($elements, $element);

		}

		$result = $wpdb->update(
			Fancy_Product_Designer::$views_table_name,
			array('elements' => serialize($elements)),
			array('ID' => $_POST['view_id']),
			'%s',
			'%d'
		);

		$requested_view_elements = $elements;

		echo '<div class="updated"><p><strong>'.__('Elements saved.', 'radykal').'</strong></p></div>';

	}
	?>
	<br /><br />
	<h3><?php _e( 'Select a view of a fancy product:', 'radykal' ); ?></h3>
	<select id="fpd-view-switcher">
		<?php
			if(is_array($fancy_products)) {
				foreach($fancy_products as $fancy_product) {
					$fancy_product_id = $fancy_product->product_id;
					echo '<optgroup label="'.get_the_title($fancy_product_id).'" id="'.$fancy_product_id.'">';
					$views = $wpdb->get_results("SELECT * FROM ".Fancy_Product_Designer::$views_table_name." WHERE product_id=$fancy_product_id ORDER BY ID");
					$temp_view_id = NULL;
					if(is_array($views)) {
						foreach($views as $view) {
							//get first view
							if($request_view_id == NULL)
								$request_view_id = $view->ID;
							//save first views ids
							if($temp_view_id == NULL) {
								$first_views_ids[] = $view->ID;
							}
							//get requested view
							if($request_view_id == $view->ID && $requested_view_elements == NULL) {
								$requested_view_elements = unserialize($view->elements);
							}
							echo '<option value="'.$view->ID.'" '.selected( $request_view_id ,  $view->ID, false).'>'.$view->title.'</option>';

							$temp_view_id = $view->ID;
						}
					}
					echo '</optgroup>';
				}
			}
		?>
	</select>
	<br /><br />
	<div id="fpd-manage-elements">

		<h3><?php _e( 'Manage elements', 'radykal' ); ?></h3>
		<div id="fpd-add-element">
			<button class="button button-secondary" id="fpd-add-image-element"><?php _e( 'Add Image Element', 'radykal' ); ?></button>
			<button class="button button-secondary" id="fpd-add-text-element"><?php _e( 'Add Text Element', 'radykal' ); ?></button>
		</div>
		<form method="post" id="fpd-submit">

			<input type="hidden" value="<?php echo $request_view_id; ?>" name="view_id" />
			<ul class="fpd-clearfix" id="fpd-elements-list">
			<?php

			$index = 0;
			if(is_array($requested_view_elements)) {
				foreach($requested_view_elements as $view_element) {
					$parameters = $view_element['parameters'];
					$elementIdentifier = $view_element['type'] == 'image' ? '<img src="'.$view_element['source'].'" />' : '<i class="fa fa-font"></i>';
					?>
					<li id="<?php echo $index; ?>"><input type="text" name="element_titles[]" value="<?php echo $view_element['title']; ?>"/><div class="fpd-element-identifier"><?php echo $elementIdentifier; ?></div><div class="fpd-clearfix"><span class="fa fa-unlock fpd-lock-element"></span><span class="fa fa-times fpd-trash-element"></span></div><textarea name="element_sources[]"><?php echo stripslashes($view_element['source']); ?></textarea><input type="hidden" name="element_types[]" value="<?php echo $view_element['type']; ?>"/><input type="hidden" name="element_parameters[]" value="<?php echo http_build_query($view_element['parameters']); ?>"/></li>


				<?php
				$index++;
				}
			}?>
			</ul>
			<p class="description"><?php _e( 'You can drag the list items to change the z-position of the associated element.', 'radykal' ); ?></p>
			<?php wp_nonce_field( 'fpd_save_elements' ); ?>
			<input type="submit" class="button button-primary" name="save_elements" value="<?php _e( 'Save Elements', 'radykal' ); ?>" />

		</form>

	</div>
	<div class="fpd-clearfix" id="fpd-edit-parameters" >
		<div style="width: 350px;">
			<h3><?php _e( 'Edit parameters for ', 'radykal' ); ?><span id="fpd-edit-parameters-for"></span></h3>
			<?php require_once('parameters-form.php'); ?>
		</div>
		<div>
			<h3 class="fpd-clearfix"><?php _e('Product Stage', 'radykal'); ?>
				<span class="description"><?php echo get_option('fpd_stage_width'); ?>px * <?php echo get_option('fpd_stage_height'); ?>px</span>
				<a class="fpd-help" data-tip="<?php _e('If you can not touch a new added element, save the elements and try again!', 'radykal'); ?>" style="float: right;"><?php _e( 'Problems?', 'radykal' ); ?></a>
			</h3>
			<div id="fpd-element-toolbar">
				<button class="button button-secondary fpd-center-horizontal" title="<?php _e( 'Center Element Horizontal', 'radykal' ); ?>"><i class="fa fa-arrows-h"></i></button>
				<button class="button button-secondary fpd-center-vertical" title="<?php _e( 'Center Element Vertical', 'radykal' ); ?>"><i class="fa fa-arrows-v"></i></button>
			</div>
			<div id="fpd-fabric-stage-wrapper">
				<canvas id="fpd-fabric-stage" width="<?php echo get_option('fpd_stage_width'); ?>" height="<?php echo get_option('fpd_stage_height'); ?>"></canvas>
			</div>
		</div>
	</div>
	<br />
</div>
<script type="text/javascript">

	jQuery(document).ready(function($) {

		var mediaUploader = null,
			$currentListItem = null,
			changesAreSaved = true,
			boundingBoxRect = null,
			$onlyForTextElements = $('.only-for-text-elements').hide(),
			$elementLists = $('#fpd-elements-list');

		var defaultObjectParams = {
			originX: 'center',
			originY: 'center',
			lockUniScaling: true,
			fontFamily: '<?php echo get_option('fpd_default_font') ? get_option('fpd_default_font') : 'Arial' ?>',
			fontSize: <?php echo get_option('fpd_default_text_size') ? get_option('fpd_default_text_size') : 18 ?>
		};

		$(".help_tip, .fpd-help").tipTip({attribute:"data-tip",fadeIn:50,fadeOut:50,delay:200});

		//make elements list sortable
		$elementLists.sortable({
			placeholder: 'ui-state-highlight',
			helper : 'clone',
			update: function(evt, ui) {
				//when item index changes, change also the z-index for the element in stage
				var newIndex = $elementLists.children('li').index(ui.item),
					element = _getElementById(ui.item.attr('id'));

				element.moveTo(newIndex);
				stage.renderAll();

				changesAreSaved = false;
			}
		});

		//enable spinner for text inputs
		var spinnerOpts = {min: 0, spin: _triggerChangeForm};
		$('#fpd-elements-form').find('input[name="x"], input[name="y"], input[name="angle"]').spinner(spinnerOpts);
		$('#fpd-elements-form').find('input[name="scale"],input[name="price"]').spinner($.extend({step: 0.01}, spinnerOpts));
		$('#boundig-box-params input').spinner(spinnerOpts);

		function _triggerChangeForm() {
			$(this).change();
		}

		//create fabricjs stage
		var stage = new fabric.Canvas('fpd-fabric-stage', {
			selection: false,
			hoverCursor: 'pointer'
		});

		//create a bounding box rectangle
		boundingBoxRect = new fabric.Rect({
			stroke: 'blue',
			strokeWidth: 1,
			fill: false,
			selectable: false,
			originX: 'center',
			originY: 'center',
			visible: false,
			evented: false,
			selectable: false,
			transparentCorners: false,
			cornerSize: 20
		});
		stage.add(boundingBoxRect);

		//fabricjs stage handlers
		stage.on({
			'mouse:down': function(opts) {
				if(opts.target == undefined) {
					_updateFormState();
				}
			},
			'object:moving': function(opts) {
				_setFormFields(opts.target);
			},
			'object:scaling': function(opts) {
				_setFormFields(opts.target);
			},
			'object:rotating': function(opts) {
				_setFormFields(opts.target);
			},
			'object:selected': function(opts) {
				_updateFormState();
				_setFormFields(opts.target.setCoords());
			},
			'text:editing:exited': function(opts) {
				$elementLists.children('li#'+opts.target.id+'').children('[name="element_sources[]"]').val(opts.target.text);
			}
		});

		//dropdown handler for choicing a view
		$('#fpd-view-switcher').change(function() {
			var $this = $(this),
				$tempOption = $this.find(':selected');


			$('#fpd-submit').attr('action', "<?php echo admin_url(); ?>edit.php?post_type=product&page=edit_fancy_products&view_id="+$this.val()+"").submit();
			if(!changesAreSaved) {
				$this.val(<?php echo $request_view_id; ?>);
				$('#fpd-submit').attr('action', "<?php echo admin_url(); ?>edit.php?post_type=product&page=edit_fancy_products&view_id=<?php echo $request_view_id; ?>");
				return false;
			}
		}).chosen({width: '400px'});

		//dropdown handler for choicing a font
		$('form#fpd-elements-form select').chosen().change(function() {
			if(stage.getActiveObject() && stage.getActiveObject().type == 'i-text') {
				stage.getActiveObject().setFontFamily(this.value);
			}
		});

		//add new element buttons handler
		$('#fpd-add-image-element, #fpd-add-text-element').click(function(evt) {
			evt.preventDefault();

			var $this = $(this);

			//enter title
			var elementTitle = prompt("<?php _e( 'Enter a title for the element:', 'radykal' ); ?>", "");

			if(elementTitle == null) {
				return false;
			}
			else if(elementTitle.length == 0) {
				alert("<?php _e( 'Please enter a title!', 'radykal' ); ?>");
				return false;
			}

			//add image
			if(this.id == 'fpd-add-image-element') {
				if (mediaUploader) {
					mediaUploader.elementTitle = elementTitle;
		            mediaUploader.open();
		            return;
		        }

		        mediaUploader = wp.media({
		            title: '<?php _e( 'Choose an element image', 'radykal' ); ?>',
		            button: {
		                text: '<?php _e( 'Set Element', 'radykal' ); ?>'
		            },
		            multiple: true
		        });

				mediaUploader.elementTitle = elementTitle;
				mediaUploader.on('select', function() {
					_addElement(
						mediaUploader.elementTitle,
						mediaUploader.state().get('selection').toJSON()[0].url,
						'image',
						{index: stage.getObjects().length-1}
					);
		        });

		        mediaUploader.open();

			}
			//add text
			else {
				_addElement(elementTitle, "<?php _e( 'Enter your text.', 'radykal' ); ?>", 'text', {index: stage.getObjects().length-1});
			}

	    });

	    //only allow numeric values for text inputs with .fpd-only-numbers
	    $('form#fpd-elements-form').on('keypress', 'input.fpd-only-numbers', function(evt) {

			var charCode = (evt.which) ? evt.which : evt.keyCode;
			if($(this).hasClass('fpd-allow-dots')) {
				if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode != 46)) {
				    return false;
			    }
			    else {
				    return true;
			    }
			}
			else {
				if (charCode > 31 && (charCode < 48 || charCode > 57)) {
				    return false;
			    }
			    else {
				    return true;
				}
			}

	    });

		$('.fpd-allow-dots').keyup(function(){

	        if($(this).val().indexOf('.')!=-1){
	            if($(this).val().split(".")[1].length > 2){
	                if( isNaN( parseFloat( this.value ) ) ) return;
	                this.value = parseFloat(this.value).toFixed(2);
	            }
	         }
	         return this;

	    });


		//form change handler
		$('form#fpd-elements-form').on('change', function(evt) {

			if($('input[name="bounding_box_control"]').is(':checked')) {
				//get bounding box from other element
				_updateBoundingBox($('input[name="bounding_box_by_other"]').val());
			}
			else {
				_updateBoundingBox({
					x: $('input[name="bounding_box_x"]').val(),
					y: $('input[name="bounding_box_y"]').val(),
					width: $('input[name="bounding_box_width"]').val(),
					height: $('input[name="bounding_box_height"]').val()
				});
			}

			_setParameters();

		});

		 //update fabric element
		$('#fpd-elements-form').on('change', '.fpd-only-numbers, select', function() {

			_updateFabricElement();

		});

		$('input[name="bounding_box_control"]').change(function() {
			boundingBoxRect.visible = false;
			stage.renderAll();
			if($(this).is(':checked')) {
				$('#boundig-box-params').hide();
				$('input[name="bounding_box_by_other"]').show().val('');
			}
			else {
				$('#boundig-box-params').show().children('input').val('');
				$('input[name="bounding_box_by_other"]').hide();
			}
		});

		//when select a list item, select the corresponding element in stage
		$elementLists.on('click', '.fpd-element-identifier', function(evt) {
			stage.setActiveObject(_getElementById($(this).parent('li:first').attr('id')));
		});

		//element lock handler
		$elementLists.on('click', '.fpd-lock-element', function(evt) {
			var $this = $(this),
				element = _getElementById($this.parents('li:first').attr('id'));

			//lock
			if($this.hasClass('fa-unlock')) {
				$this.removeClass('fa-unlock').addClass('fa-lock');
				element.set('evented', false);
			}
			//unlock
			else {
				$this.removeClass('fa-lock').addClass('fa-unlock');
				element.set('evented', true);
			}

			stage.discardActiveObject();

			_updateFormState();
		});

		//remove element
		$elementLists.on('click', '.fpd-trash-element', function() {
			var c = confirm("<?php _e('Remove element?', 'radykal'); ?>");
			if(!c) {
				return false;
			}

			var $this = $(this);

			_getElementById($(this).parents('li').remove().attr('id')).remove();
			boundingBoxRect.visible = false;
			stage.discardActiveObject().renderAll();

			_updateFormState();

			changesAreSaved = false;
		});

		//let the page know that elements are now saved
		$('input[name="save_elements"]').click(function() {
			stage.discardActiveObject();
			changesAreSaved = true;
		});

		$('.fpd-center-horizontal, .fpd-center-vertical').click(function() {
			var currentElement = stage.getActiveObject();
			if(currentElement) {
				if($(this).hasClass('fpd-center-horizontal')) {
					currentElement.centerH();
				}
				else {
					currentElement.centerV();
				}
				_setFormFields(currentElement);
			}
		});

		//set color for text elements
		$('input[name="colors"]').keyup(function() {

			var currentElement = stage.getActiveObject();
			if(currentElement.type == 'i-text') {
				var colorInput = _getFirstHexOfString(this.value);
				if(colorInput) {
					currentElement.setFill(colorInput);
				}
				else if(this.value == '') {
					currentElement.setFill('#000');
				}
			}

		});

		//check if changes are saved before page unload
		$(window).on('beforeunload', function () {
			if(!changesAreSaved) {
				return '<?php _e('You have not save your changes!', 'radykal'); ?>';
			}
		});

		//add a new element to stage
		function _addElement(title, source, type, params) {

			$.extend(params, defaultObjectParams);

			//new element
			if(params.left == undefined) {
				changesAreSaved = false;
				params.id = String(new Date().getTime());

				var elementIdentifier,
					paramsStr;
				if(type == 'image') {
					elementIdentifier = '<img src="'+source+'" />';
				}
				else {
					elementIdentifier = '<i class="fa fa-font"></i>';
				}

				$elementLists.append('<li id="'+params.id+'"><input type="text" name="element_titles[]" value="'+title+'"/><div class="fpd-element-identifier">'+elementIdentifier+'</div><div class="fpd-clearfix"><span class="fa fa-unlock fpd-lock-element"></span><span class="fa fa-times fpd-trash-element"></span></div><textarea name="element_sources[]">'+source+'</textarea><input type="hidden" name="element_types[]" value="'+type+'"/><input type="hidden" name="element_parameters[]" value=""/></li>');
			}

			if(type == 'image') {
				new fabric.Image.fromURL(source, function(fabricImg) {
					_addElementToStage(fabricImg, params);
				}, params);
			}
			else {
				//replace underscore with space again
				if(params.font) params.fontFamily = params.font;
				var color = _getFirstHexOfString(params.colors);
				if(color) {
					params.fill = color;
				}
				var fabricText = new fabric.IText(source.replace(/\\n/g, '\n'), params);
				_addElementToStage(fabricText, params);
			}

		}

		//set element params and create list item for it
		function _addElementToStage(element, params) {

			stage.add(element);

			if(params.left == undefined) {
				//new element is added
				element.center();
				stage.setActiveObject(element);
			}

			element.moveTo(params.index);
			stage.renderAll();
			element.setCoords();

		}

		//enable editing of the form when an element is selected in stage
		function _updateFormState() {

			if(stage.getActiveObject() && stage.getActiveObject().selectable) {
				$('form#fpd-elements-form input').attr("disabled", false);
				$elementLists.children('li').removeClass('fpd-active-item');
				$currentListItem = $elementLists.children('#'+stage.getActiveObject().id).addClass('fpd-active-item');
				$('#fpd-edit-parameters-for').text($currentListItem.children('input[name="element_titles[]"]').val());
				if(stage.getActiveObject().type == 'i-text') {
					$('#fpd-elements-form .fpd-text-element-params').attr("disabled", false);
					$('#fpd-elements-form input[name="patternable"]').attr("disabled", false);
					$('#fpd-elements-form input[name="textEditable"]').attr("disabled", false);
					$onlyForTextElements.show();
				}
				else {
					$('#fpd-elements-form .fpd-text-element-params').attr("disabled", true);
					$('#fpd-elements-form input[name="patternable"]').attr("disabled", true);
					$('#fpd-elements-form input[name="textEditable"]').attr("disabled", true);
					$onlyForTextElements.hide();
				}

				$('#fpd-elements-form .fpd-text-element-params').trigger('chosen:updated');
			}
			else {
				$('form#fpd-elements-form input, form#fpd-elements-form select').attr("disabled", true).trigger('chosen:updated');
				$elementLists.children('li').removeClass('fpd-active-item');
				boundingBoxRect.visible = false;
				$currentListItem = null;
			}

		}

		//update form fields when element is changed via product stage
		function _setFormFields(element) {

			$('input[name="x"]').val(Math.round(element.left) || 0);
			$('input[name="y"]').val(Math.round(element.top) || 0);
			$('input[name="scale"]').val(Number(element.scaleX).toFixed(2));
			$('input[name="angle"]').val(Math.round(element.angle) % 360);

			var paramsFromInput = $currentListItem.children('input[name="element_parameters[]"]').val(),
				splitParams = paramsFromInput.split("&");

			//convert parameter string into object
			var paramsObject = {};
			for(var i=0; i < splitParams.length; ++i) {
				var splitIndex = splitParams[i].indexOf("=");
				paramsObject[splitParams[i].substr(0, splitIndex)] = splitParams[i].substr(splitIndex+1)  ;
			}

			$('input[name="price"]').val(paramsObject.price);

			$('input[name="removable"]').prop('checked', paramsObject.removable == '1');
			$('input[name="draggable"]').prop('checked', paramsObject.draggable == '1');
			$('input[name="rotatable"]').prop('checked', paramsObject.rotatable == '1');
			$('input[name="resizable"]').prop('checked', paramsObject.resizable == '1');
			$('input[name="zChangeable"]').prop('checked', paramsObject.zChangeable == '1');
			$('input[name="patternable"]').prop('checked', paramsObject.patternable == '1');
			$('input[name="textEditable"]').prop('checked', paramsObject.textEditable == '1');

			$('input[name="colors"]').val(paramsObject.colors ? unescape(paramsObject.colors) : '');

			boundingBoxRect.visible = false;
			stage.renderAll();

			$('input[name="bounding_box_control"]').prop('checked', paramsObject.bounding_box_control == '1');
			if(paramsObject.bounding_box_control == '1') {
				$('#boundig-box-params').hide();
				$('input[name="bounding_box_by_other"]').show().val(paramsObject.bounding_box_by_other);
				if(paramsObject.bounding_box_by_other) {
					_updateBoundingBox(paramsObject.bounding_box_by_other);
				}
			}
			else {
				$('#boundig-box-params').show();
				$('input[name="bounding_box_by_other"]').hide();
				$('input[name="bounding_box_x"]').val(paramsObject.bounding_box_x);
				$('input[name="bounding_box_y"]').val(paramsObject.bounding_box_y);
				$('input[name="bounding_box_width"]').val(paramsObject.bounding_box_width);
				$('input[name="bounding_box_height"]').val(paramsObject.bounding_box_height);
				_updateBoundingBox({x: paramsObject.bounding_box_x, y: paramsObject.bounding_box_y, width: paramsObject.bounding_box_width, height: paramsObject.bounding_box_height});
			}

			if(element.type == 'i-text') {
				$('select[name="font"]').val(element.fontFamily).trigger('chosen:updated');
			}

			element.setCoords();

			_setParameters();
		}

		//update element in product stage when form fields are changed
		function _updateFabricElement() {

			if(stage.getActiveObject()) {
				var currentElement = stage.getActiveObject();


				currentElement.set({
					left: $('input[name="x"]').val(),
					top: $('input[name="y"]').val(),
					angle: $('input[name="angle"]').val(),
					scaleX: $('input[name="scale"]').val(),
					scaleY: $('input[name="scale"]').val()
				});

				currentElement.setCoords();
				stage.renderAll();
			}

		}

		function _updateBoundingBox(target) {
			//set by another element
			if(typeof target == 'string') {
				var targetElement = _getElementByTitle(target);
				if(targetElement) {
					boundingBoxRect.left = targetElement.getLeft();
					boundingBoxRect.top = targetElement.getTop();
					boundingBoxRect.width = targetElement.getWidth();
					boundingBoxRect.height = targetElement.getHeight();
					boundingBoxRect.visible = true;
				}
				else {
					boundingBoxRect.visible = false;
				}
			}
			//set by custom parameters
			else {
				boundingBoxRect.left = parseInt(target.x);
				boundingBoxRect.top = parseInt(target.y);
				boundingBoxRect.width = parseInt(target.width);
				boundingBoxRect.height = parseInt(target.height);
				boundingBoxRect.visible = true;
			}
			boundingBoxRect.setCoords();
			stage.renderAll();
		}

		//set parameters from form to the current list item
		function _setParameters() {
			var serializedForm = $('#fpd-elements-form').serialize();
			$currentListItem.children('input[name="element_parameters[]"]').val(serializedForm.replace(/\+/g, '_'));

			changesAreSaved = false;
		}

		function _getElementById(id) {
			var objects = stage.getObjects();
			for(var i=0; i < objects.length; ++i) {
				if(objects[i].id == id) {
					return objects[i];
					break;
				}
			}
		}

		function _getElementByTitle(title) {
			var objects = stage.getObjects();
			for(var i=0; i < objects.length; ++i) {
				if(objects[i].title == title) {
					return objects[i];
					break;
				}
			}
			return false;
		}

		function _parameterStringToObject(paramStr) {

			var splitParams = paramStr.split("&");

			//convert parameter string into object
			var paramsObject = {};
			for(var i=0; i < splitParams.length; ++i) {
				var splitIndex = splitParams[i].indexOf("=");
				paramsObject[splitParams[i].substr(0, splitIndex)] = splitParams[i].substr(splitIndex+1).replace(/\_/g, ' ');
			}
			return paramsObject;

		};

		function _getFirstHexOfString(str) {

			str = unescape(str);
			if(str == '' || str.indexOf('#') == -1) {
				return false;
			}
			else {
				var hexStr = str.indexOf(',') == -1 ? str : str.substr(0, str.indexOf(','));
				return /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(hexStr) ? hexStr : false;
			}

		};


		$elementLists.children('li').each(function(index, item) {
			var $item = $(item),
				title = $item.find('input[name="element_titles[]"]').val(),
				source = $item.find('textarea[name="element_sources[]"]').val(),
				type = $item.find('input[name="element_types[]"]').val(),
				parameters = $item.find('input[name="element_parameters[]"]').val();

			var params = _parameterStringToObject(parameters);
			params.left = parseInt(params.x);
			params.top = parseInt(params.y);
			params.angle = parseInt(params.angle);
			params.scaleX = params.scaleY = Number(params.scale);
			params.title = title;
			params.index = index;
			params.id = $item.attr('id');

			_addElement(title, source, type, params);
		});

		_updateFormState();
		stage.renderAll();

	});
</script>

