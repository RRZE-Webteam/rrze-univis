var edited = false;

export function createBlock(blockConfig) {
    var registerBlockType = wp.blocks.registerBlockType;
    var createElement = wp.element.createElement;
    var CheckboxControl = wp.components.CheckboxControl;
    var RadioControl = wp.components.RadioControl;
    var SelectControl = wp.components.SelectControl;
    var TextControl = wp.components.TextControl;
    var TextareaControl = wp.components.TextareaControl;
    var ToggleControl = wp.components.ToggleControl;
    var serverSideRender = wp.serverSideRender;

    function clean(obj) {
        var propName;

        for (propName in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, propName)) {
                if (obj[propName] === null || obj[propName] === undefined) {
                    delete obj[propName];
                }
            }
        }
    }

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

    registerBlockType(blockConfig.block.blocktype, {
        title: blockConfig.block.title,
        category: blockConfig.block.category,
        icon: blockConfig.block.icon,
        edit: function edit(props) {
            var att = props.attributes;
            var setAtts = props.setAttributes;
            var ret;
            var fieldname;

            function changeField(fieldName, val) {
                var nextValue = val;

                if (blockConfig[fieldName].type === 'number') {
                    nextValue = parseInt(val, 10);
                }

                setAtts({ [fieldName]: nextValue });
            }

            if (props.isSelected === false && edited === true) {
                clean(att);
                return createElement(serverSideRender, {
                    block: blockConfig.block.blocktype,
                    attributes: att
                });
            }

            ret = [];
            ret.push(
                createElement(
                    'div',
                    { className: 'components-placeholder__label' },
                    [
                        createElement(
                            'span',
                            { className: 'editor-block-icon block-editor-block-icon dashicons-before dashicons-' + blockConfig.block.icon },
                            null
                        ),
                        blockConfig.block.title
                    ]
                )
            );

            for (fieldname in blockConfig) {
                if (!Object.prototype.hasOwnProperty.call(blockConfig, fieldname) || fieldname === 'block') {
                    continue;
                }

                switch (blockConfig[fieldname].field_type) {
                    case 'checkbox':
                        ret.push(createElement(CheckboxControl, {
                            id: fieldname,
                            checked: typeof att[fieldname] !== 'undefined' ? att[fieldname] : blockConfig[fieldname].default,
                            label: blockConfig[fieldname].label,
                            onChange: changeField.bind(null, fieldname)
                        }));
                        break;
                    case 'radio':
                        ret.push(createElement(RadioControl, {
                            id: fieldname,
                            selected: typeof att[fieldname] !== 'undefined' ? att[fieldname] : blockConfig[fieldname].default,
                            label: blockConfig[fieldname].label,
                            onChange: changeField.bind(null, fieldname),
                            options: createRadioOptions(blockConfig[fieldname])
                        }));
                        break;
                    case 'multi_select':
                    case 'select':
                        ret.push(createElement(SelectControl, {
                            id: fieldname,
                            multiple: blockConfig[fieldname].field_type === 'multi_select' ? 1 : 0,
                            value: att[fieldname],
                            label: blockConfig[fieldname].label,
                            type: blockConfig[fieldname].type,
                            onChange: changeField.bind(null, fieldname),
                            options: createSelectOptions(blockConfig[fieldname])
                        }));
                        break;
                    case 'text':
                        ret.push(createElement(TextControl, {
                            id: fieldname,
                            value: att[fieldname],
                            label: blockConfig[fieldname].label,
                            type: blockConfig[fieldname].type,
                            onChange: changeField.bind(null, fieldname)
                        }));
                        break;
                    case 'textarea':
                        ret.push(createElement(TextareaControl, {
                            id: fieldname,
                            value: att[fieldname],
                            label: blockConfig[fieldname].label,
                            type: blockConfig[fieldname].type,
                            onChange: changeField.bind(null, fieldname)
                        }));
                        break;
                    case 'toggle':
                        ret.push(createElement(ToggleControl, {
                            id: fieldname,
                            checked: typeof att[fieldname] !== 'undefined' ? att[fieldname] : blockConfig[fieldname].default,
                            label: blockConfig[fieldname].label,
                            type: blockConfig[fieldname].type,
                            onChange: changeField.bind(null, fieldname)
                        }));
                        break;
                }
            }

            edited = true;

            return createElement('div', { className: 'components-placeholder' }, ret);
        },
        save: function save() {
            return null;
        }
    });
}
