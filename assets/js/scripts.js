var cimlap_page = 1;

function cimlap_show_loader(where) {
	if (where) { 
		jQuery(where).after('<div class="cimlap-ajax-loader"></div>'); 
	}
}

function cimlap_remove_loader() {
	jQuery(".cimlap-ajax-loader").remove();
}

function cimlap_ajax_load(url, pars, where, evalonsuccess, method, cache) {
	
	cimlap_show_loader(where);
	
	if (cache === undefined) {
		cache = false;
	}
	if (method === undefined) {
		method = 'GET';
	}
	
	jQuery.ajax({
		url : url,
		data : pars,
		cache : cache,
		type : method,
		success : function(response) {
			if (where != "") {
				jQuery(where).append(response);
				cimlap_remove_loader();
			}
			eval(evalonsuccess);
		}
	});
}

jQuery(document).ready(function() {
	jQuery("body").on("click", ".btn-cimlap-more-items", function(){
		
		jQuery(".btn-cimlap-more-items").blur();
		
		cimlap_page++; 
		
		var cimlap_pars = ""; 
		
		cimlap_pars = cimlap_pars+"cimlap_page="+cimlap_page;
		cimlap_pars = cimlap_pars+"&action=cimlap_load_posts";
		cimlap_pars = cimlap_pars+"&cimlap_shortcode_id="+cimlap_shortcode_id;
		
		cimlap_ajax_load(cimlap_ajax_url, cimlap_pars, "#cimlap-ajax-container");
	});
});