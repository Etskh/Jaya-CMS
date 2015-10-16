<?php



class PostMarkdown extends Post
{
	public function PostPHP($filename) {
		parent::__construct($filename);
	}

	public function outputContent ( $req ) {
		$parsedown = new Parsedown();
		$contents = file_get_contents(Post::$POST_PATH.DIRECTORY_SEPARATOR.$this->filename);
		echo $parsedown->text($contents);
	}

	// Returns an array of comma delimited tags contained in the
	// <div id="tags"></div> element
	//
	public function getTags() {
		return array();
	}
}
