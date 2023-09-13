<?php
/******************************************************************************\
|                                                                              |
|                                buildings.php                                 |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This defines an interface for fetching and storing information        |
|        from the Academic Analytics database.                                 |
|                                                                              |
|        Author(s): Abe Megahed                                                |
|                                                                              |
|        This file is subject to the terms and conditions defined in           |
|        'LICENSE.txt', which is part of this source code distribution.        |
|                                                                              |
|******************************************************************************|
|  Copyright (C) 2022 Data Science Institute, Univeristy of Wisconsin-Madison  |
\******************************************************************************/

include_once "api/geojson.php";
include_once "api/departments.php";

//
// fetching methods
//

function fetchBuildings($api) {
	$url = $api . '/buildings';

	try {
		$contents = file_get_contents($url, false, stream_context_create([
			'http' => [
				'method'  => 'GET'
			]
		]));
	} catch (\Throwable $e) {
		echo "Error fetching buildings. \n";
		$contents = null;
	}

	// parse contents
	//
	$json = $contents? json_decode($contents) : null;

	return $json;
}

function fetchBuilding($api, $id) {
	$url = $api . '/buildings/' . $id;

	try {
		$contents = file_get_contents($url, false, stream_context_create([
			'http' => [
				'method'  => 'GET'
			]
		]));
	} catch (\Throwable $e) {
		echo "Error fetching building #$id. \n";
		$contents = null;
	}

	// parse contents
	//
	$json = $contents? json_decode($contents) : null;

	return $json;
}

//
// storing methods
//

function storeBuilding($db, $building) {

	// find if building already exists
	//
	if (!$building || exists($db, 'buildings', 'id', $building->id)) {
		return;
	}

	insert($db, 'buildings', [
		'id' => $building->id,
		'name' => $building->name,
		'building_number' => $building->building_number,
		'department_ids' => storeDepartments($db, $building->departments, $building),
		'description' => $building->description,
		'hours' => $building->hours,
		'geojson_id' => storeGeoJson($db, $building->id, $building->geojson),
		'medium_image' => $building->images->medium,
		'thumb_image' => $building->images->thumb,
		'latitude' => $building->latlng[0],
		'longitude' => $building->latlng[1],
		'object_type' => $building->object_type,
		'street_address' => $building->street_address,
		'tag_ids' => getIds($building->tags),
		'thumbnail' => $building->thumbnail
	]);
}

function storeBuildings($db, $buildings) {
	if (!$buildings) {
		return;
	}

	echo "Storing " . count($buildings) . " buildings...\n";
	for ($i = 0; $i < count($buildings); $i++) {
		storeBuilding($db, $buildings[$i]);
	}
}