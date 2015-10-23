<?php


$headerLinks = array(
	"updates",
	"projects",
	//"about",
	//"tags",
);

?><!DOCTYPE html>
<html>
	<head>
		<title>{{config.title}}</title>
		<meta charset="utf8"/>
		<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no"/>
		<meta name="description" content="">
		<meta name="keywords" content="James Loucks">

		<link rel="icon" href="modules/james/skull.ico" />
		<link rel="shortcut icon" href="modules/james/skull.png" />
		<link rel="apple-touch-icon" href="modules/james/skull.png" />

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

		<div id="content">
			<div id="updates-tab tab hidden-tab">
				{{view:"posts.posts"}}
			</div>
		</div>

		<div id="scripts">
			{{config.scripts}}
		</div>
	</body>
</html>
