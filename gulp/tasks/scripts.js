const gulp = require('gulp');
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');
const babel = require('gulp-babel');
const runSequence = require('run-sequence');
const webpackStream = require('webpack-stream');
const webpack = require('webpack');

gulp.task('scripts', ['custom-modules'], () => {
	const webpackConfig = require('../../webpack.config.js');

    return gulp.src('./vendor/fe/src/main.js')
    	.pipe(webpackStream(webpackConfig))
        .pipe(gulp.dest('./dist/js/'));
});