<?php


$headerLinks = array(
	"updates",
	//"projects",
	//"about",
	//"tags",
);

?><!DOCTYPE html>
<html>
	<head>
		<title>{{config.title}}</title>
		<meta charset="utf8"/>
		<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no"/>
		<meta name="description" content="James Loucks - Fullstack LAMP developer">
		<meta name="keywords" content="James Loucks Developer LAMP fullstack generalist">

		<link rel="icon" href="modules/james/skull2.ico" />
		<link rel="shortcut icon" href="modules/james/skull2.png" />
		<link rel="apple-touch-icon" href="modules/james/skull2.png" />

		{{config.stylesheets}}
	</head>
	<body>
		<div id="header">
			<a href="#" id="brand">
				<span>{{config.title}}</span>
			</a>
			<?php
			foreach( $headerLinks as $link ) {
				?><a class="link" id="header-<?=$link?>" href="#<?=$link?>"><?=$link?></a><?php
			}
			?>
		</div>

		<div id="content">
			<div id="updates-tab tab hidden-tab">
				{{view:"posts.posts"}}
			</div>
		</div>

		<div id="footer">
			<p>
				This site is created with <a href="https://www.github.com/etskh/jaya-cms" target="_blank">Jaya-CMS</a> (which I am the author)
			</p>
			<p>
				{{config.source}} in {{config.loadtime}} seconds
			</p>
		</div>

		<div id="scripts">
			{{config.scripts}}
		</div>
	</body>
</html>
