<?php
/******************************************************************************\
|                                                                              |
|                             fetch_buildings.php                              |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This script fetches information for all buildings from the            |
|        campus map and stores it in a database.                               |
|                                                                              |
|        Author(s): Abe Megahed                                                |
|                                                                              |
|        This file is subject to the terms and conditions defined in           |
|        'LICENSE.txt', which is part of this source code distribution.        |
|                                                                              |
|******************************************************************************|
|  Copyright (C) 2022 Data Science Institute, Univeristy of Wisconsin-Madison  |
\******************************************************************************/

require('vendor/autoload.php');
include 'utilities/database.php';
include 'api/api.php';

// main
//
$buildings = fetchBuildings($api);
$verbose = false;

if ($buildings) {
	for ($i = 0; $i < count($buildings); $i++) {
		$building = $buildings[$i];
		$building->geojson = fetchGeoJson($api, $building->id)->geojson;

		if ($verbose) {
			echo "BUILDING:\n";
			print_r($building);
		} else {
			echo "Storing building #" . $building->id . "\n";
		}
		storeBuilding($db, $building);
	}
}