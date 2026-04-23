"use strict";

function onAjaxStart($loading) {
    $loading.show();
}

function onAjaxStop($loading) {
    $loading.hide();
}

function onSearchResult($resultTab, result) {
    $resultTab.html(result);
    jQuery('div#loading').hide();
}

function onDocumentReady($) {
    var $loading = $('div#loading').hide();

    $(document)
        .ajaxStart(onAjaxStart.bind(null, $loading))
        .ajaxStop(onAjaxStop.bind(null, $loading));

    $('#searchUnivisID').click(getUnivISData);
}

jQuery(document).ready(onDocumentReady);

function getUnivISData() {
    var $dataType = jQuery('#dataType').val();
    var $keyword = jQuery('input#keyword');
    var $keywordVal = $keyword.val();
    var $resultTab = jQuery('div#univis-search-result');

    if ($keywordVal) {
        $resultTab.html('');
        $keyword.val('');

        jQuery.post(univis_ajax.ajax_url, {
            _ajax_nonce: univis_ajax.nonce,
            action: 'GetUnivISData',
            data: {'keyword': $keywordVal, 'dataType': $dataType},
        }, onSearchResult.bind(null, $resultTab));
    }
}
