<?php
error_reporting (0);
class CtoPayHbaseException extends Exception
{
    private $error_type;
    public function __construct($error_type,$message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->error_type=$error_type;
    }
    public function __toString() {
        return (string)printf("%s: %s in %s on line %d\n",$this->error_type, $this->message, $this->file, $this->line);
    }
}
function exitdir_Hbase($dir){
        $dirarr=pathinfo($dir);
        if (!is_dir( $dirarr['dirname'] )) {
            mkdir( $dirarr['dirname'], 0777, true);
        }
        return $dir;
}
function ctopay_error_handler_Hbase ($error_level, $error_message, $file, $line) {
  $levelenable=false;
  if(defined('RUNTIME_DEBUG_LEVEL')){
    switch (RUNTIME_DEBUG_LEVEL) {
      case 1:
        $levelenable=true;
        break;
      case 2:
        if(in_array($error_level,array(E_WARNING,E_USER_WARNING,E_PARSE,E_ERROR,E_USER_ERROR,E_CORE_ERROR,E_CORE_WARNING,E_COMPILE_ERROR,E_COMPILE_WARNING,E_RECOVERABLE_ERROR,E_USER_DEPRECATED,E_STRICT,E_DEPRECATED))){
          $levelenable=true;
        }
        break;
      case 3:
        if(in_array($error_level,array(E_PARSE,E_ERROR,E_USER_ERROR,E_CORE_ERROR,E_COMPILE_ERROR,E_RECOVERABLE_ERROR,E_USER_DEPRECATED,E_STRICT,E_DEPRECATED))){
          $levelenable=true;
        }
        break;
    }
  }
  switch ($error_level) {
      case E_NOTICE:
        $error_type = 'PHP SYSTEM Notice';
        break;
      case E_USER_NOTICE:
        $error_type = 'PHP USER Notice';
        break;
      case E_WARNING:
        $error_type = 'PHP SYSTEM WARNING';
        break;
      case E_USER_WARNING:
        $error_type = 'Warning';
        break;
      case E_PARSE:
        $error_type = 'PHP SYSTEM PARSE';
        break;
      case E_ERROR:
        $error_type = 'PHP SYSTEM Fatal ERROR';
        break;
      case E_USER_ERROR:
        $error_type = 'PHP USER Fatal Error';
        break;
      case E_CORE_ERROR:
        $error_type = 'PHP SYSTEM CORE_ERROR';
        break;
      case E_CORE_WARNING:
        $error_type = 'PHP SYSTEM CORE_WARNING';
        break;
      case E_COMPILE_ERROR:
        $error_type = 'PHP SYSTEM COMPILE_ERROR';
        break;
      case E_COMPILE_WARNING:
        $error_type = 'PHP SYSTEM COMPILE_WARNING';
        break;
      case E_STRICT:
        $error_type = 'PHP SYSTEM STRICT';
        break;
      case E_RECOVERABLE_ERROR:
        $error_type = 'PHP SYSTEM RECOVERABLE_ERROR';
        break;
      case E_USER_DEPRECATED:
        $error_type = 'PHP SYSTEM USER_DEPRECATED';
        break;
      case E_DEPRECATED:
        $error_type = 'PHP SYSTEM DEPRECATED';
        break;
      case E_ALL:
        $error_type = 'PHP SYSTEM E_ALL';
        break;
      default:
        $error_type = 'PHP Unknown ERROR';
        break;
  }
  if(defined('RUNTIME_DEBUG') && RUNTIME_DEBUG===true && $levelenable){
    printf("%s: %s in %s on line %d\n<br/>",$error_type,$error_message, $file, $line);
  }else if(defined('RUNTIME_LOG_PATH') && $levelenable){
    $data=date("[Y-m-d H:i:s]").sprintf(" %s: %s in %s on line %d",$error_type,$error_message, $file, $line);
    file_put_contents(exitdir_Hbase(RUNTIME_LOG_PATH.date('Ymd').'.log'), $data."\r\n", FILE_APPEND);
  }
  
}
 function ctopay_shutdown_handler_Hbase() {
    if($error = error_get_last()) {
       ctopay_error_handler_Hbase($error['type'], $error['message'], $error['file'], $error['line']);
    }
 }
register_shutdown_function('ctopay_shutdown_handler_Hbase');
set_error_handler('ctopay_error_handler_Hbase');