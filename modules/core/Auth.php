<?php


function users( $app ) {
	$app->db->loadSchema( "user", array(
		"name"=> "string",
		"password" => "string",
	));
}
