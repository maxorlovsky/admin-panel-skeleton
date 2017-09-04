const gulp = require('gulp');
const runSequence = require('run-sequence');

require('require-dir')('./tasks');

gulp.task('build', (cb) => {
	return runSequence(
		'clean',
		'custom-modules',
		'lint:script',
		'copy',
		['styles', 'scripts'],
		'bump-ver',
		'minify',
	cb);
});