var account_information	= {};

var FormWizard = function () {


    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

            // default form wizard
            $('#account-setup').bootstrapWizard({
            
                'nextSelector': '.button-next',
                'previousSelector': '.button-previous',
                
                onTabClick: function (tab, navigation, index) {
                	//Add validation
					return true;
                },
                onNext: function (tab, navigation, index) {
                
                    var total = navigation.find('li').length;
                    var current = index + 1;
                   
                    // set wizard title
                    $('.step-title', $('#account-setup')).text('Step ' + (index + 1) + ' of ' + total);
                   
                    // set done steps
                    jQuery('li', $('#account-setup')).removeClass("done");
                    var li_list = navigation.find('li');
                    for (var i = 0; i < index; i++) {
                        jQuery(li_list[i]).addClass("done");
                    }

                    if (current == 1) {
                        $('#account-setup').find('.button-previous').hide();
                    } else {
                        $('#account-setup').find('.button-previous').show();
                    }

                    if (current >= total) {
                    	
						//Build object and confirm.
						account_information.company		= $('#company').val();
						account_information.type		= $('#type').val();
						account_information.first_name	= $('#first_name').val();
						account_information.last_name	= $('#last_name').val();
						account_information.email		= $('#email').val();
						account_information.address_1	= $('#address_1').val();
						account_information.address_2	= $('#address_2').val();
						account_information.zip			= $('#zip').val();
						account_information.city		= $('#city').val();
						account_information.phone		= $('#phone').val();
						account_information.google_id	= $('#google_id').val();
						account_information.state		= $('#state').val();
						
						$('#confirm_company').html(account_information.company);
						$('#confirm_type').html(document.getElementById('type')[account_information.type].text);
						$('#confirm_first_name').html(account_information.first_name);
						$('#confirm_last_name').html(account_information.last_name);
						$('#confirm_email').html(account_information.email);
						$('#confirm_zip').html(account_information.zip);
						$('#confirm_city').html(account_information.city);
						$('#confirm_address_1').html(account_information.address_1);
						$('#confirm_address_2').html(account_information.address_2);
						$('#confirm_google_id').html(account_information.google_id);
						$('#confirm_phone').html(account_information.phone);
						$('#confirm_state').html(account_information.state);
						
                        $('#account-setup').find('.button-next').hide();
                        $('#account-setup').find('.button-submit').show();

                    } else {
                        $('#account-setup').find('.button-next').show();
                        $('#account-setup').find('.button-submit').hide();
                    }
                    
                    App.scrollTo($('.page-title'));
                },
                onPrevious: function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    // set wizard title
                    $('.step-title', $('#account-setup')).text('Step ' + (index + 1) + ' of ' + total);
                    // set done steps
                    jQuery('li', $('#account-setup')).removeClass("done");
                    var li_list = navigation.find('li');
                    for (var i = 0; i < index; i++) {
                        jQuery(li_list[i]).addClass("done");
                    }

                    if (current == 1) {
                        $('#account-setup').find('.button-previous').hide();
                    } else {
                        $('#account-setup').find('.button-previous').show();
                    }

                    if (current >= total) {
                        $('#account-setup').find('.button-next').hide();
                        $('#account-setup').find('.button-submit').show();
                    } else {
                        $('#account-setup').find('.button-next').show();
                        $('#account-setup').find('.button-submit').hide();
                    }

                    App.scrollTo($('.page-title'));
                },
                onTabShow: function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    var $percent = (current / total) * 100;
                    $('#account-setup').find('.bar').css({
                        width: $percent + '%'
                    });
                }
            });

            $('#account-setup').find('.button-previous').hide();
            
            $('#account-setup .button-submit').click(function () {
				console.log("Submit form.");
            }).hide();
         	   
        }

    };

}();

$(function() {
	FormWizard.init();
});