const gulp = require('gulp');
const concat = require('gulp-concat');
const rename = require("gulp-rename");
const runSequence = require('run-sequence');

gulp.task('copy', (cb) => {
	return runSequence(
		['copy:assets', 'copy:html', 'copy:index'],
		'copy:fontawesome',
	cb);
});

// Copy assets
gulp.task('copy:assets', () => {
  	return gulp.src('./fe/assets/**/*')
    	.pipe(gulp.dest('./dist/assets/'));
});

// Copy html files with rename
gulp.task('copy:html', () => {
    return gulp.src('./fe/src/**/*.html')
		.pipe(rename((path) => {
			let split = path.dirname.split(/[\\\/]+/).pop();
			let newName = path.basename.replace(path.basename, split);
			path.dirname = '';
			path.basename = newName;
		}))
    	.pipe(gulp.dest('./dist/html/'));
});

// index.html
gulp.task('copy:index', () => {
	return gulp.src([
			'./fe/index.html',
		])
        .pipe(gulp.dest('./dist/'));
});

// FontAwesome fonts
gulp.task('copy:fontawesome', () => {
	return gulp.src('./node_modules/font-awesome/fonts/*')
        .pipe(gulp.dest('./dist/assets/font/'));
});