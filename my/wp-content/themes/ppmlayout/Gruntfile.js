module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
		
        concat: {      
            dist: {
		        src: [
		        	'themes/ppmlayout/assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js',
		        	'themes/ppmlayout/assets/plugins/bootstrap/js/bootstrap.min.js',
		        	'themes/ppmlayout/assets/plugins/breakpoints/breakpoints.js',
		        	'themes/ppmlayout/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js',
		        	'themes/ppmlayout/assets/plugins/jquery.blockui.js',
		        	'themes/ppmlayout/assets/plugins/jquery.cookie.js',
		        	'themes/ppmlayout/assets/plugins/uniform/jquery.uniform.min.js',
		        	'themes/ppmlayout/assets/plugins/jqvmap/jqvmap/jquery.vmap.js',
		        	'themes/ppmlayout/assets/plugins/jqvmap/jqvmap/maps/jquery.vmap.russia.js',
		        	'themes/ppmlayout/assets/plugins/jqvmap/jqvmap/maps/jquery.vmap.world.js',
		        	'themes/ppmlayout/assets/plugins/jqvmap/jqvmap/maps/jquery.vmap.europe.js',
		        	'themes/ppmlayout/assets/plugins/jqvmap/jqvmap/maps/jquery.vmap.germany.js',
		        	'themes/ppmlayout/assets/plugins/jqvmap/jqvmap/maps/jquery.vmap.usa.js',
		        	'themes/ppmlayout/assets/plugins/jqvmap/jqvmap/data/jquery.vmap.sampledata.js',
		        	'themes/ppmlayout/assets/plugins/flot/jquery.flot.js',
		        	'themes/ppmlayout/assets/plugins/flot/jquery.flot.time.js',
		        	'themes/ppmlayout/assets/plugins/flot/jquery.flot.resize.js',
		        	'themes/ppmlayout/assets/plugins/data-tables/jquery.dataTables.js',
		        	'themes/ppmlayout/assets/plugins/data-tables/DT_bootstrap.js',
		        	'themes/ppmlayout/assets/plugins/jquery.pulsate.min.js',
		        	'themes/ppmlayout/assets/plugins/bootstrap-daterangepicker/date.js',
		        	'themes/ppmlayout/assets/plugins/bootstrap-daterangepicker/daterangepicker.js',
		        	'themes/ppmlayout/assets/plugins/gritter/js/jquery.gritter.js',
		        	'themes/ppmlayout/assets/plugins/fullcalendar/fullcalendar/fullcalendar.min.js',
		        	'themes/ppmlayout/assets/plugins/jquery-easy-pie-chart/jquery.easy-pie-chart.js',
		        	'themes/ppmlayout/assets/plugins/jquery.sparkline.min.js',
		        	'themes/ppmlayout/assets/scripts/app.js',
		        	'themes/ppmlayout/assets/scripts/index.js',
		            'themes/ppmlayout/js/progress-circle.js', 
		            'themes/ppmlayout/js/plugins.js', 
		            'themes/ppmlayout/js/main.js',
		            'themes/ppmlayout/js/jquery-validate.js',
		            'themes/ppmlayout/js/carhartl-jquery-cookie-3caf209/jquery.cookie.js',
		            'themes/ppmlayout/js/signon.js',
		            'themes/ppmlayout/share42/share42.js',
		            'themes/ppmlayout/js/blog-rotator.js',
		            'themes/ppmlayout/js/ajax-resources.js',
		            'themes/ppmlayout/js/fancy-product-designer.js'    
		        ],
		        dest: 'themes/ppmlayout/js/build/production.js'
		    }
        },
        uglify: {
		    build: {
		        src: 'themes/ppmlayout/js/build/production.js',
		        dest: 'themes/ppmlayout/js/build/production.min.js'
		    }
		},
		sass: {
		    dist: {
		        files: {
		            'themes/ppmlayout/css/build/style.css':'themes/ppmlayout/style.scss',
		            'themes/ppmlayout/css/build/checkout.css':'themes/ppmlayout/checkout.scss',
		            'themes/ppmlayout/css/shop/fancy-product-designer.css':'themes/ppmlayout/css/shop/fancy-product-designer.scss'
		        }
		    } 
		},
		cssmin: {
		  combine: {
		    files: {
		      'themes/ppmlayout/css/build/production.min.css': [
		      	'themes/ppmlayout/assets/plugins/bootstrap/css/bootstrap.min.css', 
		      	'themes/ppmlayout/assets/plugins/bootstrap/css/bootstrap-responsive.min.css',
		      	'themes/ppmlayout/assets/plugins/font-awesome/css/font-awesome.min.css',
		      	'themes/ppmlayout/assets/css/style-metro.css',
		      	'themes/ppmlayout/assets/css/style.css',
		      	'themes/ppmlayout/assets/css/style-responsive.css',
		      	'themes/ppmlayout/assets/css/themes/default.css',
		      	'themes/ppmlayout/assets/plugins/uniform/css/uniform.default.css',
		      	'themes/ppmlayout/assets/plugins/gritter/css/jquery.gritter.css',
		      	'themes/ppmlayout/assets/plugins/bootstrap-daterangepicker/daterangepicker.css',
		      	'themes/ppmlayout/assets/plugins/fullcalendar/fullcalendar/fullcalendar.css',
		      	'themes/ppmlayout/assets/plugins/jqvmap/jqvmap/jqvmap.css',
		      	'themes/ppmlayout/assets/plugins/jquery-easy-pie-chart/jquery.easy-pie-chart.css',
		      	'themes/ppmlayout/assets/css/pages/blog.css',
		      	'themes/ppmlayout/assets/css/pages/news.css',
		      	'themes/ppmlayout/css/build/style.css',
		        'themes/ppmlayout/css/shop/fancy-product-designer.css',
		        'plugins/transactional-data/css/style-frontend.css',
		        'plugins/monthly-statements/css/style-frontend.css'
		      ],
		      'themes/ppmlayout/css/build/checkout.production.min.css': [
		      	'themes/ppmlayout/css/build/checkout.css',
		      	'themes/ppmlayout/assets/plugins/bootstrap/css/bootstrap.min.css', 
		      	'themes/ppmlayout/assets/plugins/bootstrap/css/bootstrap-responsive.min.css'
		      ]
		    }
		  }
		},
		watch: {
			options: {
		        livereload: true,
		    },
		    scripts: {
		        files: ['themes/ppmlayout/js/*.js'],
		        tasks: ['concat', 'uglify'],
		        options: {
		            spawn: false,
		        }
		    },
			css: {
			    files: ['themes/ppmlayout/*.{css,scss}','themes/ppmlayout/css/*.{css,scss}','themes/ppmlayout/css/*/*.{css,scss}'],
			    tasks: ['sass', 'cssmin'],
			    options: {
			        spawn: false,
			    }
			}
		}
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-watch');

    // Change base to "wp-content" folder
    grunt.file.setBase('../../');
	
    grunt.registerTask('default', ['concat', 'uglify', 'sass', 'cssmin', 'watch']);
};
