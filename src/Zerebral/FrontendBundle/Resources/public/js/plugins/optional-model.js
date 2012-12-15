(function( $ ){

    var methods = {
        init : function( options ) {
            this.find('.show-input').on('click', $.proxy(methods.showInput, this));
            this.find('.show-dropdown').on('click',  $.proxy(methods.showDropdown, this));
        },

        showInput: function(e){
            e.preventDefault();

            $(this).closest('.controls').find('input').removeAttr('disabled').show();
            $(this).find('.show-dropdown').show();
            $(this).find('.show-input').hide();
            $(this).closest('.controls').find('select').attr('disabled', 'disabled').hide();
        },

        showDropdown: function(e){
            e.preventDefault();
            $(this).closest('.controls').find('input').attr('disabled', 'disabled').hide();
            $(this).find('.show-input').show();
            $(this).find('.show-dropdown').hide();
            $(this).closest('.controls').find('select').removeAttr('disabled').show();
        }
    };

    $.fn.optionalModel = function( method ) {
        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.optionalModel' );
        }
    };

})( jQuery );