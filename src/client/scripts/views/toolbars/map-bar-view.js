/******************************************************************************\
|                                                                              |
|                              map-bar-view.js                                 |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This defines the toolbar used to control maps.                        |
|                                                                              |
|        Author(s): Abe Megahed                                                |
|                                                                              |
|        This file is subject to the terms and conditions defined in           |
|        'LICENSE.txt', which is part of this source code distribution.        |
|                                                                              |
|******************************************************************************|
|     Copyright (C) 2022, Data Science Institute, University of Wisconsin      |
\******************************************************************************/

import ToolbarView from './toolbar-view.js';

export default ToolbarView.extend({

	//
	// attributes
	//

	id: 'map-bar',
	className: 'vertical toolbar',

	template: _.template(`
		<div class="title">Map</div>

		<div class="buttons">
			<button id="show-map-mode" class="selected" data-toggle="tooltip" title="Map Mode" data-placement="left">
				<i class="fa fa-compass"></i>
			</button>

			<button id="show-aerial-mode" data-toggle="tooltip" title="Aerial Mode" data-placement="left">
				<i class="fa fa-plane"></i>
			</button>
		</div>
	`),

	events: {
		'click #show-map-mode': 'onClickShowMapMode',
		'click #show-aerial-mode': 'onClickShowAerialMode'
	},

	//
	// getting methods
	//

	getMapMode: function() {
		if (this.$el.find('#show-map-mode').hasClass('selected')) {
			return 'map';
		} else if (this.$el.find('#show-aerial-mode').hasClass('selected')) {
			return 'aerial';
		}
	},

	getQueryParams: function(params) {
		let mode = this.getMapMode();
		if (mode != 'map') {
			params.set('mode', mode);
		}
		return params;
	},

	//
	// setting methods
	//

	setMapMode: function(mode) {

		// set button states
		//
		switch (mode) {
			case 'map':
				this.$el.find('#show-map-mode').addClass('selected');
				this.$el.find('#show-aerial-mode').removeClass('selected');
				break;
			case 'aerial':
				this.$el.find('#show-map-mode').removeClass('selected');
				this.$el.find('#show-aerial-mode').addClass('selected');
				break;
		}

		// set map mode
		//
		this.parent.setMapMode(mode);
	},

	setQueryParams: function(params) {
		if (params.mode) {
			this.setMapMode(params.mode);
		}
	},

	//
	// mouse event handling methods
	//

	onClickShowMapMode: function() {
		if (this.getMapMode() != 'map') {
			this.setMapMode('map');
		}
	},

	onClickShowAerialMode: function() {
		if (this.getMapMode() != 'aerial') {
			this.setMapMode('aerial');
		}
	}
});