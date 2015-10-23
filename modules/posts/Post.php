<?php


class Post
{
	// Where to find this data
	static public $POST_PATH = "data/posts";

	public $timestamp;
	public $slug;
	public $name;
	public $filename;

	public function Post($filename) {
		$this->filename = $filename;
		$this->slug = substr( $filename,0,strrpos( $filename, ".") );
		$this->name = ucwords(str_replace("-"," ",$this->slug));
		$this->timestamp = filemtime(Post::$POST_PATH.DIRECTORY_SEPARATOR.$this->filename);
	}

	public function outputHeader( $extraClass ) {
		?>
		<div class="post <?=$extraClass?>">
			<a name="<?=$this->slug?>"></a>
			<h1><?=$this->name?></h1>
			<div class="date-modified"><?=date("F Y", $this->timestamp)?></div><?php
	}
	public function outputFooter() {
		?></div><?php
	}

	public function output( $request, $extraClass ) {
		$this->outputHeader($extraClass);
		$this->outputContent($request);
		$this->outputFooter();
	}

	// Returns an array in the form of $tag => $count
	//	arg $postList : an array of posts to iterate over
	static public function GetTagCloud( $postList ) {
		$tagCloud = array();
		foreach( $postList as $post ) {
			$postTags = $post->getTags();
			foreach( $postTags as $tag ) {
				$tagCloud[$tag] = isset($tagCloud[$tag])?$tagCloud[$tag]+1:1;
			}
		}
		return $tagCloud;
	}


	static public function GetAll() {
		$files = scandir(Post::$POST_PATH, SCANDIR_SORT_ASCENDING );
		if( $files == false ) {
			die("Can't read from posts directory");
		}
		$posts = array();
		foreach( $files as $file ) {
			if( $file[0] == '.') {
				continue;
			}
			$extension = substr( $file, strrpos( $file, ".") );
			switch($extension) {
			case ".php":
				$posts[] = new PostPHP($file);
				break;
			case ".md":
				$posts[] = new PostMarkdown($file);
				break;
			default:
				// TODO: Log something here instead of dying
				die("Unknown extension '$extension'");
			}

		}
		usort($posts, "Post::TimestampSorter");

		return $posts;
	}

	static public function TimestampSorter ($postA, $postB) {
		return $postB->timestamp - $postA->timestamp;
	}
}
