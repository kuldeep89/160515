jQuery(document).ready(function($) {

	var $designsParamsBBControl = $('#fpd_designs_parameter_bounding_box_control');
	toggleBetweenElements(
		$designsParamsBBControl,
		$designsParamsBBControl.parents('tbody').find('.fpd-bounding-box-target-input').parents('tr'),
		$designsParamsBBControl.parents('tbody').find('.fpd-bounding-box-custom-input').parents('tr')
	);

	var $textParamsBBControl = $('#fpd_custom_texts_parameter_bounding_box_control');
	toggleBetweenElements(
		$textParamsBBControl,
		$textParamsBBControl.parents('tbody').find('.fpd-bounding-box-target-input').parents('tr'),
		$textParamsBBControl.parents('tbody').find('.fpd-bounding-box-custom-input').parents('tr')
	);

	toggleBetweenElements(
		$('#fpd_upload_designs'),
		$('input[name="fpd_type_of_uploader"]').parents('tr').add($('#fpd_max_image_size').parents('tr'))
	);

	function toggleBetweenElements($switcher, $groupOne, $groupTwo) {
		$switcher.change(function() {
			if($switcher.is(':checked')) {
				$groupOne.show();
				if($groupTwo) {
					$groupTwo.hide();
				}
			}
			else {
				$groupOne.hide();
				if($groupTwo) {
					$groupTwo.show();
				}
			}
		}).change();

	}

	$('input[name="fpd_type_of_uploader"]').change(function() {

		var $radios = $('input[name="fpd_type_of_uploader"]:checked'),
			$phpUploaderElements = $('[name="fpd_max_image_size"],[name="fpd_upload_designs_php_logged_in"]').parents('tr');

		if($radios.val() == 'filereader') {
			$phpUploaderElements.hide();
		}
		else {
			$phpUploaderElements.show();
		}

	}).change();
});

var openModal = function( $modalWrapper ) {

	$modalWrapper.stop().fadeIn(300);
	jQuery('body').addClass('modal-open');

};

var closeModal = function( $modalWrapper ) {

	$modalWrapper.stop().fadeOut(200);
	jQuery('body').removeClass('modal-open');

};