import compression  from 'compression'
import express from "express";
// import {getDir, getFile} from "./Supports/file.js";
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import fs from 'fs';
import bodyParser from 'body-parser';

export function getDir(url){
    return path.dirname(fileURLToPath(url))
}

export function getResolvePath(dirname, p) {
    return path.resolve(dirname, p)
}

export function getFile(dirname, filePath) {
    return fs.readFileSync(getResolvePath(dirname, filePath),'utf-8')
}

import { render } from './../../../../../bootstrap/ssr/server/server-entry.js';

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
    app.use(bodyParser.urlencoded({ extended: true }));
    app.use(bodyParser.json());
    app.use(compression());

    app.use("/", async (req, res) => {
        try {
            const [appHtml, preloadLinks] = await render(req);

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