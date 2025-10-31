const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const sourcemaps = require('gulp-sourcemaps');

// Compile Light Theme
function compileLightTheme() {
    return gulp.src('./style-theme-light.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('/')); // Output to the same directory
}

// Compile Dark Theme
function compileDarkTheme() {
    return gulp.src('./style-theme-dark.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('./')); // Output to the same directory
}

// Compile Dark Theme
function compileSystemTheme() {
    return gulp.src('./style-theme-system.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('./')); // Output to the same directory
}

// Compile Layout

function compileLayout() {
    return gulp.src('./style.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('./')); // Output to the same directory
}

// Watch for changes
function watch() {
    gulp.watch('./style-theme-light.scss', compileLightTheme);
    gulp.watch('./style-theme-dark.scss', compileDarkTheme);
    gulp.watch('./style-theme-system.scss', compileSystemTheme);
    gulp.watch('./style.scss', compileLayout);
}

// Exports
exports.compileLightTheme = compileLightTheme;
exports.compileDarkTheme = compileDarkTheme;
exports.compileLayout=compileLayout;
exports.watch = watch;
