<?php
/**
 * 入库服务model
 * @author: qieangel2013 2018/10/26 
 * 
 */

require_once dirname(__DIR__).'/service/ThriftBase.php';

//use Cto\Logger\Logger\PayCenterLogger;

class HbaseParseModel extends  \Cto\Edu\Pay\Base\ThriftService{

    /**
     * 获取Hbase数据表
     * 
     * @param 
     * @author: qieangel2013 2018/10/26
     * 
     */
    public static function getAllTables(){ 
        return self::getTables();
    }

    /**
     * 操作Hbase数据表
     * 
     * @param 
     * @author: qieangel2013 2018/10/26
     * 
     */
    public static function QuerySinTable($sql){ 
        return self::Query($sql);
    }

    /**
     * 获取数据
     * 
     * @param 
     * @author: qieangel2013 2018/10/26
     * 
     */
    public static function getField($table,$row,$attributes=array()){ 
        return self::getTableField($table,$row,$attributes);
    }

    /**
     * 删除Hbase数据表
     * 
     * @param 
     * @author: qieangel2013 2018/10/26
     * 
     */
    public static function delSinTable($table){ 
        return self::delTable($table);
    }

   

   
}

