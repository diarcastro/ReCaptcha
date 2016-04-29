#Recaptcha

````
<?php
require_once '/libraries/autoload.php';

use diarcastro\utils\ReCaptcha;

$reCaptcha=new ReCaptcha('YOUR_SECRET_KEY','YOUR_SITE_KEY');
````
##Show the widget
````
$recaptcha->render();
````

##Validate
````
$recaptcha->isValid();
````

##see example in index.php
