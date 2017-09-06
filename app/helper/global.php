<?php

if (!function_exists('alert')) {
	function alert($data, $die = false) {
		echo '<pre>';
		print_r($data);
		echo '</pre>';
		if ($die) die();
	}
}

if (!function_exists('dd')) {
	function dd($data) {
		echo '<pre>';
		print_r($data);
		echo '</pre>';
		exit();
	}
}
