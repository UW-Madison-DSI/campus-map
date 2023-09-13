<?php
/******************************************************************************\
|                                                                              |
|                                    api.php                                   |
|                                                                              |
|******************************************************************************|
|                                                                              |
|        This defines an interface for fetching and storing information        |
|        from the UW Campus Map database.                                      |
|                                                                              |
|        Author(s): Abe Megahed                                                |
|                                                                              |
|        This file is subject to the terms and conditions defined in           |
|        'LICENSE.txt', which is part of this source code distribution.        |
|                                                                              |
|******************************************************************************|
|  Copyright (C) 2022 Data Science Institute, Univeristy of Wisconsin-Madison  |
\******************************************************************************/

include 'buildings.php';
include 'parking.php';

// API attributes
//
$api = 'https://map.wisc.edu/api/v1';
