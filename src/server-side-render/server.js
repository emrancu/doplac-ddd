import compression  from 'compression'
import express from "express";
import {getDir, getFile} from "./Supports/file.js";

import { render } from '../../../../bootstrap/ssr/server/server-entry.js';

const PORT = 6173

function removeScriptTags(inputString) {
    const pattern = /<script type="module"[^>]*>[\s\S]*?<\/script>/g;
    return inputString.replace(pattern, '');
}

function removeLinkTags(inputString) {
    const pattern = /<link rel="stylesheet"[^>]*>[\s\S]*?">/g;
    return inputString.replace(pattern, '');
}

export async function createProductionServer(app, dirname ) {

    app.use(compression());

    app.use("/", async (req, res) => {
        try {
            const url = req.originalUrl;
          //  const template = getFile(dirname, "../dist/client/ssr/index.html");
           // const manifest  = JSON.parse(getFile(dirname,  "../../../../bootstrap/ssr/ssr-manifest.json"))

            const [appHtml, preloadLinks] = await render(url);

            res.status(200).set({ "Content-Type": "text/html" }).end(appHtml);
        } catch (e) {
            res.status(500).end(e.stack);
        }
    });
    return { app }
}


async function createServer(
    root = process.cwd(),
    isProd = process.env.NODE_ENV === "production",
) {

    const __dirname = getDir(import.meta.url)
    const app = express();
    await createProductionServer(app, __dirname)
    return { app };
}

createServer().then(({ app }) =>
    app.listen(PORT, () => {
        console.log(`http://localhost:${PORT}`);
    })
);