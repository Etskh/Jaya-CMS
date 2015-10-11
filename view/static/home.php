<?php

$posts = Post::GetAll();
$tags = Post::GetTagCloud($posts);

?><!DOCTYPE html>
<html>
	<head>
		<title></title>
		{{config.stylesheets}}
	</head>
	<body>
		<div id="header">
			<span id="">Binary.Art</span>
		</div>

		<div id="post-list">
			<ul><?php
			foreach( $posts as $post ) {
				?><li><a href="#<?=$post->slug?>"><?=$post->name?></a></li><?php
			}
			?></ul>
		</div>

		<div id="tag-cloud">
			<?php
			foreach( $tags as $tag => $count ) {
				?><a><?=$tag?>(<?=$count?>)</a><?php
			}
			?>
		</div>

		<div id="content">
			<?php
			if( count($posts) > 0 ) {
				foreach( $posts as $post ) {
					$post->output( $request );
				}
			}
			else {
				echo "No posts to show";
			}
			?>
		</div>

		<div id="scripts">
			{{config.scripts}}
		</div>
		<div id="footer">
			{{config.loadtime}}s from {{config.source}}
		</div>
	</body>
</html>
