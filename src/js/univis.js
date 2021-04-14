"use strict";

jQuery(document).ready(function($){
    var $loading = $('#loading').hide();
    $(document)
        .ajaxStart(function () {
            $loading.show();
        })
        .ajaxStop(function () {
            $loading.hide();
        });

        $('#searchDepartment').click(getDepartment);
});


function getDepartment() {
    var depName = jQuery('#department_name').val();

    jQuery.post(univis_ajax.ajax_url, { //POST request
        _ajax_nonce: univis_ajax.nonce, //nonce
        action: "GetDepartments",      //action
        data: depName,               //data
    }, function(result) {             //callback
        jQuery('div#univis-search-result').html(result);
    });
}

