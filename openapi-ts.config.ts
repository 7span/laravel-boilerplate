import { defineConfig } from '@hey-api/openapi-ts';

const OUTPUT_DIR = './typescript-transform/';

const sharedPlugins = [
    {
        name: '@hey-api/typescript' as const,
        enums: 'javascript' as const,
        comments: false,
    },
];

const panelOutput = (prefix: string) => ({
    path: OUTPUT_DIR,
    clean: false,
    entryFile: false,
    fileName: {
        name: (name: string) => `${prefix}.${name}`,
    },
});

export default defineConfig([
    {
        input: './scramble/api.json',
        output: panelOutput('api'),
        plugins: sharedPlugins,
    },
    {
        input: './scramble/admin.json',
        output: panelOutput('admin'),
        plugins: sharedPlugins,
    },
    {
        input: './scramble/organizer.json',
        output: panelOutput('organizer'),
        plugins: sharedPlugins,
    },
    {
        input: './scramble/usher.json',
        output: panelOutput('usher'),
        plugins: sharedPlugins,
    },
]);
