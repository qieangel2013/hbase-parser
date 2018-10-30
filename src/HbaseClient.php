<?php
/**
 * 服务处理类
 * @author: qieangel2013 2018/10/12 
 * 
 */
//use Cto\Logger\Logger\PayCenterLogger;

class HbaseClient {

    /**
     * 执行相应的方法
     * @param 
     * @author: qieangel2013 2018/10/12
     * 
     */
    public static function getMethod(){ 
        spl_autoload_register('self::__AutoLoad');
        list($class,$method)=current(func_get_args()); 
        $classor = new $class;
        return call_user_func_array([$class,$method],array_slice(func_get_args(),1,count(func_get_args())));
    }

    /**
     * 
     * @param 
     * @author: qieangel2013 2018/10/12
     * 
     */
    public static function __callStatic($method, $arguments)
    {
        throw new \Exception($method.' method is not exit! argument:'.json_encode($arguments,true));
    }

    /**
     * 
     * @param 自动加载
     * @author: qieangel2013 2018/10/12
     * 
     */
    public static function __AutoLoad($class)
    {
        $file = __DIR__.'/model/'. $class . '.php';
        $file = str_replace('\\','/',$file);
        if (file_exists($file)) {
            require_once $file;
        }
        require_once __DIR__.'/config.php';
        require_once __DIR__.'/handle/error.php';
    }


}

