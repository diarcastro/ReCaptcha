<?php

/* 
 *  Created by Diego Castro <ing.diegocastro@gmail.com>
 */

spl_autoload_register(function ($class){
  $base=dirname(dirname(__FILE__)).'/libraries/';
  $class=str_replace('\\','/',$class);
  $path=$class.'.php';
  if(is_readable($base.$path)){
    require_once $base.$path;
  }
});
