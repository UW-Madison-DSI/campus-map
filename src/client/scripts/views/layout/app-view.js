/******************************************************************************\
|                                                                              |
|                                 app-view.js                                  |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This defines the top level view of our application.                   |
|                                                                              |
|        Author(s): Abe Megahed                                                |
|                                                                              |
|        This file is subject to the terms and conditions defined in           |
|        'LICENSE.txt', which is part of this source code distribution.        |
|                                                                              |
|******************************************************************************|
|     Copyright (C) 2022, Data Science Institute, University of Wisconsin      |
\******************************************************************************/

import Buildings from '../../collections/buildings.js';

import SplitView from '../layout/split-view.js';
import MainView from './main-view.js';
import InfoView from './info-view.js';

// dialog views
//
import HelpDialogView from '../dialogs/help-dialog-view.js';
import MessageDialogView from '../dialogs/message-dialog-view.js';
import DownloadDialogView from '../dialogs/download-dialog-view.js';

//
// fetching methods
//

export default SplitView.extend({

	//
	// constructor
	//

	onRender: function() {

		// call superclass method
		//
		SplitView.prototype.onRender.call(this);

		// set initial state
		//
		this.clearSideBar();

		// add region for dialogs
		//
		this.addRegion('overlays', {
			el: $('#overlays'),
			replaceElement: false
		});
	},

	//
	// setting methods
	//

	setYear: function(value) {
		if (this.getChildView('sidebar').setYear) {
			this.getChildView('sidebar').setYear(value);
		}
		this.getChildView('mainbar').setYear(value);
	},

	setRange: function(values) {
		if (this.getChildView('sidebar').setRange) {
			this.getChildView('sidebar').setRange(values);
		}
		this.getChildView('mainbar').setRange(values);
	},

	//
	// sidebar rendering methods
	//

	clearSideBar: function() {
		this.showChildView('sidebar', new InfoView());
	},

	//
	// dialog rendering methods
	//

	showDialog: function(dialogView) {
		this.showChildView('overlays', dialogView);
	},

	showHelp: function() {
		this.showDialog(new HelpDialogView());
	},

	showMessage: function(options) {
		this.messageDialogView = new MessageDialogView(options);
		this.showDialog(this.messageDialogView);
	},

	hideMessage: function() {
		if (this.messageDialogView) {
			this.messageDialogView.destroy();
			this.messageDialogView = null;
		}
	},

	showDownloadDialog: function(options) {
		this.showDialog(new DownloadDialogView(options));
	},

	//
	// mainbar rendering methods
	//

	getMainBarView: function() {
		return new MainView();
	},

	//
	// window event handling methods
	//

	onResize: function() {
		this.getChildView('mainbar').onResize();
	}
});