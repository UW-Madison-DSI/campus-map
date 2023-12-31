<?php

include 'lib/mysql.php';

define('DATABASE_HOST', '127.0.0.1');
define('DATABASE_USERNAME', 'root');
define('DATABASE_PASSWORD', 'root');
define('DATABASE_NAME', 'campus_map');
define('DATABASE_DEBUG', false);

// connect to database
//
$db = mysqli_connect(DATABASE_HOST, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if (!$db) {
	die("Could not connect to datbase.");
}

function getValue($object, $name) {
	return property_exists($object, $name)? $object->name : null;
}

function getId($item) {
	if ($item) {
		return $item->id;
	} else {
		return null;
	}
}

function getIds($items) {
	if ($items) {
		$ids = [];
		for ($i = 0; $i < count($items); $i++) {
			array_push($ids, $items[$i]->id);
		}
		return $ids;
	} else {
		return null;
	}
}

function exists($db, $tableName, $row, $key) {
	$query = "SELECT * from $tableName WHERE $row = '$key'";
	$result = mysqli_query($db, $query);
	if ($result) {
		$row = mysql_fetch_assoc($result);
		if ($row) {
			return true;
		}
	}
	return false;
}

function insert($db, $tableName, $rows) {

	// get row names
	//
	$keys = array_keys($rows);

	// get row values
	//
	$array = [];
	for ($i = 0; $i < count($rows); $i++) {
		$key = $keys[$i];
		$value = $rows[$key];

		// handle nulls
		//
		if ($value == 'NULL') {
			$value = null;

		// handle strings
		//
		} else if (gettype($value) == 'string') {

			// handle quotes in strings
			//
			if (str_contains($value, "'")) {
				$value = str_replace("'", "''", $value);
			}

			// handle backslashes in strings
			//
			if (str_contains($value, "\\")) {
				$value = stripslashes($value);
			}

		// handle arrays
		//
		} else if (is_array($value)) {
			$value = implode(", ", $value);

		// handle booleans
		//
		} else if (gettype($value) == 'bool') {
			$value = intval($value);

		// handle objects
		//
		} else if (is_object($value)) {
			$value = print_r($value, 1);
			echo "Warning: converted object value of " . $key . " to string: " . $value;
		}

		if ($value) {
			array_push($array, "'" . $value . "'");
		} else {
			array_push($array, 'null');
		}
	}

	// format keys, values
	//
	$keys = '`' . implode('`, `', $keys) . '`';
	$values = implode(',', $array);

	// insert into database
	//
	try {
		$query = "INSERT INTO `$tableName` ($keys) VALUES ($values)";
		if (DATABASE_DEBUG) {
			echo "query = " . $query . "\n";
		}
	} catch (\Exception $exception) {
		echo "Error: query = " . $query . "\n";
	}

	$result = mysqli_query($db, $query);
	if (!$result) {
		die("Could not insert data into database." . mysql_error());
	}
}
