jQuery(document).ready(function () {
	 if(window.location.href.indexOf("shop") == -1){
	
	 	if($.cookie("popup") != 1 && user_logged_in == false) {
		jQuery(window).load(function () {
			jQuery('#myModal').modal('show');
		});
		} 
	}

	jQuery( "#no-thanks" ).click(function() {
		$.cookie('popup', 1, { expires: 1 });
  	});

	jQuery( "#reset-button" ).click(function() {
		jQuery("#login").addClass( "hide" );
		jQuery("#reset").removeClass("hide");
  	});

	jQuery( "#reset-button" ).click(function() {
		jQuery("#login").addClass( "hide" );
		jQuery("#reset").removeClass("hide");
  	});
  	jQuery( "#reset-button-off" ).click(function() {
		jQuery("#reset").addClass( "hide" );
		jQuery("#login").removeClass("hide");
  	});
});

function toggleLogin() {
	if ($('#reset-button').is(':visible')) {
		$('button,input').attr('disabled', 'disabled');
		$('#reset-button').hide();
		$('#login-submit').css('width', '239px');
		$('#login-submit').html('Logging In...');
	} else {
		$('button,input').removeAttr('disabled');
		$('#reset-button').show();
		$('#login-submit').css('width', 'auto');
		$('#login-submit').html('Login <i class="m-icon-swapright m-icon-white"></i>');
	}
}
function handleResetAttempt( response_type, response_message ) {
	
	if( response_type == 'failed' ) {
		
		$('#reset-form .error-messages').html(response_message);
		toggleLogin();
		
	}
	else if( response_type == 'success' ) {
		$('.success-messages').html(response_message);
		toggleLogin();
		jQuery("#reset").addClass( "hide" );
		jQuery("#login").removeClass("hide");
	}
	
}
(function($,W,D){
    var JQUERYreset = {};

    JQUERYreset.UTIL = {
        setupFormValidation: function(){
            //form validation rules
            $("#reset-form").validate({
                rules: {
                   email: {
                        required: true,
                        email: true
                    },
                 },
                messages: {
                    email: "Please enter a valid email address"
        
                },
                submitHandler: function(form) {  
                    
	              email=$('#email').val();
		
				  // Disable user/pass boxes, make full width login, hide forgot password
				  $('button,input').attr('disabled', 'disabled');
				  $('#reset-button').hide();
				  $('#login-submit').css('width', '239px');
				  $('#login-submit').html('Logging In...');
				  location.protocol+'//'+location.hostname+"/wp-content/themes/ppmlayout"
				  var resetAPI = "/reset-password/?email="+email+"&callback=?";
				  $.getJSON( resetAPI, {
				  	crossDomain: false,
				    format: "json",
				    jsonpCallback: handleResetAttempt
				  })

                }
            });
        }
    }
    //when the dom has loaded setup form validation rules
    $(D).ready(function($) {
        JQUERYreset.UTIL.setupFormValidation();
    });
    
    })(jQuery, window, document);
    (function($,W,D){

    var JQUERYlogin = {};

    JQUERYlogin.UTIL = {
        setupFormValidation: function(){
            //form validation rules
            $("#login-form").validate({
				rules: {
                    log: "required",
                    lastname: "required",
                    pwd: {
                        required: true,
                        minlength: 5
                    }
                },
                messages: {
                    firstname: "Please Enter Your Username or Email",
                    
                    pwd: {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 5 characters long"
                    },
                    },
                    submitHandler: function(form1) {

					username=$("#log").val();
					password=$("#pwd").val();
					
					// Disable user/pass boxes, make full width login, hide forgot password
					$('button,input').attr('disabled', 'disabled');
					$('#reset-button').hide();
					$('#login-submit').css('width', '100%');
					$('#login-submit').html('Logging In...');
					
					var do_login = $.getJSON( location.protocol+'//'+location.hostname+"/wp-content/themes/ppmlayout/ajax-login.php?log="+username+"&pwd="+password, function(data) {
					
						if (data.error) {
							$('#login-form .error-messages').html(data.error);
							toggleLogin();
						} else {
							window.location.reload();
						}
					}).fail(function(error) {
					
						if (error.responseText.indexOf("authy") > -1) {
							window.location = location.protocol+'//'+location.hostname+'/wp-admin/';
						} else {
							$('#login-form .error-messages').html(error.responseText);
							toggleLogin();
						}
					});
                	
                
                }
            });
        }
    }

    //when the dom has loaded setup form validation rules
    $(D).ready(function($) {
        JQUERYlogin.UTIL.setupFormValidation();
    });

})(jQuery, window, document);