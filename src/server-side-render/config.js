export const viteConfig = {
    build: {
        rollupOptions: {
            input: {
                main: './vendor/zupitar-doplac/domain-driven-development/src/server-side-render/client-entry.js',
            },
        },
    }
}