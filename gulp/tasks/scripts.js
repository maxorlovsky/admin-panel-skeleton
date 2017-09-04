const gulp = require('gulp');
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');
const babel = require('gulp-babel');
const runSequence = require('run-sequence');
const gwebpack = require('gulp-webpack');
const webpack = require('webpack');

gulp.task('scripts', () => {
	const webpackConfig = require('../../webpack.config.js');

    return gulp.src('./vendor/fe/src/main.js')
    	.pipe(gwebpack(webpackConfig, webpack))
        .pipe(gulp.dest('./dist/js/'));
});