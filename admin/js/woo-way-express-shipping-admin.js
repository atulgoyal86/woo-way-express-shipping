(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

    $(document).on('click', '#woowes_settings_auth_login', function(){
        console.log('fhjshk');
        $('.woowes_settings_error').remove();
        var username_ = $('#woowes_settings_auth_username').val();
        var password_ = $('#woowes_settings_auth_password').val();
        var valid__ = true;
        if(username_ == ''){
            valid__ = false;
            $('#woowes_settings_auth_username').after('<span class="woowes_settings_error" style="color:red">Please Enter Username</span>');
        }
        if(password_ == ''){
            valid__ = false;
            $('#woowes_settings_auth_password').after('<span class="woowes_settings_error" style="color:red">Please Enter Password</span>');
        }

        if(valid__){
            $.ajax({
                url: woowes_ajax_object.ajaxurl,
                data: {action:'woowes_auth_generate_token',username_:username_,password_:password_},                         
                type: 'post',
                success: function(php_script_response){
					
					console.log(php_script_response);
					if(php_script_response.status == 0){
						$('#woowes_settings_auth_username').before('<span class="woowes_settings_error" style="color:green;display: block;">'+php_script_response.messageEn+'</span>');
						$('#woowes_settings_auth_token').val(php_script_response.data.access_token);
						$('#woowes_settings_submit').click();
					}
					else{
						$('#woowes_settings_auth_username').before('<span class="woowes_settings_error" style="color:red;display: block;">'+php_script_response.messageEn+'</span>');
                        $('#woowes_settings_auth_token').val('');
                    }
                }
             });
        }
    });

    $(document).on('change', '#woowes_setting_options_test_mode', function(){
        $('#woowes_settings_auth_username').val('');
        $('#woowes_settings_auth_password').val('');
        $('#woowes_settings_auth_token').val('');
        $('#woowes_settings_submit').click();
    });

})( jQuery );
