<?php

/**
	This is base-class for any files from the `/data` folder
*/
class Data
{
	static public function GetAllByModule( $moduleName ) {
		$files = scandir('data/'.$moduleName, SCANDIR_SORT_ASCENDING );
		if( $files == false ) {
			die("Can't read from posts directory");
		}
		$fileList = array();
		foreach( $files as $file ) {
			if( $file[0] == '.') {
				continue;
			}
			$fileList[] = $file;
		}
		usort($fileList, "Data::TimestampSorter");
	}


	public funtion Data ( ) {

	}

	static public function TimestampSorter ($postA, $postB) {
		return $postB->timestamp - $postA->timestamp;
	}
}
