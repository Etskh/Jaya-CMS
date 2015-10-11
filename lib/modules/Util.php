<?php


class Util
{
	public static function GetDirContents( $dir ){
		$results = array()
	    $files = scandir($dir);

	    foreach($files as $value){
	        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
	        if(!is_dir($path)) {
	            $results[] = $path;
	        } else if(is_dir($path) && $value != "." && $value != "..") {
	            getDirContents($path, $results);
	            $results[] = $path;
	        }
	    }

	    return $results;
	}
}