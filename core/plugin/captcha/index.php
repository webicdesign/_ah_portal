<?php
$resultStr = mt_rand ( rand(0,10000) , rand(10000,100000) );
$NewImage  = imagecreatefromjpeg("inc/img/captcha.jpg");
$LineColor = imagecolorallocate($NewImage,233,239,239);
$TextColor = imagecolorallocate($NewImage,255,255,255);
$f_0       = rand(0, 50);
$f_l       = rand(5, 50);
imageline($NewImage,$f_0,1,$f_l,40,$LineColor);
imageline($NewImage,1,$f_0+50,60,0,$LineColor);
imageline($NewImage,$f_l,$f_0+30,$f_l+20,10,$LineColor);
imagestring($NewImage, 5, 17, 5, $resultStr, $TextColor);
$_SESSION['_ah_capcha'] = NULL;
unset($_SESSION['_ah_capcha']);
$_SESSION['_ah_capcha'] = $resultStr;
return header("Content-type: image/jpeg").imagejpeg($NewImage);