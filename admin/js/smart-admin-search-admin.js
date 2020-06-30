(function( $ ) {
	'use strict';

	$(function() {

		/*
		 * ELEMENTS
		 */
		let sasDocument          = $( document );
		let sasSearchModal       = $( '.sas-search-modal' );
		let sasSearchModalSelect = $( '.sas-search-modal__select' );
		
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
		 * SEARCH MODAL
		 */
		function showSearchModal() {
			sasSearchModal.css( 'display', 'block' );
			
			sasSearchModalSelect.select2( {
				dropdownParent: sasSearchModal,
				width         : '100%',
				placeholder   : "what are you looking for?",
			} );
			
			setTimeout( function() {
				sasSearchModalSelect.select2( 'open' );
			}, 300 );
		}
		
		function hideSearchModal() {
			sasSearchModal.css( 'display', 'none' );
			
			sasSearchModalSelect.select2( 'destroy' );
		}
		
		sasSearchModal.on( 'click', function( e ) {
			if ( e.target === this ) {
				hideSearchModal();
			}
		} );

	});

})( jQuery );
