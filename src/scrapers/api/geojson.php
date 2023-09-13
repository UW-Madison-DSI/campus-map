<?php
/******************************************************************************\
|                                                                              |
|                                 geojson.php                                  |
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

//
// fetching methods
//

function fetchGeoJson($api, $id) {
	$url = $api . '/map_objects/' . $id . '.geojson';

	try {
		$contents = file_get_contents($url, false, stream_context_create([
			'http' => [
				'method'  => 'GET'
			]
		]));
	} catch (\Throwable $e) {
		echo "Error fetching geojson. \n";
		$contents = null;
	}

	// parse contents
	//
	$json = $contents? json_decode($contents) : null;

	return $json;
}

function fetchBuildingsGeoJson($api, $buildings) {
	for ($i = 0; $i < count($buildings); $i++) {
		$building = $buildings[$i];
		echo "Fetching geojson for building #" . $building->id . "\n";
		$buildings[$i]->geojson = fetchGeoJson($api, $building->id);
	}
	return $buildings;
}

//
// storing methods
//

function storeGeoJson($db, $id, $geojson) {

	// find if geojson already exists
	//
	if (!$geojson || exists($db, 'geojson', 'id', $id)) {
		return;
	}

	switch ($geojson->type) {

		case 'Polygon':
			$coordinates = '[';
			for ($i = 0; $i < count($geojson->coordinates); $i++) {
				$cycle = $geojson->coordinates[$i];
				if ($i > 0) {
					$coordinates .= ', ';
				}		
				$coordinates .= '[';
				for ($j = 0; $j < count($cycle); $j++) {
					$coordinate = $cycle[$j];
					if ($j > 0) {
						$coordinates .= ', ';
					}
					$coordinates .= '[';
					$coordinates .= $coordinate[0];
					$coordinates .= ", ";
					$coordinates .= $coordinate[1];
					$coordinates .= ']';
				}
				$coordinates .= ']';
			}
			$coordinates .= ']';
			break;

		case 'MultiPolygon':
			$coordinates = '[';
			for ($i = 0; $i < count($geojson->coordinates); $i++) {
				$polygon = $geojson->coordinates[$i];
				if ($i > 0) {
					$coordinates .= ', ';
				}
				$coordinates .= '[';
				for ($j = 0; $j < count($polygon); $j++) {
					$cycle = $polygon[$j];
					if ($j > 0) {
						$coordinates .= ', ';
					}
					$coordinates .= '[';
					for ($k = 0; $k < count($cycle); $k++) {
						$coordinate = $cycle[$k];
						if ($k > 0) {
							$coordinates .= ', ';
						}
						$coordinates .= '[';
						$coordinates .= $coordinate[0];
						$coordinates .= ", ";
						$coordinates .= $coordinate[1];
						$coordinates .= ']';
					}
					$coordinates .= ']';
				}
				$coordinates .= ']';
			}
			break;

		case 'Point':
			$coordinates = $geojson->coordinates;
			break;
	}

	insert($db, 'geojson', [
		'id' => $id,
		'type' => $geojson->type,
		'coordinates' => $coordinates
	]);

	return $id;
}