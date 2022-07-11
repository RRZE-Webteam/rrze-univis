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

     $('.linkToICS').click(getICS);
    });

function getICS() {
    jQuery.get(univis_ajax.ajax_url, { 
        _ajax_nonce: univis_ajax.nonce,
        // action: 'GetUnivISData',
        data: {
            'v':v, 
            'h':h
        },               
    }, function(result) {
        alert('v = ' + v);
    });
}
