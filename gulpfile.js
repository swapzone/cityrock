var gulp = require('gulp');

var uglify      = require('gulp-uglify'),
    rename      = require('gulp-rename'),
    ts          = require('gulp-typescript'),
    sass        = require('gulp-ruby-sass'),
    prefixer    = require('gulp-autoprefixer'),
    concat      = require('gulp-concat'),
    gulpif      = require('gulp-if'),
    cssmin      = require('gulp-cssmin'),
    notify      = require('gulp-notify'),
    plumber     = require('gulp-plumber'),
    livereload  = require('gulp-livereload');

var path = require('path');

var destPath = './site/';

var notifyInfo = {
  title: 'Gulp'
};

// error notification settings for plumber
var plumberErrorHandler = {
  errorHandler: notify.onError({
    title: notifyInfo.title,
    icon: notifyInfo.icon,
    message: "Error: <%= error.message %>"
  })
};

gulp.task('scripts', function() {
  var dest = path.join(destPath, 'scripts');

  return gulp.src([
    'bower_components/lodash/dist/lodash.js',
    'bower_components/jquery/dist/jquery.js',
    'bower_components/moment/moment.js',
    'bower_components/fullcalendar/dist/fullcalendar.js',
    'bower_components/fullcalendar/dist/lang/de.js',
    'scripts/*.ts'
  ])
  .pipe(plumber(plumberErrorHandler))
  .pipe(
    gulpif(
      /[.]ts$/,
      ts({
        declarationFiles: false,
        noExternalResolve: false,
        target: 'ES5'
      })
    )
  )
  .pipe(concat('script.js'))
  .pipe(gulp.dest(dest))
  .pipe(uglify())
  .pipe(rename({ suffix: '.min' }))
  .pipe(gulp.dest(dest));
});

gulp.task('fonts', function() {
  var dest = path.join(destPath, 'styles/fonts');

  return gulp.src('bower_components/font-awesome/fonts/*')
    .pipe(gulp.dest(dest));
});

gulp.task('styles', function() {
  var dest = path.join(destPath, 'styles');

  return gulp.src([
      'bower_components/fullcalendar/dist/fullcalendar.css',
      'styles/*.scss'
    ])
    .pipe(plumber(plumberErrorHandler))
    .pipe(
      gulpif(
        /[.]scss$/,
        sass({ style: 'expanded', 'sourcemap=none': true })
      )
    )
    .pipe(concat('style.css'))
    .pipe(gulp.dest(dest))
    .pipe(prefixer({
      browsers: ['last 2 versions'],
      cascade: false
    }))
    .pipe(gulp.dest(dest))
    .pipe(cssmin())
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest(dest));
});

gulp.task('live', ['scripts', 'fonts', 'styles'], function() {
  livereload.listen();
  gulp.watch('styles/**', ['styles']);
  gulp.watch('scripts/**', ['scripts']);
  gulp.watch([
    destPath + '**/*.php',
    destPath + 'styles/style.css',
    destPath + 'scripts/script.min.js'
  ]).on('change', livereload.changed);
});

gulp.task('default', ['scripts', 'fonts', 'styles'], function(){ });
