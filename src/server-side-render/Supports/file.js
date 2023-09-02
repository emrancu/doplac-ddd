import path from 'node:path';
import { fileURLToPath } from 'node:url';
import fs from 'fs';

export function getDir(url){
    return path.dirname(fileURLToPath(url))
}

export function getResolvePath(dirname, p) {
    return path.resolve(dirname, p)
}

export function getFile(dirname, filePath) {
    return fs.readFileSync(getResolvePath(dirname, filePath),'utf-8')
}
