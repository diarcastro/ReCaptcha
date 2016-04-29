<?php

namespace diarcastro\utils;

/*
 *  Created by Diego Castro <ing.diegocastro@gmail.com>
 */

/**
 * Create and validate the captcha
 *
 * @author Diego Castro <ing.diegocastro@gmail.com>
 * @copyright (c) 2016, Diego Castro
 * @version 1.0
 */
class ReCaptcha{

  const SITE_KEY='6LdSPgETAAAAAJjG9E3tGIcFs_r8YX3gOrjgHjid';
  const SECRET_KEY='6LdnZwkTAAAAAGCZ2D-BEx1qtNO-D3wR9OV1PwiX';

  /**
   * @var string Nombre de el parámetro enviado por post
   */
  const PARAM_NAME='g-recaptcha-response';

  private static $_signupUrl="https://www.google.com/recaptcha/admin";
  private static $_siteVerifyUrl="https://www.google.com/recaptcha/api/siteverify?";
  private $_secret='';
  private $_sitekey='';
  private static $_version="php_1.0";

  /**
   * Constructor.
   *
   * @param string $secret shared secret between site and ReCAPTCHA server.
   * @param string $siteKey Site key
   */
  function __construct($secret=null,$siteKey=null){
    if($secret === null){
      $secret=self::SECRET_KEY;
    }
    if($siteKey === null){
      $siteKey=self::SITE_KEY;
    }
    if($secret == null || $secret == ""){
      die("To use reCAPTCHA you must get an API key from <a href='"
        .self::$_signupUrl."'>".self::$_signupUrl."</a>");
    }
    $this->_secret=$secret;
    $this->_sitekey=$siteKey;
  }

  /**
   * Encodes the given data into a query string format.
   *
   * @param array $data array of string elements to be encoded.
   *
   * @return string - encoded request.
   */
  private function _encodeQS($data){
    $req="";
    foreach($data as $key=> $value){
      $req .= $key.'='.urlencode(stripslashes($value)).'&';
    }
    // Cut the last '&'
    $req=substr($req,0,strlen($req) - 1);
    return $req;
  }

  /**
   * Submits an HTTP GET to a reCAPTCHA server.
   *
   * @param string $path url path to recaptcha server.
   * @param array  $data array of parameters to be sent.
   *
   * @return array response
   */
  private function _submitHTTPGet($path,$data){
    $req=$this->_encodeQS($data);
    $response=file_get_contents($path.$req);
    return $response;
  }
  /**
   * The current captcha is valid?
   * @return boolean
   */
  public function isValid(){
    $success=$this->verifyResponse();
    return $success->success;
  }
  /**
   * Calls the reCAPTCHA siteverify API to verify whether the user passes
   * CAPTCHA test.
   *
   * @param string $remoteIp   IP address of end user.
   * @param string $response   response string from recaptcha verification.
   *
   * @return ReCaptchaResponse
   */
  public function verifyResponse($remoteIp=null,$response=null){
    if($remoteIp == null){
      $remoteIp=$_SERVER['REMOTE_ADDR'];
    }
    if($response == null){
      $response=$_POST[self::PARAM_NAME];
    }
    // Discard empty solution submissions
    if($response == null || strlen($response) == 0){
      $recaptchaResponse=new ReCaptchaResponse();
      $recaptchaResponse->success=false;
      $recaptchaResponse->errorCodes='missing-input';
      return $recaptchaResponse;
    }
    $getResponse=$this->_submitHttpGet(
      self::$_siteVerifyUrl,array(
      'secret'=>$this->_secret,
      'remoteip'=>$remoteIp,
      'v'=>self::$_version,
      'response'=>$response
      )
    );
    $answers=json_decode($getResponse,true);
    $recaptchaResponse=new ReCaptchaResponse();
    if(trim($answers ['success']) == true){
      $recaptchaResponse->success=true;
    }else{
      $recaptchaResponse->success=false;
      $recaptchaResponse->errorCodes=$answers [error - codes];
    }
    return $recaptchaResponse;
  }

  /**
   * Load and draw the widget
   * @param array $options ReCaptcha widget options defaults:
   * [
   * 'class'=>'g-recaptcha',
    'theme'=>'light',
    'type'=>'image',
    'size'=>'normal',
    'tabindex'=>0,
    'callback'=>false,
    'expired-callback'=>false,
    'explicit'=>false,
    'id'=>random
    'unloadCallback'=>random
   * ]
   */
  public function render($options=[]){
    $validReCaptchaOptions=[
      'class'=>'g-recaptcha',
      'data-sitekey'=>$this->_sitekey,
      'data-theme'=>'light',
      'data-type'=>'image',
      'data-size'=>'normal',
      'data-tabindex'=>0,
      'data-callback'=>false,
      'data-expired-callback'=>false,
    ];
    $validOptions=[
      'explicit',
      'id',
      'unloadCallback',
      'language',
    ];

    if(count($options)){
      $_options=[];
      foreach($options as $k=> $v){
        if(in_array($k,$validOptions)){
          $_options[$k]=$v;
        }else{
          $_options[(strpos($k,'data-') === false)?'data-'.$k:$k]=$v;
        }
      }
      $options=$_options;
    }
    
    
    $options['class']='g-recaptcha '.(string)$options['data-class'];
    unset($options['data-class']);
    
    $options=array_merge([
      'id'=>'recaptcha_'.time(),
      'unloadCallback'=>false,
      'language'=>'es-419',
      'explicit'=>false,
      ],$validReCaptchaOptions,$options);
    $html='';
    $html.='<div id="'.$options['id'].'"';
    $_paramsToJS=[];
    foreach($validReCaptchaOptions as $k=> $v){
      $value=$options[$k];
      if($value != false){
        $html.=PHP_EOL.$k.'="'.$value.'"';
        $_paramsToJS[str_replace('data-','',$k)]=$value;
      }
    }
    $html.='></div>'.PHP_EOL;

    if($options['explicit'] && !$options['unloadCallback']){
      if(!$options['unloadCallback']){
        $options['unloadCallback']=$options['id'].'_callback';
      }
      $params=json_encode($_paramsToJS);
      $html.=<<<JS
        <script type="text/javascript">
          var {$options['unloadCallback']}=function(){
            grecaptcha.render('{$options['id']}',$params);
          }
        </script>
        
JS;
    }

    $html.='<script src="//www.google.com/recaptcha/api.js?'.($options['unloadCallback']?'onload='.$options['unloadCallback']:'').($options['explicit']?'&render=explicit':'').($options['language']?'&hl='.$options['language']:'').'" async defer></script>';
    echo $html;
  }

}

/**
 * A ReCaptchaResponse is returned from checkAnswer().
 */
class ReCaptchaResponse{

  public $success;
  public $errorCodes;

}
