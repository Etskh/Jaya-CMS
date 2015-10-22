<?php

$posts = Post::GetAll();

if( count($posts) > 0 ) {
	foreach( $posts as $post ) {
		$post->output( $request );
	}
}
else {
	echo "No posts to show";
}
