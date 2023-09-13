<?php
/******************************************************************************\
|                                                                              |
|                                 parking.php                                  |
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

//
// fetching methods
//

function fetchParking($api) {
	$url = $api . '/parking';

	try {
		$contents = file_get_contents($url, false, stream_context_create([
			'http' => [
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'method'  => 'GET',
				'timeout' => TIMEOUT
			]
		]));
	} catch (\Throwable $e) {
		echo "Error fetching parking. \n";
	}

	if ($contents) {
		$json = json_decode($contents);
	}

	return $json;
}

//
// storing methods
//

function storeParking($db, $parking) {

	// find if parking already exists
	//
	if (!$parking || exists($db, 'parking', 'id', $parking->id)) {
		return;
	}

	insert($db, 'parking', [
		'id' => $parking->id,
		'name' => $parking->name,
		'building_number' => $parking->building_number,
		'department_ids' => getIds($parking->departments),
		'description' => $parking->description,
		'hours' => $parking->hours,
		'geojson_id' => storeGeoJson($db, $parking->id, $parking->geojson),
		'medium_image' => $parking->images->medium,
		'thumb_image' => $parking->images->thumb,
		'latitude' => $parking->latlng[0],
		'longitude' => $parking->latlng[1],
		'object_type' => $parking->object_type,
		'street_address' => $parking->street_address,
		'tag_ids' => getIds($parking->tags),
		'thumbnail' => $parking->thumbnail
	]);
}

function storeParkings($db, $parkings) {
	if ($buildings) {
		echo "Storing " . count($parkings) . " parkings...\n";
		for ($i = 0; $i < count($parkings); $i++) {
			storeGrant($db, $parkings[$i]);
		}
	}
}