export default {
	input: '../scripts/main.js',

	external: [
		'https://apis.google.com/js/api.js',
		'https://www.dropbox.com/static/api/2/dropins.js',
		'https://apis.google.com/js/api.js'
	],

	output: {
		format: 'es',
		dir: '../../campus-map-built/scripts',
		inlineDynamicImports: false,
		chunkFileNames: '[name].js'
	},

	manualChunks: {

		// library chunks
		//
		'jquery': ['../library/jquery/jquery-3.6.0.js'],
		'lodash': ['../library/lodash/lodash.js'],
		'backbone': ['../library/backbone/backbone.js'],
		'marionette': ['../library/backbone/marionette/backbone.marionette.js'],
		'underscore': ['../library/underscore/underscore.js'],

		// vendor chunks
		//
		'splitjs': ['../vendor/split-js/split.js']
	}
};