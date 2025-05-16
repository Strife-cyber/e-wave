import createStudioEditor from '@grapesjs/studio-sdk';

import '@grapesjs/studio-sdk/style';

// ...once the '#studio-editor' DOM element is rendered
createStudioEditor({
    root: '#studio-editor',
    licenseKey: '9ee1692d2eb740b88e2c2e8519040a68792ef809caa64f65ab846c67098a3e55',
    project: {
        type: 'web',
        // TODO: replace with a unique id for your projects. e.g. an uuid
        id: '37920b33-d881-42d2-ad6d-1077accb08da',
    },
    identity: {
        // TODO: replace with a unique id for your end users. e.g. an uuid
        id: '548626e8-d7e6-4ada-b587-91c116372924',
    },
    assets: {
        storageType: 'cloud',
    },
    storage: {
        type: 'cloud',
        autosaveChanges: 100,
        autosaveIntervalMs: 10000,
    },
});
