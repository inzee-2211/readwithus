const {src, dest, watch, parallel} = require('gulp');
const sass = require('gulp-sass')(require('sass'));
sass.compiler = require('node-sass');

const autoprefixer = require('gulp-autoprefixer');
function css() {
    var common = src('scss/common*.scss')
            .pipe(sass())
            .pipe(autoprefixer())
            .pipe(dest('application/views/css'))
            .pipe(dest('dashboard/views/css'));
    var frontend = src('scss/frontend*.scss')
            .pipe(sass())
            .pipe(autoprefixer())
            .pipe(dest('application/views/css'));
    var dashboard = src('scss/dashboard*.scss')
            .pipe(sass())
            .pipe(autoprefixer())
            .pipe(dest('dashboard/views/css'));
    var course = src('scss/course-personal*.scss')
            .pipe(sass())
            .pipe(autoprefixer())
            .pipe(dest('dashboard/views/css'));
    return (common, frontend, dashboard, course);
}

function watchFiles() {
    watch(['scss'], parallel(css));
}

exports.default = parallel(css);
exports.watch = watchFiles;
