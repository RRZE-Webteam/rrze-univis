var edited=!1;function createBlock(r){const{registerBlockType:e}=wp.blocks,{createElement:b}=wp.element;var{}=wp.blockEditor;const{CheckboxControl:p,RadioControl:d,SelectControl:u,TextControl:h,TextareaControl:k,ToggleControl:v}=wp.components,{serverSideRender:y}=wp;e(r.block.blocktype,{title:r.block.title,category:r.block.category,icon:r.block.icon,construct(){props.setAttributes({countit:0})},edit(e){var l=e.attributes;const t=e.setAttributes;function o(e){"number"==r[this].type&&(e=parseInt(e)),t({[this]:e})}if(!1===e.isSelected&&!0===edited)return function(e){for(var l in e)null!==e[l]&&void 0!==e[l]||delete e[l]}(l),b(y,{block:r.block.blocktype,attributes:l});var a,c=[];for(a in c.push(b("div",{className:"components-placeholder__label"},[b("span",{className:"editor-block-icon block-editor-block-icon dashicons-before dashicons-"+r.block.icon},null),r.block.title])),r)switch(r[a].field_type){case"checkbox":c.push(b(p,{checked:void 0!==l[a]?l[a]:r[a].default,label:r[a].label,onChange:o.bind(a)}));break;case"radio":var n,s=[];for(n in r[a].values)s.push(JSON.parse('{"value":"'+n+'", "label":"'+r[a].values[n]+'"}'));c.push(b(d,{selected:void 0!==l[a]?l[a]:r[a].default,label:r[a].label,onChange:o.bind(a),options:s}));break;case"multi_select":case"select":for(var s=[],i=0;i<r[a].values.length;i++)s.push(JSON.parse('{"value":"'+r[a].values[i].id+'", "label":"'+r[a].values[i].val+'"}'));c.push(b(u,{multiple:"multi_select"==r[a].field_type?1:0,value:l[a],label:r[a].label,type:r[a].type,onChange:o.bind(a),options:s}));break;case"text":c.push(b(h,{value:l[a],label:r[a].label,type:r[a].type,onChange:o.bind(a)}));break;case"textarea":c.push(b(k,{value:l[a],label:r[a].label,type:r[a].type,onChange:o.bind(a)}));break;case"toggle":c.push(b(v,{checked:void 0!==l[a]?l[a]:r[a].default,label:r[a].label,type:r[a].type,onChange:o.bind(a)}))}return edited=!0,b("div",{className:"components-placeholder"},c)},save(e){return null}})}