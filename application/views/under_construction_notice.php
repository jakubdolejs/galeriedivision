<!DOCTYPE html>
<html>
<head>
<title><?php echo $title; ?></title>
<style type="text/css">
body {
	background-color: #eee;
}
div.notice {
	background-color: #fff;
	width: 400px;
	height: 300px;
	margin: auto;
	position: absolute;
	top: 0px;
	left: 0px;
	right: 0px;
	bottom: 0px;
	border-radius: 12px;
	box-shadow: 0px 5px 20px rgba(0,0,0,0.3);
}
div.content {
	position: absolute;
	left: 30px;
	top: 30px;
	right: 30px;
	bottom: 30px;
	font-family: Helvetica, Arial, sans-serif;
}
</style>
</head>
<body>
<div class="notice"><div class="content">
<?php
echo $text;
?>
</div></div>
</body>
</html>