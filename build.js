'use strict';

const oryx = require('@spryker/oryx');
const oryxForZed = require('@spryker/oryx-for-zed');
const path = require('path');

const customSettings = Object.assign({}, oryxForZed.settings, {
    entry: {
        dirs: [path.resolve('./vendor/spryker'), path.resolve('./assets')],
        patterns: ['**/Zed/**/*.entry.js'],
        description: 'looking for entry points...',
        defineName: p => path.basename(p, '.entry.js')
    },

    resolveModules: {
        dirs: [path.resolve('./vendor/spryker'), path.resolve('./assets')],
        patterns: ['**/Zed/node_modules'],
        description: 'resolving core modules deps...'
    },
});

const configuration = oryxForZed.getConfiguration(customSettings);

oryx.build(configuration);
