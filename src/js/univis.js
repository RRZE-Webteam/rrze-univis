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

        $('#searchDepartment').click(getUnivISData);
});


function getUnivISData() {
    var keyword = jQuery('#keyword').val();
    var dataType = jQuery('#dataType').val();

    jQuery.post(univis_ajax.ajax_url, { 
        _ajax_nonce: univis_ajax.nonce,
        action: 'GetUnivISData',
        data: {'keyword':keyword, 'dataType':dataType},               
    }, function(result) {
        jQuery('div#univis-search-result').html(result);
    });
}

