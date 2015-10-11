<?php


class PostPHP extends Post
{
	public function PostPHP($filename) {
		parent::__construct($filename);
	}

	public function outputContent ( $req ) {
		require(Post::$POST_PATH.DIRECTORY_SEPARATOR.$this->filename);
	}

	// Returns an array of comma delimited tags contained in the
	// <div id="tags"></div> element
	//
	public function getTags() {
		$contents = file_get_contents(Post::$POST_PATH.DIRECTORY_SEPARATOR.$this->filename);
		$startTag = "<div class=\"tags\">";
		$indexOfTagElem = strpos($contents,$startTag) + strlen($startTag);
		if ( $indexOfTagElem === false ) {
			return array();
		}
		$indexOfEndElem = strpos($contents,"</div>", $indexOfTagElem );
		if ( $indexOfEndElem === false ) {
			return array();
		}
		$tags = substr($contents, $indexOfTagElem, $indexOfEndElem - $indexOfTagElem);
		return explode(",", $tags);
	}
}
