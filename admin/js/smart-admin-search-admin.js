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
					'<div class="link_url">' + result.link_url + '</div>' +
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
					method        : 'GET',
					url           : sas_ajax.search_url,
					delay         : 500,
					beforeSend    : function (xhr) {
						xhr.setRequestHeader( 'X-WP-NONCE', sas_ajax.nonce );
					},
					data          : function ( params ) {
						return {
							query: params.term
						};
					},
					processResults: function ( result ) {
						return {
							results: result
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
			
			if ( sasSearchModalSelect.hasClass( 'select2-hidden-accessible' ) ) {
				sasSearchModalSelect.select2( 'destroy' );
			}
		}
		
		sasSearchModal.on( 'click', function( e ) {
			if ( e.target === this ) {
				hideSearchModal();
			}
		} );
		
		// Event triggered when a select item is selected.
		sasSearchModalSelect.on('select2:select', function (e) {
			let item_data = e.params.data;
			
			if ( item_data.link_url !== null && item_data.link_url !== '' ) {
				window.location.href = item_data.link_url;
			}
		});

	});

})( jQuery );
