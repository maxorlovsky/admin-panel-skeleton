import * as path from 'path';

let getPublicPath: string = path.join(__dirname, '../../public');

if (process.env.NODE_ENV === 'development') {
    getPublicPath = path.join(__dirname, '../public');
}

export const publicPath: string = getPublicPath;