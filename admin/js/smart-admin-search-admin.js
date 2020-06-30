(function( $ ) {
	'use strict';

	$(function() {

		/*
		 * ELEMENTS
		 */
		let sasDocument = $( document );
		let sasSearchModal = $( '.sas-search-modal' );
		
		/*
		 * KEY PRESS
		 */
		// Array of pressed keys.
		let pressedKeys = [];
		
		// Check the pressed keys.
		sasDocument.on( 'keydown', function( e ) {
			if ( pressedKeys.includes( e.which ) === false ) {
				pressedKeys.push( e.which );
			}
			
			if ( pressedKeys.includes( 16 ) && pressedKeys.includes( 17 ) && pressedKeys.includes( 70 ) ) {
				// Keys: SHIFT (16), CTRL (17), F (70).
				showSearchModal();
			} else if ( pressedKeys.includes( 27 ) ) {
				// Keys: ESC (27).
				hideSearchModal();
			}
		} );
		
		// Reset pressed keys array.
		sasDocument.on( 'keyup', function( e ) {
			pressedKeys = [];
		} );
		
		/*
		 * MODAL
		 */
		function showSearchModal() {
			sasSearchModal.css( 'display', 'block' );
		}
		
		function hideSearchModal() {
			sasSearchModal.css( 'display', 'none' );
		}
		
		sasSearchModal.on( 'click', function() {
			hideSearchModal();
		} );

	});

})( jQuery );
