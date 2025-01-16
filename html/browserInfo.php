<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Get browser information.</title>
</head>
<?php
include_once('browser_detection.php');
echo "_____________________________________________" . '<br>';
echo "Browser ver.: " . browser_detection( 'number' ) .'<br>'. 
"Browser name: " . browser_detection( 'browser' ) .'<br>'.  
"OS name: " . browser_detection( 'os' ) .'<br>'.  
"OS type: " . browser_detection( 'os_number' ).'<br>'.'<br>'.'<br>';
echo "Browser: " .'<br>';
print_r(browser_detection( 'full' ));
?>
<body>
</body>
</html>
