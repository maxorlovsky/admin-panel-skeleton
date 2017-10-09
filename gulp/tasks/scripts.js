const gulp = require('gulp');
const webpackStream = require('webpack-stream');

gulp.task('scripts', ['custom-modules'], () => {
	const webpackConfig = require('../../webpack.config.js');

    return gulp.src('./vendor/fe/src/main.js')
    	.pipe(webpackStream(webpackConfig))
        .pipe(gulp.dest('./dist/js/'));
});