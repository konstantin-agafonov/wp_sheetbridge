function sbShowCopiedNotice( value ) {
	var notice = document.createElement( 'span' );
	notice.textContent = 'Value ' + value + ' copied to clipboard';
	notice.style.cssText = 'position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:#1d2327;color:#fff;padding:8px 16px;border-radius:4px;z-index:99999;font-size:13px;';
	document.body.appendChild( notice );
	setTimeout( function() { notice.remove(); }, 2000 );
}

function sbCopyFallback( value ) {
	var ta = document.createElement( 'textarea' );
	ta.value = value;
	ta.style.position = 'fixed';
	ta.style.left = '-9999px';
	document.body.appendChild( ta );
	ta.select();
	document.execCommand( 'copy' );
	ta.remove();
	sbShowCopiedNotice( value );
}

document.addEventListener( 'click', function( e ) {
	var el = e.target.closest( '.sb-copy-id' );
	if ( ! el ) return;
	e.preventDefault();
	var value = el.getAttribute( 'data-id' );
	if ( ! value ) return;
	if ( navigator.clipboard ) {
		navigator.clipboard.writeText( value ).then( function() {
			sbShowCopiedNotice( value );
		} ).catch( function() {
			sbCopyFallback( value );
		} );
	} else {
		sbCopyFallback( value );
	}
} );
