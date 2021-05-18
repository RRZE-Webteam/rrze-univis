"use strict";
 
wp.domReady( 
    function(){
        jQuery(document).ready(function($){
            jQuery(document).on('change', 'input#number', function(){
                getUnivISDataForBlockelements('personAll', 'select#univisid');
                getUnivISDataForBlockelements('lectureByDepartment', 'select#id');
            });
            // jQuery(document).on('change', 'select#univisid', function(){
            //     jQuery(document).ready(function($){
            //         if (jQuery('select#id') == 'undefined'){
            //             var task = 'mitarbeiter-' + (jQuery('select#univisid').val() == '' ? 'alle' : 'einzeln');
            //             jQuery('select#task').val(task);
            //         }
            //     });
            // });
            // jQuery(document).on('change', 'select#id', function(){
            //     jQuery(document).ready(function($){
            //         // var task = 'lehrveranstaltungen-' + (jQuery('select#id').val() == '' ? 'alle' : 'einzeln');
            //         jQuery('select#task').val(task).trigger('change');
            //     });
            // });
        });
    });

function getUnivISDataForBlockelements($dataType, $output) {
    var $univisOrgID = jQuery('input#number').val();
    var $output = jQuery($output);

    if ($univisOrgID){
        $output.html('<option value="">loading... </option>');
    
        jQuery.post(univis_ajax.ajax_url, { 
            _ajax_nonce: univis_ajax.nonce,
            action: 'GetUnivISDataForBlockelements',
            data: {'univisOrgID':$univisOrgID, 'dataType':$dataType},               
        }, function(result) {
            $output.html(result);
        });
    }
}
