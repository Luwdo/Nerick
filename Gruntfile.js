module.exports = function(grunt) {
  grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {// Task
	    app: {// Target
		options: {// Target options
		    compass: true,
		    style: 'nested', //expanded
		    require: 'sass-css-importer'
		},
		files: {// Dictionary of files
		    'app/build/css/app.css': 'app/css/app.scss' // 'destination': 'source'
		}
	    }
	},
	browserify: {
	    app: {
		src: [
		    'app/js/core.js'
		],
		dest: 'app/build/js/app.js'
	    }
	},
	uglify: {
	    options: {
	      banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
		      compress: false,
		      mangle: false,
		      beautify: false
	    }, 
	    platform: {
	      src: [
			'app/bower_components/jquery/dist/jquery.min.js',
			'app/bower_components/slick-carousel/slick/slick.js',
			'app/bower_components/photoswipe/dist/photoswipe.js',
			'app/bower_components/photoswipe/dist/photoswipe-ui-default.js',
			'app/bower_components/datatables/media/js/jquery.dataTables.min.js',
			'app/js/bootstrapDataTables.js',
			'app/bower_components/angular/angular.js',
			'app/bower_components/angular-slick/dist/slick.js',
			'app/bower_components/angular-route/angular-route.min.js',
			'app/bower_components/angular-resource/angular-resource.min.js',
			'app/bower_components/lodash/dist/lodash.min.js',
			'app/bower_components/restangular/dist/restangular.min.js',
			'app/bower_components/datetimepicker/jquery.datetimepicker.js',
			'app/bower_components/magnific-popup/dist/jquery.magnific-popup.min.js',
			'app/bower_components/angular-datatables/dist/angular-datatables.min.js',
			'app/bower_components/ng-flow/dist/ng-flow-standalone.min.js',
			'app/bower_components/jquery-ui/jquery-ui.min.js',
			'app/bower_components/angular-ui-sortable/sortable.min.js',
			'app/bower_components/angular-animate/angular-animate.js',
			'app/bower_components/bootstrap-sass/assets/javascripts/bootstrap.js',
			'app/js/common.js',
		    ],
	      dest: 'app/build/js/platform.min.js'
	    },
	    app: {
		src: [
			'app/build/js/app.js'
		],
		dest: 'app/build/js/app.min.js'
	    }
	},
	watch: {
	    sass: {
		files: 'app/css/**/*.scss',
		tasks: ['sass']
	    },
	    browserify: {
		files: [
		    'Gruntfile.js',
		    'app/js/**/*.js',
		    'app/modules/**/*.js'
		],
		tasks: ['browserify', 'uglify'],
		options: {
		    reload: true
		}
	    }
	}
    });

  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-browserify');
  
  grunt.registerTask('default', ['sass', 'browserify']);

};

