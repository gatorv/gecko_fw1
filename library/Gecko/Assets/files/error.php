<!-- Standar error template -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title><?php echo $title; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	</head>
	<body>
	<h1>Oops! There was an internal server error.</h1>

	<br />
	<p>
	<a href="javascript:history.go(-1)">Go back a page</a><br />
	</p>
	<br />
	<p><em><?php echo $error; ?></em></p>
	</body>
</html>
