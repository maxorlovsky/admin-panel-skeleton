var formInProgress = 0;

$('#submitContactForm').on('click', this, function() {
    formInProgress = 1;
	var query = {
        type: 'POST',
        data: {
        	ajax: 'submitContactForm',
        	form: $('.contact-form').serialize()
		},
    	success: function(data) {
    		formInProgress = 0;
    	}
    };
	ajax(query);
});


function ajax(object) {
    if (!object.url) {
        object.url = site;
    }
    if (!object.async) {
        object.async = true;
    }
    if (!object.dataType) {
        object.dataType = '';
    }
    if (!object.success) {
        object.success = function(data) {
            alert(data);
        };
    }
    if (!object.data) {
        object.data = {};
    }
    if (!object.type) {
        object.type = 'GET';
    }
    if (!object.xhrFields) {
        object.xhrFields = { withCredentials: true };
    }
    if (!object.crossDomain) {
        object.crossDomain = true;
    }
    if (!object.cache) {
        object.cache = true;
    }
    if (!object.timeout) {
        object.timeout = 60000;
    }
    if (!object.error) {
        object.error = function(xhr, ajaxOptions, thrownError) {
            console.log(object.url);
            console.log(xhr);
            console.log(ajaxOptions);
            console.log(thrownError);
        };
    }
    
    return $.ajax({
    	url: object.url,
        type: object.type,
    	async: object.async,
        data: object.data,
    	dataType: object.dataType,
        xhrFields: object.xhrFields,
        crossDomain: object.crossDomain,
        cache: object.cache,
        timeout: object.timeout,
    	success: object.success,
        error: object.error
    });
}