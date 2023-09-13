<?php
/******************************************************************************\
|                                                                              |
|                               departments.php                                |
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
// storing methods
//

function storeDepartment($db, $department, $building) {

	// find if department already exists
	//
	if (!$department || exists($db, 'departments', 'id', $department->id)) {
		return;
	}

	$baseName = $department->name;
	$baseName = str_replace(", Dept of", '', $baseName);
	$baseName = str_replace(", Department of", '', $baseName);
	$baseName = str_replace(", Dept of", '', $baseName);
	$baseName = str_replace(", Laboratory of", '', $baseName);
	$baseName = str_replace(", Division of", '', $baseName);
	$baseName = str_replace(", Office for", '', $baseName);
	$baseName = str_replace(", Institute for", '', $baseName);
	$baseName = str_replace("'s Office", '', $baseName);
	$baseName = str_replace(" Program", '', $baseName);
	$baseName = str_replace(" Division", '', $baseName);
	$baseName = str_replace(" Center", '', $baseName);

	insert($db, 'departments', [
		'id' => $department->id,
		'name' => $department->name,
		'base_name' => $baseName,
		'url' => $department->url,
		'building_id' => $building->id
	]);

	return $department->id;
}

function storeDepartments($db, $departments, $building) {
	if (!$departments) {
		return;
	}

	echo "Storing " . count($departments) . " departments...\n";
	$ids = [];
	for ($i = 0; $i < count($departments); $i++) {
		$department = $departments[$i];
		$id = storeDepartment($db, $department, $building);
		if ($id) {
			array_push($ids, $id);
		}
	}

	return $ids;
}