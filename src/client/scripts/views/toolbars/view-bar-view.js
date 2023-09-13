/******************************************************************************\
|                                                                              |
|                             view-bar-view.js                                 |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This defines a toolbar view for settings.                             |
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

	id: 'view-bar',
	className: 'vertical toolbar',

	template: _.template(`
		<div class="title">View</div>

		<div class="buttons">

			<button id="toggle-fullscreen" data-toggle="tooltip" title="Toggle Fullscreen" data-placement="left">
				<i class="fa fa-desktop"></i>
			</button>

			<button id="fit-to-view" data-toggle="tooltip" title="Fit to View" data-placement="left">
				<i class="fa fa-expand"></i>
			</button>
		</div>
	`),

	events: {
		'click #toggle-fullscreen': 'onClickToggleFullscreen',
		'click #fit-to-view': 'onClickFitToView'
	},

	//
	// querying methods
	//

	isFullScreenSelected: function() {
		return this.$el.find('#toggle-fullscreen').hasClass('selected');
	},

	//
	// rendering methods
	//

	onRender: function() {

		// call superclass method
		//
		ToolbarView.prototype.onRender.call(this);

		// set initial state
		//
		if (!this.$el.find('#show-grid').hasClass('selected')) {
			this.parent.hideGrid();
		}
	},

	//
	// mouse event handling methods
	//

	onClickToggleFullscreen: function() {
		if (!this.isFullScreenSelected()) {
			this.$el.find('#toggle-fullscreen').addClass('selected');
			this.parent.requestFullScreen();
		} else {
			this.$el.find('#toggle-fullscreen').removeClass('selected');
			this.parent.exitFullScreen();
		}
	},

	onClickFitToView: function() {
		this.parent.resetView();
	}
});