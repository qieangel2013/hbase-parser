<?php
/**
 * 服务基类
 * @author: qieangel2013 2018/10/12 
 * 
 */
namespace Cto\Edu\Pay\Base;

//use Cto\Logger\Logger\PayCenterLogger;

class HbaseService {

    /**
     * 
     * @param 
     * @author: qieangel2013 2018/10/12
     * 
     */
    public static function __callStatic($method, $arguments)
    {
    	//\PayCenterLogger::info($method.' method is not exit! argument:',is_array($arguments)?$arguments:[]);
        throw new \Exception($method.' method is not exit! argument:'.json_encode($arguments,true));
    }

   
}

