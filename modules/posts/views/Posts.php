<?php

$posts = Post::GetAll();
$tags = Post::GetTagCloud($posts);


$headerLinks = array(
	"posts",
	"code",
	"about",
	"tags",
);

?><!DOCTYPE html>
<html>
	<head>
		<title>{{config.title}}</title>
		{{config.stylesheets}}
	</head>
	<body>
		<div id="header">
			<a href="#" id="brand">{{config.title}}</a>
			<?php
			foreach( $headerLinks as $link ) {
				?><a class="link" id="header-<?=$link?>" href="#<?=$link?>"><?=$link?></a><?php
			}
			?>
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
			<code>{{config.source}} in {{config.loadtime}} seconds</code>
			<p>{{config.errors}}</p>
		</div>
	</body>
</html>
