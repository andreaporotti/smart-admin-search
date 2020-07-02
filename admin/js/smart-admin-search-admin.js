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
		function formatSearchResult( result ) {
			if( result.loading ) {
				return result.text;
			}
			
			let template = $(
				'<div class="result-item">' +
					'<div class="name">' + result.text + '</div>' + 
					'<div class="description">' + result.description + '</div>' +
				'</div>'
			);
			
			return template;
		}
		
		function formatSearchResultSelection( result ) {
			if ( result.id === '' ) {
				return sas_strings.search_select_placeholder;
			}
	
			return result.text;
		}
		
		function showSearchModal() {
			sasSearchModal.css( 'display', 'block' );
			
			sasSearchModalSelect.select2( {
				dropdownParent    : sasSearchModal,
				width             : '100%',
				placeholder       : sas_strings.search_select_placeholder,
				minimumInputLength: 3,
				allowClear        : true,
				templateResult    : formatSearchResult,
				templateSelection : formatSearchResultSelection,
				ajax              : {
					method        : 'POST',
					url           : sas_ajax.url,
					delay         : 500,
					data          : function ( params ) {
						return {
							_ajax_nonce: sas_ajax.nonce,
							action     : 'smart_admin_search',
							query      : params.term
						};
					},
					processResults: function ( result ) {
						return {
							results: result.data
						};
					}
				}
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
