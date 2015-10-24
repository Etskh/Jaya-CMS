<?php


class Util
{

	public static function IsExtern($path) {
		if( substr($path,0,4) == "http" ) {
			return true;
		}
		return false;
	}


	public static function IsTextFile( $filename ) {
		// get mime type ala mimetype extension
		$finfo = finfo_open(FILEINFO_MIME);

		//check to see if the mime-type starts with 'text'
		return substr(finfo_file($finfo, $filename), 0, 4) == 'text';
	}


	public static function GetDirContents( $dir, $results = array()){
	    $files = scandir($dir);

	    foreach($files as $value){
	        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
			// Ignore all hidden files OR external files
			if( $value[0] == "." || $value == "extern") {
				continue;
			}

	        if(!is_dir($path)) {
	            $results[] = $path;
	        } else if(is_dir($path) && $value != "." && $value != "..") {
				$results[] = $path;
	            $results = Util::GetDirContents($path, $results);
	        }
	    }

	    return $results;
	}



	public static function GetLineNumberOfText ( $needle, $haystack ) {
		$haystack = explode("\n", $haystack);
		$line_number = false;
		while (list($key, $line) = each($haystack) and !$line_number) {
			$line_number = (strpos($line, $needle) !== FALSE) ? $key + 1 : $line_number;
		}
		return $line_number;
	}


	public static function GetTODOs() {
		$files = Util::GetDirContents('./');
		$todos = array();

		foreach( $files as $key => $file ) {
			if( ! Util::IsTextFile( $file )) {
				unset($files[$key]);
				continue;
			}
			$buffer = file_get_contents($file);
			if( preg_match_all( "/TODO\:(.+)/", $buffer, $matches ) > 0 ) {
				foreach( $matches[1] as $text ) {
					$todos[] = array(
						"file" => $file,
						"text" => $text,
						"line" => Util::GetLineNumberOfText($text, $buffer),
					);
				}
			}
		}
		return $todos;
	}
}
