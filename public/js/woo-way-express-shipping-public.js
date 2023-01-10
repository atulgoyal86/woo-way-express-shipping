(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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

    $(document).ready(function(){
        
        var initialBCountry = $('.woocommerce-checkout #billing_country').val();
        var initialSCountry = $('.woocommerce-checkout #shipping_country').val();
        console.log(initialSCountry);
         // 1. On Start (after Checkout is loaded)
         showHideFields(initialBCountry, 'billing');
         showHideFields(initialSCountry, 'shipping');

         // 2. Live: On Country change event
        $('body').on( 'change', 'select#billing_country', function(){
            showHideFields($(this).val(), 'billing');
        });
        $('body').on( 'change', 'select#shipping_country', function(){
            showHideFields($(this).val(), 'shipping');
        });

        // 3. Live: On City change event for "Saudi Arabia" country
        $('body').on( 'change', 'select#billing_city_woowes', function(){
            $('input#billing_city').val($(this).val());
       });
       $('body').on( 'change', 'select#shipping_city_woowes', function(){
            $('input#shipping_city').val($(this).val());
       });
    });

    function showHideFields( country, fieldset ) {
        var targetedCountry = 'SA';
        var select2Classes = ''; //'country_select select2-hidden-accessible';

        if( country === targetedCountry ) {
            $('#'+fieldset+'_city_woowes_field').removeClass('hidden');
            $('#'+fieldset+'_city_field').addClass('hidden');
            $('select#'+fieldset+'_city_woowes').addClass(select2Classes);
        } else if( country !== targetedCountry && $('#'+fieldset+'_city_field').hasClass('hidden') ) {
            $('#'+fieldset+'_city_woowes_field').addClass('hidden');
            $('#'+fieldset+'_city_field').removeClass('hidden');
            $('select#'+fieldset+'_city_woowes').removeClass(select2Classes);
        }
    }
    // $(document).ready(function(){
    //     var country_ = $('.woocommerce-checkout #billing_country').val();
    //     woowes_manage_city_field( country_);
    // });

    // $(document).on('change','.woocommerce-checkout #billing_country', function(){
    //     var country_ = $(this).val();

    //     woowes_manage_city_field( country_);
    // });

    // function woowes_manage_city_field( country__){
    //     if(country__ == 'SA'){
    //         $('.woowes_city_field_div').removeClass('woowes_city_field_hide');
    //         $('.woowes_city_field_div .woowes_city').prop('required', true);
    //         $('.woowes_city_field_div label').html('Select City <abbr class="required" title="required">*</abbr>');
    //     }else{
    //         $('.woowes_city_field_div').addClass('woowes_city_field_hide');
    //         $('.woowes_city_field_div .woowes_city').prop('required', false);
    //     }
    // }
})( jQuery );
