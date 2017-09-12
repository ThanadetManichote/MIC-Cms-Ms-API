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

if (!function_exists('ddm')) {
	function ddm($data) {
		if(count($data)>1){
			foreach ($data as $r) {
				$response[] = $r->toArray();
			}
		}else{
			$response = $data->toArray();
		}
		echo '<pre>';
		print_r($response);
		echo '</pre>';
		exit();
	}
}
