module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    concat: {
      options: {
        separator: ';'
      },
      dist: {
        src: ['app/webroot/js/redesign_2013/fancybox/jquery.mousewheel-3.0.6.pack.js',
        	'app/webroot/js/redesign_2013/fancybox/jquery.fancybox.js',
        	'app/webroot/js/redesign_2013/jquery.easing.js',
        	'app/webroot/js/redesign_2013/jquery.slidorion.js',
        	'app/webroot/js/redesign_2013/products_pagination.js',
        	'app/webroot/loadmask/jquery.loadmask.min.js',
        	'app/webroot/js/redesign_2013/comment_form_management.js',
        	'app/webroot/js/redesign_2013/jquery-ui.js',
        	'app/webroot/jRating-master/jquery/jRating.jquery.js',
        	'app/webroot/js/redesign_2013/product_rating_management.js'
        	],
        dest: 'app/webroot/js/redesign_2013/concatenated.js'
      }
    },
    uglify: {
      options: {
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
      },
      dist: {
        files: {
          'app/webroot/js/redesign_2013/hp.min.js': ['<%= concat.dist.dest %>']
        }
      }
    },
    qunit: {
      files: ['test/**/*.html']
    },
    jshint: {
      files: ['Gruntfile.js', 'src/**/*.js', 'test/**/*.js'],
      options: {
        // options here to override JSHint defaults
        globals: {
          jQuery: true,
          console: true,
          module: true,
          document: true
        }
      }
    },
    watch: {
      files: ['<%= jshint.files %>'],
      tasks: ['jshint', 'qunit']
    },
    cssmin : {
        minify : {
            src : ["app/webroot/css/redesign_2013/style000.css"],
            dest : "app/webroot/css/redesign_2013/style000.min.css"
        }
    }

  });

  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-qunit');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-concat');
  //load cssmin plugin
  grunt.loadNpmTasks('grunt-contrib-cssmin');

  grunt.registerTask('test', ['jshint', 'qunit']);

  grunt.registerTask('default', ['jshint', 'qunit', 'concat', 'uglify', 'cssmin']);

};