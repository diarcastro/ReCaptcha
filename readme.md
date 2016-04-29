#Recaptcha#
<?php
require_once __DIR__.'/libraries/autoload.php';

use diarcastro\utils\ReCaptcha;

$reCaptcha=new ReCaptcha('6LdnZwkTAAAAAGCZ2D-BEx1qtNO-D3wR9OV1PwiX','6LdnZwkTAAAAAO7mU9ZuV-otXYqH34JnUWngkR6D');
/*
 *  Created by Diego Castro <diego.castro@knowbi.com>
 */

if(isset($_POST['action'])){
  if($reCaptcha->isValid()){
    die('Valid Captcha!');
  }else{
    die('Invalid Captcha <a href="'.$_SERVER['PHP_SELF'].'">Try Again!</a>');
  }
}

##see index.php