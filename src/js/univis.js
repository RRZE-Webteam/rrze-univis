"use strict";

jQuery(document).ready(function($){
    $('#searchUnivisID').click(getUnivISData);
});


function getUnivISData() {
    var keyword = jQuery('#keyword').val();
    var dataType = jQuery('#dataType').val();
    var $loading = jQuery('#loading');
    $loading.show();
    jQuery('div#univis-search-result').html('');

    jQuery.post(univis_ajax.ajax_url, { 
        _ajax_nonce: univis_ajax.nonce,
        action: 'GetUnivISData',
        data: {'keyword':keyword, 'dataType':dataType},               
    }, function(result) {
        $loading.hide();
        jQuery('div#univis-search-result').html(result);
    });
}

