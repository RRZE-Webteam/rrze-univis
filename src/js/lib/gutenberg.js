var dynamicBlockConfigCache = {};
var dynamicBlockConfigCallbacks = {};

export function createBlock(blockConfig) {
    var registerBlockType = wp.blocks.registerBlockType;
    var createElement = wp.element.createElement;
    var Fragment = wp.element.Fragment;
    var useEffect = wp.element.useEffect;
    var useState = wp.element.useState;
    var CheckboxControl = wp.components.CheckboxControl;
    var PanelBody = wp.components.PanelBody;
    var RadioControl = wp.components.RadioControl;
    var SelectControl = wp.components.SelectControl;
    var TextControl = wp.components.TextControl;
    var TextareaControl = wp.components.TextareaControl;
    var ToggleControl = wp.components.ToggleControl;
    var InspectorControls = wp.blockEditor ? wp.blockEditor.InspectorControls : wp.editor.InspectorControls;
    var serverSideRender = wp.serverSideRender;

    function createRadioOptions(fieldConfig) {
        var opts = [];
        var valueKey;

        for (valueKey in fieldConfig.values) {
            if (Object.prototype.hasOwnProperty.call(fieldConfig.values, valueKey)) {
                opts.push({
                    value: valueKey,
                    label: fieldConfig.values[valueKey]
                });
            }
        }

        return opts;
    }

    function createSelectOptions(fieldConfig) {
        var opts = [];
        var i;

        for (i = 0; i < fieldConfig.values.length; i++) {
            opts.push({
                value: fieldConfig.values[i].id,
                label: fieldConfig.values[i].val
            });
        }

        return opts;
    }

    function getFieldValue(att, config, fieldName) {
        if (typeof att[fieldName] !== 'undefined' && att[fieldName] !== '') {
            return att[fieldName];
        }

        if (typeof config[fieldName] !== 'undefined' && typeof config[fieldName].default !== 'undefined') {
            return config[fieldName].default;
        }

        return '';
    }

    function getAttributesWithDefaults(att, config) {
        var normalized = {};
        var fieldName;

        for (fieldName in att) {
            if (Object.prototype.hasOwnProperty.call(att, fieldName)) {
                normalized[fieldName] = att[fieldName];
            }
        }

        for (fieldName in config) {
            if (!Object.prototype.hasOwnProperty.call(config, fieldName) || fieldName === 'block') {
                continue;
            }

            if ((typeof normalized[fieldName] === 'undefined' || normalized[fieldName] === '') && typeof config[fieldName].default !== 'undefined' && config[fieldName].default !== null) {
                normalized[fieldName] = config[fieldName].default;
            }
        }

        return normalized;
    }

    function createSelectOptionsFromMap(values, firstLabel) {
        var opts = [];
        var valueKey;

        opts.push({
            value: '',
            label: firstLabel
        });

        for (valueKey in values) {
            if (Object.prototype.hasOwnProperty.call(values, valueKey)) {
                opts.push({
                    value: valueKey,
                    label: values[valueKey]
                });
            }
        }

        return opts;
    }

    function isDynamicField(fieldName) {
        return ['univisid', 'id', 'type', 'sprache'].indexOf(fieldName) !== -1;
    }

    function hasDynamicFields(config) {
        return !!(config.univisid || config.id || config.type || config.sprache);
    }

    function getInitialDynamicOptions(config) {
        return {
            univisid: config.univisid ? createSelectOptions(config.univisid) : [],
            id: config.id ? createSelectOptions(config.id) : [],
            type: config.type ? createSelectOptions(config.type) : [],
            sprache: config.sprache ? createSelectOptions(config.sprache) : []
        };
    }

    function normalizeDynamicConfig(result) {
        return {
            univisid: createSelectOptionsFromMap(result.persons || {}, '-- All --'),
            id: createSelectOptionsFromMap(result.lectures || {}, '-- All --'),
            type: createSelectOptionsFromMap(result.lectureTypes || {}, '-- All --'),
            sprache: createSelectOptionsFromMap(result.lectureLanguages || {}, '-- All --')
        };
    }

    function loadDynamicBlockConfig(univisOrgID, callback) {
        if (!univisOrgID || typeof univis_ajax === 'undefined') {
            callback(null);
            return;
        }

        if (typeof dynamicBlockConfigCache[univisOrgID] !== 'undefined') {
            callback(dynamicBlockConfigCache[univisOrgID]);
            return;
        }

        if (!dynamicBlockConfigCallbacks[univisOrgID]) {
            dynamicBlockConfigCallbacks[univisOrgID] = [];

            jQuery.post(univis_ajax.ajax_url, {
                _ajax_nonce: univis_ajax.nonce,
                action: 'GetUnivISDataForBlockelements',
                data: { univisOrgID: univisOrgID, dataType: 'blockConfig' }
            }, function onBlockConfigLoaded(result) {
                var callbacks = dynamicBlockConfigCallbacks[univisOrgID] || [];
                var i;

                dynamicBlockConfigCache[univisOrgID] = result;
                delete dynamicBlockConfigCallbacks[univisOrgID];

                for (i = 0; i < callbacks.length; i++) {
                    callbacks[i](result);
                }
            });
        }

        dynamicBlockConfigCallbacks[univisOrgID].push(callback);
    }

    registerBlockType(blockConfig.block.blocktype, {
        title: blockConfig.block.title,
        description: blockConfig.block.description || '',
        category: blockConfig.block.category,
        icon: blockConfig.block.icon,
        keywords: Array.isArray(blockConfig.block.keywords) ? blockConfig.block.keywords : [],
        edit: function edit(props) {
            var att = props.attributes;
            var setAtts = props.setAttributes;
            var normalizedAtt = getAttributesWithDefaults(att, blockConfig);
            var currentNumber = typeof att.number !== 'undefined' && att.number !== '' ? att.number : (blockConfig.number ? blockConfig.number.default : '');
            var initialDynamicOptions = getInitialDynamicOptions(blockConfig);
            var dynamicOptionsState = useState(initialDynamicOptions);
            var dynamicOptions = dynamicOptionsState[0];
            var setDynamicOptions = dynamicOptionsState[1];
            var loadedOrgState = useState('');
            var loadedOrgId = loadedOrgState[0];
            var setLoadedOrgId = loadedOrgState[1];
            var controls;
            var fieldname;

            useEffect(function onSelectedBlock() {
                if (!props.isSelected || !hasDynamicFields(blockConfig) || !currentNumber || loadedOrgId === currentNumber) {
                    return;
                }

                loadDynamicBlockConfig(currentNumber, function onDynamicConfigLoaded(result) {
                    if (!result || typeof result !== 'object') {
                        return;
                    }

                    setDynamicOptions(normalizeDynamicConfig(result));
                    setLoadedOrgId(currentNumber);
                });
            }, [props.isSelected, currentNumber, loadedOrgId]);

            useEffect(function onDefaultNumber() {
                if (typeof att.number !== 'undefined' || !blockConfig.number || !blockConfig.number.default) {
                    return;
                }

                setAtts({
                    number: blockConfig.number.default
                });
            }, [att.number]);

            function changeField(fieldName, val) {
                var nextValue = val;

                if (blockConfig[fieldName].type === 'number') {
                    nextValue = parseInt(val, 10);
                }

                if (fieldName === 'number') {
                    setDynamicOptions(initialDynamicOptions);
                    setLoadedOrgId('');
                    setAtts({
                        univisid: '',
                        id: '',
                        type: '',
                        sprache: '',
                        number: nextValue
                    });
                    return;
                }

                setAtts({ [fieldName]: nextValue });
            }

            controls = [];

            for (fieldname in blockConfig) {
                if (!Object.prototype.hasOwnProperty.call(blockConfig, fieldname) || fieldname === 'block') {
                    continue;
                }

                switch (blockConfig[fieldname].field_type) {
                    case 'checkbox':
                        controls.push(createElement(CheckboxControl, {
                            id: fieldname,
                            checked: typeof att[fieldname] !== 'undefined' ? att[fieldname] : blockConfig[fieldname].default,
                            label: blockConfig[fieldname].label,
                            onChange: changeField.bind(null, fieldname)
                        }));
                        break;
                    case 'radio':
                        controls.push(createElement(RadioControl, {
                            id: fieldname,
                            selected: typeof att[fieldname] !== 'undefined' ? att[fieldname] : blockConfig[fieldname].default,
                            label: blockConfig[fieldname].label,
                            onChange: changeField.bind(null, fieldname),
                            options: createRadioOptions(blockConfig[fieldname])
                        }));
                        break;
                    case 'multi_select':
                    case 'select':
                        controls.push(createElement(SelectControl, {
                            id: fieldname,
                            multiple: blockConfig[fieldname].field_type === 'multi_select' ? 1 : 0,
                            value: getFieldValue(normalizedAtt, blockConfig, fieldname),
                            label: blockConfig[fieldname].label,
                            type: blockConfig[fieldname].type,
                            onChange: changeField.bind(null, fieldname),
                            options: isDynamicField(fieldname) ? dynamicOptions[fieldname] : createSelectOptions(blockConfig[fieldname])
                        }));
                        break;
                    case 'text':
                        controls.push(createElement(TextControl, {
                            id: fieldname,
                            value: getFieldValue(normalizedAtt, blockConfig, fieldname),
                            label: blockConfig[fieldname].label,
                            type: blockConfig[fieldname].type,
                            onChange: changeField.bind(null, fieldname)
                        }));
                        break;
                    case 'textarea':
                        controls.push(createElement(TextareaControl, {
                            id: fieldname,
                            value: getFieldValue(normalizedAtt, blockConfig, fieldname),
                            label: blockConfig[fieldname].label,
                            type: blockConfig[fieldname].type,
                            onChange: changeField.bind(null, fieldname)
                        }));
                        break;
                    case 'toggle':
                        controls.push(createElement(ToggleControl, {
                            id: fieldname,
                            checked: typeof att[fieldname] !== 'undefined' ? att[fieldname] : blockConfig[fieldname].default,
                            label: blockConfig[fieldname].label,
                            type: blockConfig[fieldname].type,
                            onChange: changeField.bind(null, fieldname)
                        }));
                        break;
                }
            }

            return createElement(
                Fragment,
                null,
                [
                    createElement(
                        InspectorControls,
                        { key: 'inspector' },
                        createElement(
                            PanelBody,
                            {
                                title: blockConfig.block.title,
                                initialOpen: true
                            },
                            controls
                        )
                    ),
                    createElement(
                        'div',
                        { key: 'preview', className: 'rrze-univis-block-preview' },
                        createElement(serverSideRender, {
                            block: blockConfig.block.blocktype,
                            attributes: normalizedAtt
                        })
                    )
                ]
            );
        },
        save: function save() {
            return null;
        }
    });
}
