const gulp = require('gulp');
const webpackStream = require('webpack-stream');
let release = false;

if (process.argv.indexOf("--release") > -1) {
    release = true;
}

gulp.task('scripts', ['custom-modules'], () => {
    const webpackConfig = release ? require('../../webpack.config.prod.js') : require('../../webpack.config.dev.js');

    return gulp.src('./vendor/fe/src/main.js')
    	.pipe(webpackStream(webpackConfig, require('webpack')))
        .pipe(gulp.dest('./dist/js/'));
});