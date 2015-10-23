<?php

$posts = Post::GetAll();

if( count($posts) > 0 ) {
	foreach( $posts as $row => $post ) {
		$post->output( $request, $row%2==0?"even":"odd" );
	}
}
else {
	echo "No posts to show";
}
