'use strict';
module.exports = function(grunt) {

	// load all grunt tasks matching the `grunt-*` pattern
	require('load-grunt-tasks')(grunt);

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		// Watch for changes and trigger less, jshint, uglify and livereload
		watch: {
			options: {
				livereload: true
			},
			scripts: {
				files: ['public/js/src/*.js'],
				tasks: ['jshint', 'uglify']
			},
			styles: {
				files: ['public/css/src/*.scss'],
				tasks: ['sass', 'postcss']
			}
		},

		sass: {
			// options: {
			// 	sourceMap: true
			// },
			dist: {
				files: {
				    'public/css/public.css': 'public/css/src/public.scss',
				}
			}
		},

		// PostCSS handles post-processing on CSS files. Used here to autoprefix and minify.
		postcss: {
			options: {
				map: {
					inline: false, // save all sourcemaps as separate files...
					annotation: 'public/css/' // ...to the specified directory
				},
				processors: [
					require('postcss-flexbugs-fixes'),
					require('autoprefixer')({browsers: 'last 2 versions'}),
					require('cssnano')({ discardUnused: { fontFace: false }, zindex: false })
				]
			},
			dist: {
				src: 'public/css/*.css'
			}
		},

		// JavaScript linting with jshint
		jshint: {
			all: ['public/js/src/*.js']
		},

		// Uglify to concat, minify, and make source maps
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
						'<%= grunt.template.today("yyyy-mm-dd") %> */'
			},
			common: {
				files: {
					'admin/assets/js/public.min.js': ['admin/assets/js/src/*.js']
				}
			}
		},

		// Image optimization
		imagemin: {
			dist: {
				options: {
					optimizationLevel: 7,
					progressive: true,
					interlaced: true
				},
				files: [{
					expand: true,
					cwd: 'public/images/',
					src: ['**/*.{png,jpg,gif}'],
					dest: 'public/images/'
				}]
			}
		}

	});

	// Register tasks
	// Typical run, cleans up css and js, starts a watch task.
	grunt.registerTask('default', ['sass', 'postcss', 'jshint', 'uglify:common', 'watch']);

	// Before releasing a build, do above plus minimize all images.
	grunt.registerTask('build', ['sass', 'postcss',  'jshint', 'uglify:common', 'imagemin']);

};