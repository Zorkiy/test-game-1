<!DOCTYPE html>
<html lang="ua">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	<header>
		<?php if (isset($_SESSION['user_id'])) { ?>
		<div>
			<a href="http://game.loc/?route=login/logout">Вийти</a>
		</div>
		<?php } ?>
	</header>
