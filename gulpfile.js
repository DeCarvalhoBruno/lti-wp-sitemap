var gulp = require( 'gulp' ),
    plumber = require( 'gulp-plumber' ),
    watch = require( 'gulp-watch' ),
    minifycss = require( 'gulp-minify-css' ),
    uglify = require( 'gulp-uglify' );

var onError = function( err ) {
    console.log( 'An error occurred:', err.message );
    this.emit( 'end' );
};

gulp.task( 'css', function() {
    return gulp.src( './assets/dev/css/lti_sitemap_admin.css' )
        .pipe( plumber( { errorHandler: onError } ) )
        .pipe( minifycss() )
        .pipe( gulp.dest( './assets/dist/css/' ) )
} );

gulp.task( 'js', function() {
    return gulp.src( './assets/dev/js/lti_sitemap_admin.js' )
        .pipe( uglify() )
        .pipe( gulp.dest( './assets/dist/js/' ) )
} );

gulp.task( 'watch', function() {
    gulp.watch( './assets/dev/css/*.css', [ 'css' ] );
    gulp.watch( './assets/dev/js/*.js', [ 'js' ] );

} );

gulp.task( 'default', ['watch'], function() {
} );