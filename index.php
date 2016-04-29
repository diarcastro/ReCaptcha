<?php
/*
 *  Created by Diego Castro <ing.diegocastro@gmail.com>
 */
require_once __DIR__.'/libraries/autoload.php';

use diarcastro\utils\ReCaptcha;

$reCaptcha=new ReCaptcha('6LdnZwkTAAAAAGCZ2D-BEx1qtNO-D3wR9OV1PwiX','6LdnZwkTAAAAAO7mU9ZuV-otXYqH34JnUWngkR6D');

if(isset($_POST['action'])){
  if($reCaptcha->isValid()){
    die('Valid Captcha!');
  }else{
    die('Invalid Captcha <a href="'.$_SERVER['PHP_SELF'].'">Try Again!</a>');
  }
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <title>ReCaptcha Test</title>
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" />
  </head>
  <script type="text/javascript">
    function myCallback(){
      //ReCapcha render here
      alert('myCallback');
    }
  </script>
  <body>
    <div class="container">
      <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <div class="form-group">
          <?=$reCaptcha->render();?>
        </div>
        <div class="form-group">
          <input type="hidden" name="action" value="<?=uniqid();?>" />
          <input type="submit" value="Validate" class="btn btn-info" />
        </div>
      </form>
    </div>
  </body>
</html>