var postLocation = (location.hostname.indexOf("local") > -1) ? location.protocol+'//local.my'+location.hostname.replace('local','') : location.protocol+'//my.'+location.hostname;

$( document ).ready(function() {
	$( "#signup" ).click(function( event ) {
		$("#myLogin").hide(); 
		$("#myRegister").show();
	});
	$('.back-to-login').click(function() {
		$('#myReset').hide();
		$('#myLogin').show();
	});
	$('.lost-password-btn').click(function() {
		$('#myLogin').hide();
		$('#myReset').show();
		/*
var selectedButton = $(this);
		var displayElement = $(this).data('target');
		var modalHeight = $(displayElement).height();

		$('.modal-body > div').animate({height:modalHeight});
		$('.modal-body > div > div').not(displayElement).fadeOut(400, function() {
			$(displayElement).fadeIn(600);
		});
		$(selectedButton).fadeOut(400, function() {
			$('.modal-body > a').not(this).fadeIn(600);
		});
*/
	});

	var registrationPopup = function() {
		$('#logIn').find('h4').html('Sign Up Now!');
		$('#myLogin').css('display', 'none');
		$('#myReset').css('display', 'none');
		$('#myRegister').css('display', 'block');
		$('.reset').removeClass('active');
	};
	var loginPopup = function() {
		$('#logIn').find('h4').html('Saltsha Login');
		$('#myLogin').css('display', 'block');
		$('#myReset').css('display', 'none');
		$('#myRegister').css('display', 'none');
		$('.reset').addClass('active');
	};


	$('.menu li a[title=log-in]').click(function() {
		var menuItemContent = $(this).text();

		if (menuItemContent == 'Sign Up') {	
			registrationPopup();
		};
	});
		$('.menu li a[title=log-in]').click(function() {
		var menuItemContent = $(this).text();

		if (menuItemContent == 'Log In') {	
			loginPopup();
		};
	});

	$('.signUpButton a').click(function() {
		registrationPopup();
	});

	$('#loginform').submit(function(e) {
	
		//Prevent form from submitting.
		e.preventDefault();
		
		//Show system response on event.
		toggleLogin('lock');
		
		//Get username and password credientials.
		var username	= $('#user_login').val();
		var password	= $('#user_pass').val();
		
		//Remove this after it has been verified on dev/stage environments.
		console.log(postLocation+"/wp-content/themes/ppmlayout/ajax-login.php");
		
		//Submit credientials to ajax-login.php on my.saltsha
		$.ajax({
			type: "POST",
			url: postLocation+"/wp-content/themes/ppmlayout/ajax-login.php",
			data: { log: username, pwd: password },
			dataType: 'json',
			success: function(response) {
				
				//Check if authentication was successful.
				if( response.status == 'success' ) {
				
					//Valid login! Clear submit handler, adjust form action, and submit form.
					$('#loginform').unbind('submit').attr('action', postLocation+'/wp-login.php').submit();
							
				}
				else {
				
					//Invalid login! Reset button, notify user of invalid login.
					$('#loginform .error-messages').html('<span class="error">Invalid username and/or password. Please verify your username and password and try again.</span>');
					toggleLogin('free');
					
				}
				
			},
			error: function(response) {

				// This means that the response was not in JSON, which means
				// that (likely) it was HTML of the authy page, so send them
				// to the authy page to finish authentication.
				window.location = postLocation+'/wp-login.php';
				
			}
		});
		
	});
});

function handleResetAttempt( response_type, response_message ) {

	toggleLogin('free');

	if( response_type == 'failed' ) {
		$('.error-messages').html(response_message);
	} else if ( response_type == 'success' ) {
		$('.success-messages').html(response_message);
	}
	
}

function toggleLogin(state) {
	
	if( state == 'free' ) {
		$('#wp-submit').val('Log In');
		$('#email').removeAttr('disabled');
	} else {
		$('#wp-submit').val('Logging In...');
	}
}

(function($,W,D)
{
    var JQUERYreset = {};

    JQUERYreset.UTIL =
    {
        setupFormValidation: function()
        {
            //form validation rules
            $("#reset-form").validate({
                rules: {
                   email: {
                        required: true,
                        email: true
                    }
                 },
                messages: {
                    email: "Please enter a valid email address"
                   
                },  
                submitHandler: function(form) {  
                    
	              email=$('#email').val();
		
				  // Disable user/pass boxes, make full width login, hide forgot password
				  $('#email').attr('disabled', 'disabled');
				  $('#reset-button').hide();
				  $('#login-submit').css('width', '239px');
				  $('#login-submit').html('Logging In...');
				  
				  var loginAPI = postLocation+'/reset-password/?email='+email+'&callback=?';
				  $.getJSON( loginAPI, {
				  	crossDomain: true,
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