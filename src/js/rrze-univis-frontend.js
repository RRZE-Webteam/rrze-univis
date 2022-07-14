"use strict";

jQuery(document).ready(function ($) {
    $('.linkToICS').click(generateICS);

    function getURLParameter(url, name) {
        return (RegExp(name + '=' + '(.+?)(&|$)').exec(url) || [, null])[1];
    }

    function generateICS() {
        var url = this.href;

        var SUMMARY = getURLParameter(url, 'SUMMARY');
        var LOCATION = getURLParameter(url, 'LOCATION');
        var DESCRIPTION = getURLParameter(url, 'DESCRIPTION');
        var FILENAME = getURLParameter(url, 'FILENAME');
        var FREQ = getURLParameter(url, 'FREQ');
        var REPEAT = getURLParameter(url, 'REPEAT');
        var DTSTART = getURLParameter(url, 'DTSTART');
        var DTEND = getURLParameter(url, 'DTEND');
        var UNTIL = getURLParameter(url, 'UNTIL');
        var h = getURLParameter(url, 'h');

        jQuery.getJSON(univis_frontend_ajax.ajax_frontend_url, {
            _ajax_nonce: univis_frontend_ajax.ics_nonce,
            action: 'GenerateICS',
            data: {
                'SUMMARY': SUMMARY,
                'LOCATION': LOCATION,
                'DESCRIPTION': DESCRIPTION,
                'FILENAME': FILENAME,
                'FREQ': FREQ,
                'REPEAT': REPEAT,
                'DTSTART': DTSTART,
                'DTEND': DTEND,
                'UNTIL': UNTIL,
                'h': h,
                },
        }, function (response) {
            console.log('1. filename = ' + response['filename']);
            const blob = new Blob([response['icsData']], { type: 'text/calendar' });
            const downloadUrl = URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = downloadUrl;
            a.download = response['filename'];
            document.body.appendChild(a);
            a.click();
        });
    }
});
