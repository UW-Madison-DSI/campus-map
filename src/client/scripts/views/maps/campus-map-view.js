/******************************************************************************\
|                                                                              |
|                             campus-map-view.js                               |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This defines a map view showing campus buildings.                     |
|                                                                              |
|        Author(s): Abe Megahed                                                |
|                                                                              |
|        This file is subject to the terms and conditions defined in           |
|        'LICENSE.txt', which is part of this source code distribution.        |
|                                                                              |
|******************************************************************************|
|     Copyright (C) 2022, Data Science Institute, University of Wisconsin      |
\******************************************************************************/

import Vector2 from '../../utilities/math/vector2.js';
import Buildings from '../../collections/buildings.js';
import Departments from '../../collections/departments.js';
import BaseMapView from '../../views/maps/base-map-view.js';
import DepartmentMarkerView from '../../views/maps/overlays/departments/department-marker-view.js';
import BuildingsView from '../../views/maps/overlays/buildings/buildings-view.js';
import LabelsView from '../../views/maps/overlays/labels/labels-view.js';

export default BaseMapView.extend({

	//
	// constructor
	//

	initialize(options) {

		// set options
		//
		options.layers = ['buildings', 'labels'];

		// call superclass constructor
		//
		BaseMapView.prototype.initialize.call(this, options);
	},

	//
	// fetching methods
	//

	fetchBuildings: function(options) {
		new Buildings().fetch({

			// callbacks
			//
			success: (collection) => {
				let excluded = [202, 1299, 1239];
				let buildings = new Buildings();

				// filter buildings
				//
				for (let i = 0; i < collection.length; i++) {
					let building = collection.at(i);
					let number = building.get('building_number');
					if (!excluded.includes(number)) {
						buildings.add(building);
					}
				}

				// perform callback
				//
				if (options && options.success) {
					options.success(buildings);
				}
			}
		});
	},

	fetchDepartments: function(options) {
		new Departments().fetch({
			full: options && options.full,

			// callbacks
			//
			success: (collection) => {

				// link departments with buildings
				//
				if (options.buildings) {

					// create index of buildings
					//
					let directory = [];
					for (let i = 0; i < options.buildings.length; i++) {
						let building = options.buildings.at(i);
						directory[building.get('id')] = building;
					}

					// add link from department to building
					//
					for (let i = 0; i < collection.length; i++) {
						let department = collection.at(i);
						department.building = directory[department.get('building_id')];
					}
				}

				// perform callback
				//
				if (options && options.success) {
					options.success(collection);
				}
			}
		});
	},

	//
	// getting methods
	//

	getDepartmentLocation: function(name) {
		let department = this.departments[name];
		if (department) {
			return department.location;
		}
	},

	getBuildingLocation: function(buildingNumber) {
		let building = this.buildings.findByNumber(buildingNumber);
		if (building) {
			let latlng = building.get('latlng');
			return this.latLongToPoint(latlng[0], latlng[1]);
		}	
	},

	getRGBColor: function(r, g, b, a) {
		if (a == undefined) {
			return 'rgb(' + r + ', ' + g + ', ' + b + ')';
		} else {
			return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + a + ')';
		}
	},

	//
	// setting methods
	//

	setDepartments: function(departments) {

		// build department directory
		//
		this.departments = [];
		for (let i = 0; i < departments.length; i++) {
			let department = departments.at(i);
			let name = department.get('name');
			this.departments[name] = department;
		}
	},

	setInstitutionUnits: function(institutionUnits) {

		// build institution unit directory
		//
		this.institutionUnits = [];
		for (let i = 0; i < institutionUnits.length; i++) {
			let institutionUnit = institutionUnits.at(i);
			let name = institutionUnit.get('name');
			this.institutionUnits[name] = institutionUnit;
		}
	},

	setMapMode: function(mapMode) {

		// check if current mode matches desired mode
		//
		if (this.mapMode == mapMode) {
			return;
		}

		// update map tiles
		//
		if (mapMode == 'graph') {
			this.fadeOut();
		} else {
			if (this.mapMode == 'graph') {
				this.fadeIn();
			}

			// update map
			//
			BaseMapView.prototype.setMapMode.call(this, mapMode);
		}

		// set attributes
		//
		this.mapMode = mapMode;
	},

	//
	// rendering methods
	//

	onAttach: function() {

		// call superclass method
		//
		BaseMapView.prototype.onAttach.call(this);

		this.hideGrid();

		// add details
		//
		this.addBuildings(() => {

			// activate ui
			//
			if (this.start) {
				this.start();
			}
		});
	},

	addBuildings: function(done) {
		const show_departments = false;

		this.fetchBuildings({

			// callbacks
			//
			success: (buildings) => {
				this.buildings = buildings;
				this.showBuildings(buildings);
			}
		});
	},

	showBuildings: function(buildings) {
		let scale = Math.pow(2, this.options.zoom_level - 14);

		this.buildingsView = new BuildingsView({
			collection: buildings,
			offset: new Vector2(-this.map.longitude, this.map.latitude),
			scale: new Vector2(11650, 15950).scaledBy(scale),
			parent: this
		});

		this.viewport.addLayerGroup('buildings', this.buildingsView.render());
		this.buildings = buildings;
	},

	fadeOut: function() {
		this.viewport.$el.find('#tiles').fadeOut();
		this.viewport.$el.find('#departments').fadeTo(500, 1);
		this.viewport.$el.find('#departments').show();
		this.viewport.$el.find('#buildings').fadeTo(500, 0);
		this.viewport.$el.find('#buildings').css('pointer-events', 'none');
		this.viewport.$el.find('#labels').removeClass('dark');
		$('#background').fadeIn();
	},

	fadeIn: function() {
		this.viewport.$el.find('#tiles').fadeIn();
		this.viewport.$el.find('#departments').show();
		this.viewport.$el.find('#departments').fadeTo(500, 0);
		this.viewport.$el.find('#buildings').fadeTo(500, 1);
		this.viewport.$el.find('#buildings').css('pointer-events', 'auto');
		this.viewport.$el.find('#labels').removeClass('dark');
		$('#background').fadeOut();
	},

	showMapLabels: function() {
		this.map.labels = true;
		this.tiles.render();
	},

	hideMapLabels: function() {
		this.map.labels = false;
		this.tiles.render();
	},

	//
	// mouse event handling methods
	//

	onClickLabel: function() {

		// do nothing
		//
	}
});