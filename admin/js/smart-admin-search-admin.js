(function( $ ) {
	'use strict';

	$(function() {

		/*
		 * ELEMENTS
		 */

		let sasDocument          = $( document );
		let sasSearchModal       = $( '.sas-search-modal' );
		let sasSearchModalSelect = $( '.sas-search-modal__select' );
		let sasAdminBarIcon      = $( '#wp-admin-bar-sas_icon' );
		
		/*
		 * ADMIN BAR
		 */
		
		sasAdminBarIcon.on( 'click', function( e ) {
			e.preventDefault();
			showSearchModal();
		} );

		/*
		 * GLOBAL KEY PRESS
		 */

		// Get search keys shortcut, sorting items and converting them to int values.
		let searchKeysShortcut = sas_values.options.search_keys_shortcut.sort().map( Number );

		// Array of pressed keys.
		let pressedKeys = [];

		// Check the pressed keys.
		sasDocument.on( 'keydown', function( e ) {
			if ( ! $( e.srcElement ).hasClass( 'sas-skip-global-keypress' ) ) {

				// Add pressed key to the array if not already added.
				if ( pressedKeys.includes( e.which ) === false ) {
					pressedKeys.push( e.which );
				}

				// If pressed keys are the same as the keys of the shortcut, open the search box.
				if ( JSON.stringify( pressedKeys.sort() ) === JSON.stringify( searchKeysShortcut ) ) {
					showSearchModal();
				} else if ( pressedKeys.includes( 27 ) ) {
					// 27 = ESC key.
					hideSearchModal();
				}

			}
		} );

		// Reset pressed keys array.
		sasDocument.on( 'keyup', function( e ) {
			if ( ! $( e.srcElement ).hasClass( 'sas-skip-global-keypress' ) ) {
				pressedKeys = [];
			}
		} );

		/*
		 * SEARCH MODAL
		 */

		function formatSearchResult( result ) {
			if( result.loading ) {
				return result.text;
			}
			
			let template = $(
				'<div class="sas-search-result">' +
					'<div class="sas-search-result__name">' + result.text + '</div>' + 
					'<div class="sas-search-result__description">' + result.description + '</div>' +
					'<div class="sas-search-result__link-url">' + result.link_url + '</div>' +
				'</div>'
			);
			
			return template;
		}
		
		function formatSearchResultSelection( result ) {
			if ( result.id === '' ) {
				return sas_values.strings.search_select_placeholder;
			}
	
			return result.text;
		}
		
		function showSearchModal() {
			sasSearchModal.css( 'display', 'block' );
			
			sasSearchModalSelect.select2( {
				dropdownParent    : sasSearchModal,
				width             : '100%',
				placeholder       : sas_values.strings.search_select_placeholder,
				minimumInputLength: 3,
				allowClear        : true,
				templateResult    : formatSearchResult,
				templateSelection : formatSearchResultSelection,
				ajax              : {
					method        : 'GET',
					url           : sas_values.ajax.search_url,
					delay         : 500,
					beforeSend    : function (xhr) {
						xhr.setRequestHeader( 'X-WP-NONCE', sas_values.ajax.nonce );
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
			if ( sasSearchModalSelect.hasClass( 'select2-hidden-accessible' ) ) {
				sasSearchModal.css( 'display', 'none' );
				
				sasSearchModalSelect.select2( 'destroy' );
				sasSearchModalSelect.empty();
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

		/*
		 * SETTINGS PAGE
		 */

		if ( $( 'body' ).hasClass( 'settings_page_smart-admin-search_options' ) ) {

			// Get elements.
			let sasCaptureSearchKeys      = $( '#sas-capture-search-keys' );
			let sasCaptureSearchKeysReset = $( '#sas-capture-search-keys-reset' );
			let sasSearchKeysShortcut     = $( '#sas_search_keys_shortcut' );

			// Get current search keys shortcut.
			let currentSearchKeysShortcut = sasSearchKeysShortcut.val();

			// Array of pressed keys.
			let optionsPressedKeys = [];

			sasCaptureSearchKeys.on( 'keydown', function( e ) {
				e.preventDefault();

				if ( optionsPressedKeys.includes( e.which + '|' + e.key ) === false ) {
					// Add pressed key to the array.
					optionsPressedKeys.push( e.which + '|' + e.key );

					// Add pressed key to the textbox.
					if ( $(this).val() === '' ) {
						$(this).val( e.key );
					} else {
						$(this).val( $(this).val() + '+' + e.key );
					}
				}
			} );

			sasCaptureSearchKeys.on( 'keyup', function() {
				sasSearchKeysShortcut.val( optionsPressedKeys.join() );
			} );

			sasCaptureSearchKeysReset.on( 'click', function() {
				// Clear the pressed keys array.
				optionsPressedKeys = [];

				// Clear the textbox content.
				sasCaptureSearchKeys.val( '' );

				// Reset the option field with the current shortcut.
				sasSearchKeysShortcut.val( currentSearchKeysShortcut );
			} );

		}

	});

})( jQuery );
