module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		sass: {
			app: {
				options: {
					compass: true,
					style: 'nested'
				},
				files: {
					'build/css/app.css': 'src/css/app.scss' 
				}
			}
		},
		concat: {
			platformCss: {
				src: [
				    'bower_components/datetimepicker/jquery.datetimepicker.css',
				    'bower_components/magnific-popup/dist/magnific-popup.css',
				    'bower_components/magnific-popup/dist/magnific-popup.css',
				    //'bower_components/bootstrap-sass/assets/stylesheets/bootstrap',
				    'bower_components/angular-datatables/dist/plugins/bootstrap/datatables.bootstrap.min.css',
				    'bower_components/slick-carousel/slick/slick.css',
				    'bower_components/slick-carousel/slick/slick-theme.css',
				    /*'bower_components/datatables/media/css/jquery.dataTables'*/
				    'bower_components/photoswipe/dist/photoswipe.css',
				    'bower_components/photoswipe/dist/default-skin/default-skin.css',
				],
				dest: 'build/css/platform.css'
			},
		},

		browserify: {
			app: {
			src: [
//			    'src/js/bootstrapDataTables.js',
			    'src/js/common.js',
			    'src/nerick/nerick.js'
			],
			dest: 'build/js/app.js'
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
				'bower_components/jquery/dist/jquery.min.js',
				'bower_components/slick-carousel/slick/slick.js',
				'bower_components/photoswipe/dist/photoswipe.js',
				'bower_components/photoswipe/dist/photoswipe-ui-default.js',
				'bower_components/datatables/media/js/jquery.dataTables.min.js',
				'bower_components/angular/angular.js',
				'bower_components/angular-datatables/dist/angular-datatables.min.js',				
				'bower_components/angular-datatables/dist/plugins/bootstrap/angular-datatables.bootstrap.min.js',
				'bower_components/angular-slick/dist/slick.js',
				'bower_components/angular-route/angular-route.min.js',
				'bower_components/angular-resource/angular-resource.min.js',
				'bower_components/lodash/dist/lodash.min.js',
				'bower_components/restangular/dist/restangular.min.js',
				'bower_components/datetimepicker/jquery.datetimepicker.js',
				'bower_components/magnific-popup/dist/jquery.magnific-popup.min.js',
				'bower_components/ng-flow/dist/ng-flow-standalone.min.js',
				'bower_components/jquery-ui/jquery-ui.min.js',
				'bower_components/angular-ui-sortable/sortable.min.js',
				'bower_components/angular-animate/angular-animate.js',
				'bower_components/bootstrap-sass/assets/javascripts/bootstrap.js',
				],
			  dest: 'build/js/platform.min.js'
			},
			app: {
				src: [
					'build/js/app.js'
				],
				dest: 'build/js/app.min.js'
			}
		},

		copy: {
			index: {
				src: 'app.html',
				dest: 'build/index.html'
			},
			devIndex: {
				src: 'app.html',
				dest: 'build/dev.html'
			},
			
			assets: {
				expand: true,
				cwd: 'src',
				src: 'assets/**',
				dest: 'build/'
			},

			html: {
				expand: true,
				cwd: 'src',
				src: 'nerick/**/*.html',
				dest: 'build/html'
			}
		},

		watch: {
			gruntfile: {
				files: 'Gruntfile.js',
				tasks: ['default'],
				options: {
					reload: true
				}
			},

			assets: {
				files: 'src/assets/**',
				tasks: ['copy']
			},
			
			sass: {
				files: 'src/css/**/*.scss',
				tasks: ['sass']
			},
			copies: { 
				files: [
					'app.html',
					'src/nerick/**/*.html'  
				],
				tasks: ['copy']
			},
			browserify: {
				files: [
					'src/**/*.js'
				],
				tasks: ['browserify']
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-browserify');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-copy');

	grunt.registerTask('default', ['sass', 'browserify', 'uglify', 'concat', 'copy']);

};
