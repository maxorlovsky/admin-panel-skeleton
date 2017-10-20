const gulp = require('gulp');
const fs = require('fs');
const packageJson = require('../../package.json');
const config = require('../../../../../mocms/config.json'); // Fetching client config params
let version = '';

if (config.version) {
    version = config.version;
} else {
    version = packageJson.version;
}

gulp.task("bump-ver", () => {
    const componentFilePath = './dist/js/vendors.js';
	let componentsJsTemp = fs.readFileSync(componentFilePath, 'utf-8');
    let componentJs = componentsJsTemp.replace('%version%', version);
    fs.writeFileSync(componentFilePath, componentJs);

    const indexFilePath = './dist/index.html';
	let indexFileTemp = fs.readFileSync(indexFilePath, 'utf-8');
    let indexFile = indexFileTemp.replace(/%version%/g, version);
    fs.writeFileSync(indexFilePath, indexFile);
});