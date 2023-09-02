
## for SSR

```shell
       "ssr:server": "node ./vendor/zupitar-doplac/domain-driven-development/src/server-side-render/index.js",
        "build:ssr": "SSR_PREVIEW=sst_preview npm run build:client && npm run build:server",
        "build:client": "vite build --ssrManifest --outDir bootstrap/ssr",
        "build:server": "vite build --ssr ./vendor/zupitar-doplac/domain-driven-development/src/server-side-render/server-entry.js --outDir bootstrap/ssr/server"
   
```