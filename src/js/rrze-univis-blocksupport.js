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
        if (
            Object.prototype.hasOwnProperty.call(configs, blockName) &&
            configs[blockName] &&
            configs[blockName].block &&
            configs[blockName].block.blocktype
        ) {
            createBlock(configs[blockName]);
        }
    }
}

function onDomReady() {
    registerBlocks();
}

wp.domReady(onDomReady);
