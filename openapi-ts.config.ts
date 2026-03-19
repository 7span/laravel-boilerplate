import { defineConfig } from '@hey-api/openapi-ts';

export default defineConfig({
    input: './openapi/openapi.json',
    output: {
        path: './openapi/output.ts',
    },
    plugins: [
        {
            name: '@hey-api/typescript',
            enums: 'javascript',
            comments: false,
        },
    ],
});
