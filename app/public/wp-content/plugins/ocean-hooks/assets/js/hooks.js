// Sticky header
var $j = jQuery.noConflict();

$j( document ).on( 'ready', function() {
	// Hooks select
	hooksSelect();
	// Cookie
	hooksCookie();
	// Sticky box
	hooksBox();
} );

/* ==============================================
HOOKS SELECT
============================================== */
function hooksSelect() {
	"use strict"

	$j( '#hook-select' ).on( 'change', function() {
		var $id = $j( this ).children( ':selected' ).attr( 'id' );

		$j( '#oh-hooks .form-table tr' ).hide();
		$j( '#oh-hooks .form-table tr' ).eq( $id ).show();
		Cookies.set( 'hookcookie', $j( '#hook-select option:selected' ).attr( 'id' ), { expires: 90, path: '' } );
		
		if ( $j( '#hook-select' ).val() == 'all' ) {
			$j( '#oh-hooks .form-table tr' ).show();
			Cookies.remove( 'hookcookie', { expires: 90, path: '' } );
		}
		
	} );

}

/* ==============================================
HOOKS COOKIE
============================================== */
function hooksCookie() {
	"use strict"

	if ( Cookies.get( 'hookcookie' ) === '' 
		|| Cookies.get( 'hookcookie' ) === undefined ) {	
		$j( '#oh-hooks .form-table tr' ).show();
		Cookies.remove( 'hookcookie', { expires: 90, path: '' } );
	} else {
		$j( '#hook-select option[id="' + Cookies.get( 'hookcookie' ) + '"]' ).attr( 'selected', 'selected' );
		$j( '#hook-select option[id="' + Cookies.get( 'hookcookie' ) + '"]' ).attr( 'selected', 'selected' );
		$j( '#oh-hooks .form-table tr' ).hide();
		$j( '#oh-hooks .form-table tr' ).eq( Cookies.get( 'hookcookie' ) ).show();
	}

}

/* ==============================================
HOOKS BOX
============================================== */
function hooksBox() {
	"use strict"

	var $top = $j( '#oh-hooks .hooks-box' ).offset().top;

	$j( window ).scroll( function ( event ) {
		var $this = $j( this ).scrollTop();

		if ( $this >= $top ) {
			$j( '#oh-hooks .hooks-box' ).addClass( 'fixed' );
		} else {
			$j( '#oh-hooks .hooks-box' ).removeClass( 'fixed');
			$j( '#oh-hooks .hooks-box' ).width( $j( '#oh-hooks .hooks-box' ).parent().width() );
		}
	} );

}