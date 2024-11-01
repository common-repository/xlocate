var gulp 			= require('gulp');
var sass 			= require('gulp-sass'); // compiles SASS to CSS
var sourcemaps 		= require('gulp-sourcemaps'); // generate css source maps
var notify 		 	= require('gulp-notify'); // provides notification to use once task is complete
var uglify 			= require('gulp-uglify'); // minifies js files
var uglifycss    	= require('gulp-uglifycss'); // minifies css files
var concat       	= require('gulp-concat');  //concatenates multiple js files 
var rename       	= require('gulp-rename'); // Renames files E.g. style.css -> style.min.css
var plumber 		= require('gulp-plumber');
var jshint 			= require('gulp-jshint');

/*-----------------------------------------------------------------------*/
var stylesSource 			 = './src/frontend/css/*.scss';
var styleDestination 		 = './assets/frontend/css/';
var styleFile 				 = 'style';
/*-----------------------------------------------------------------------*/
var jsAdminSource 			 = './src/admin/js/*.js';
var jsAdminDestination 	 	 = './assets/admin/js/';
var jsAdminFile	 		 	 = 'admin-script';
/*-----------------------------------------------------------------------*/
var jsFrontendSource 		 = './src/frontend/js/*.js';
var jsFrontendDestination 	 = './assets/frontend/js/';
var jsFrontendFile	 		 = 'frontend-script';
/*-----------------------------------------------------------------------*/

/*
	takes style.scss ,
	generates sourcemap
	generates css and put it css folder in route
*/
gulp.task('compileStyles', function(){
	return gulp.src(stylesSource)
			.pipe(plumber({errorHandler: notify.onError("Error: <%= error.message %>")}) )
			.pipe(sourcemaps.init())
			.pipe(sass())
			.pipe(sourcemaps.write('./maps'))
			.pipe(gulp.dest( styleDestination ))
			.pipe(uglifycss({
      				"maxLineLen": 80,
      				"uglyComments": true
    		}))
    		.pipe( rename( { suffix: '.min' } ) )
    		.pipe(gulp.dest( styleDestination ))
			.pipe( notify( { message: 'TASK: "styles" Completed! 💯', onLast: true } ) );
});

/*Compile Files in js/vendor intended for vendor scripts example bootstrap, meanmenu, etc*/
gulp.task('compileAdminJS', function(){
	return gulp.src(jsAdminSource)
		   .pipe(plumber({errorHandler: notify.onError("Error: <%= error.message %>")}) )
		   .pipe( jshint() )
		   .pipe(jshint.reporter('jshint-stylish'))
		   .pipe( concat( jsAdminFile + '.js' )  )
		   .pipe( gulp.dest( jsAdminDestination ) )
		   .pipe( rename( {
       			basename: jsAdminFile,
       			suffix: '.min'
    	 	}))
		   .pipe( uglify() )
		   .pipe( gulp.dest( jsAdminDestination ) )
		   .pipe( notify( { message: 'TASK: "compileVendorJS" Completed! 💯', onLast: true } ) );
});


/*Compile Files in Custom JS intended for non-vendor scripts*/
gulp.task('compileFrontendJS', function(){
	return gulp.src(jsFrontendSource)
		   .pipe(plumber({errorHandler: notify.onError("Error: <%= error.message %>")}) )
		   .pipe( jshint() )
		   .pipe(jshint.reporter('jshint-stylish'))
		   .pipe( concat( jsFrontendFile  + '.js' )  )
		   .pipe( gulp.dest( jsFrontendDestination ) )
		   .pipe( rename( {
       			basename: jsFrontendFile,
       			suffix: '.min'
    	 	}))
		   .pipe( uglify() )
		   .pipe( gulp.dest( jsFrontendDestination ) )
		   .pipe( notify( { message: 'TASK: "compileCustomJS" Completed! 💯', onLast: true } ) );
});


/*Default tasks that will be run when using "gulp" command*/
gulp.task('default', ['compileStyles', 'compileAdminJS', 'compileFrontendJS' ],  function(){
	gulp.watch( stylesSource, ['compileStyles'] );
	gulp.watch( jsAdminSource, ['compileAdminJS'] );
	gulp.watch( jsFrontendSource, ['compileFrontendJS'] );
});