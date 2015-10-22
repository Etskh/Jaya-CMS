<?php


$headerLinks = array(
	"posts",
	//"code",
	//"about",
	//"tags",
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

		<?php /*
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

		*/ ?>

		<div id="content">
			{{view:"posts.posts"}}
		</div>

		<div id="projects-tab" class="tab hidden-tab">
			<div class="post">
				<h1>Projects</h1>
				<div class="post">
					<h2>Work Project 1</h2>
					<p>
						This is a small blurb about it.
					</p>
				</div>
				<h2>Red On Black</h2>
				<h2>Work Project 2</h2>
				<h2>Jaya-CMS</h2>
				<h2>Work Project 3</h2>
			</div>
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
