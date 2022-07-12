"use strict";

jQuery(document).ready(function($){
    var $loading = $('div#loading').hide();

    $(document)
        .ajaxStart(function () {
            $loading.show();
        })
        .ajaxStop(function () {
         $loading.hide();
        });

     $('#searchUnivisID').click(getUnivISData);
    });

function getUnivISData() {
    var $dataType = jQuery('#dataType').val();
    var $keyword = jQuery('input#keyword');
    var $keywordVal = $keyword.val();
    var $resultTab = jQuery('div#univis-search-result');

    if ($keywordVal){
        $resultTab.html('');
        $keyword.val('');
        
        jQuery.post(univis_ajax.ajax_url, { 
            _ajax_nonce: univis_ajax.nonce,
            action: 'GetUnivISData',
            data: {'keyword':$keywordVal, 'dataType':$dataType},               
        }, function(result) {
            $resultTab.html(result);
            jQuery('div#loading').hide();
        });
    }
}
