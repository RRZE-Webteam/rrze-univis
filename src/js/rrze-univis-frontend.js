"use strict";

jQuery(document).ready(function ($) {
    $('.linkToICS').click(generateICS);

    function getURLParameter(url, name) {
        return (RegExp(name + '=' + '(.+?)(&|$)').exec(url) || [, null])[1];
    }

    function generateICS() {
        var url = this.href;
        var v = getURLParameter(url, 'v');
        var h = getURLParameter(url, 'h');

        jQuery.get(univis_frontend_ajax.ajax_frontend_url, {
            _ajax_nonce: univis_frontend_ajax.ics_nonce,
            action: 'GenerateICS',
            data: {
                'v': v,
                'h': h
            },
        }, function (response) {
            const blob = new Blob([response['icsData']], { type: 'text/calendar' });
            const downloadUrl = URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = downloadUrl;
            a.download = response[['filename']];
            document.body.appendChild(a);
            a.click();
        });
    }
});
