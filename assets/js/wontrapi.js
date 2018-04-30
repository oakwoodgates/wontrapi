window.wontrapi_ajax = ( function( window, document, $ ){
	var app = {};

	app.cache = function(){
		app.$ajax_form = $( '.wontrapi_bnb' );
	};

	app.init = function(){
		app.cache();
		app.$ajax_form.on( 'click', app.form_handler );
	};

	app.post_ajax = function( data ){
		var post_data = { 
			action       : 'wontrapi_bnb',
			nonce        : wontrapi.nonce,
			wontrapi_bnb : data,
		};
		$.post( wontrapi.ajax_url, post_data, app.ajax_response, 'json' )
	};

	app.ajax_response = function( response_data ){
		if( response_data.success ){
			wontrapi.nonce = response_data.data.nonce;
			top.location.replace(response_data.data.url);
		} else {
			alert( 'ERROR' );
		}
	};

	app.form_handler = function( evt ){
		evt.preventDefault();
		var data = jQuery(this).data();
		// disable to avoid duplicate transactions
		$(".wontrapi_bnb").attr("disabled", true);
		app.post_ajax( data );
	};

	$(document).ready( app.init );

	return app;

})( window, document, jQuery );
