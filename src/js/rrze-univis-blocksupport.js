import { createBlock } from './lib/gutenberg';

function getBlockConfigs() {
    if (typeof window.rrzeUnivisBlockConfigs === 'undefined' || !window.rrzeUnivisBlockConfigs) {
        return {};
    }

    return window.rrzeUnivisBlockConfigs;
}

function registerBlocks() {
    var configs = getBlockConfigs();
    var blockName;

    for (blockName in configs) {
        if (Object.prototype.hasOwnProperty.call(configs, blockName)) {
            createBlock(configs[blockName]);
        }
    }
}

function getUnivISDataForBlockelements(dataType, outputSelector) {
    var univisOrgID = jQuery('input#number').val();
    var output = jQuery(outputSelector);

    if (univisOrgID) {
        output.html('<option value="">loading... </option>');

        jQuery.post(univis_ajax.ajax_url, {
            _ajax_nonce: univis_ajax.nonce,
            action: 'GetUnivISDataForBlockelements',
            data: { univisOrgID: univisOrgID, dataType: dataType }
        }, function onResult(result) {
            output.html(result);
        });
    }
}

function initBlockEditorBindings() {
    jQuery(document).on('change', 'input#number', function onNumberChange() {
        getUnivISDataForBlockelements('personAll', 'select#univisid');
        getUnivISDataForBlockelements('lectureByDepartment', 'select#id');
    });
}

function onDomReady() {
    registerBlocks();
    initBlockEditorBindings();
}

wp.domReady(onDomReady);
