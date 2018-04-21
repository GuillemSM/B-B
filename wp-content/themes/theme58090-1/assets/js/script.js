jQuery( document ).ready(function() {

	// ---------------------------------------------------------
	// Back to Top
	// ---------------------------------------------------------
	jQuery( window ).scroll(function() {

		if ( jQuery( this ).scrollTop() > 100 ) {
			jQuery( '#back-top' ).addClass( 'show-totop' );
		} else {
			jQuery( '#back-top' ).removeClass( 'show-totop' );
		}
	});

	jQuery( '#back-top a' ).click(function() {
		jQuery( 'body,html' ).stop( false, false ).animate({
			scrollTop: 0
		}, 800 );
		return false;
	});
});

jQuery(document).ready(function(){
	var staticSearch = jQuery( '.static-search-form' ),
		search = jQuery( '.search-form', staticSearch ),
		searchField = jQuery( '.search-field', staticSearch ),
		submit = jQuery( '.search-submit', staticSearch );

	function open(event) {
		jQuery(this).off('click', open);
		searchField.focus();
		search.addClass('search-open');
		search.removeClass('search-hide');
		event.preventDefault();
	}

	function addEvent() {
		submit.on('click', open);
		searchField.blur();
		search.removeClass('search-open');
		search.addClass('search-hide');
	}

	function stopPropagation(event){
		event.stopPropagation();
	}

	search.on('click touchstart touchend', stopPropagation);
	submit.on('click touchstart touchend', stopPropagation);
	jQuery(document).on('click.hideSearch touchstart touchend', addEvent).triggerHandler('click.hideSearch');
});
