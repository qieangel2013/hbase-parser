<?php
/**
 * Thrift服务基类
 * @author: qieangel2013 2018/10/26 
 * 
 */
namespace Cto\Edu\Pay\Base;

define('THRIFTPATH',dirname(dirname(__DIR__)).'/thriftHbase');
require_once( THRIFTPATH.'/lib/Thrift/ClassLoader/ThriftClassLoader.php' );
require_once 'PayBase.php';
require_once THRIFTPATH .'/lib/Parse/Parser.php';

use Thrift\ClassLoader\ThriftClassLoader;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;
use Hbase\HbaseClient;
use Hbase\Mutation;
use Hbase\ColumnDescriptor;
//use Cto\Logger\Logger\PayCenterLogger;

class ThriftService extends HbaseService {

    private static $client;

    /*
     *初始化
     * @param 
     * @author: qieangel2013 2018/10/26
     */

    public static function __init()
    {
        $loader = new ThriftClassLoader();
        $loader->registerNamespace('Thrift',THRIFTPATH.'/lib/');
        $loader->registerDefinition('shared',THRIFTPATH);
        $loader->registerDefinition('tutorial',THRIFTPATH);
        $loader->register();
        require_once(THRIFTPATH.'/Hbase/Hbase.php');
        require_once(THRIFTPATH.'/Hbase/Types.php');
        $socket = new TSocket( HBASE_HOST, HBASE_PORT );
        $socket->setSendTimeout( 10000 ); //读超时配置
        $socket->setRecvTimeout( 20000 ); //写超时配置
        $transport = new TBufferedTransport( $socket );
        $protocol = new TBinaryProtocol( $transport );
        self::$client = new \HBase\HbaseClient( $protocol );
        $transport->open();
    }

    /**
     * 获取Hbase数据表
     * 
     * @param 
     * @author: qieangel2013 2018/10/26
     * 
     */
    public static function getTables(){ 
        self::__init();
        return self::$client->getTableNames();
    }

    /**
     * 操作Hbase数据表
     * 
     * @param 
     * @author: qieangel2013 2018/10/26
     * 
     */
    public static function Query($sql,$attributes=array(),$page=0,$offset=1,$column='51cto'){ 
        $SqlData=\Parser::parse($sql);
        self::__init();
        foreach ($SqlData as $k => $v) {
            switch ($k) {
                case 'table':
                    $table=$v;
                    break;
                case 'insert':
                    $record =[];
                    $columns[]=new ColumnDescriptor(array(  
                        'name' => $column.":" 
                    ));
                    self::$client->createTable($table, $columns);
                    foreach ($v as $kk => $vv) {
                       $record[]=new Mutation(array(
                            'column'=>$column.':'.$kk,
                            'value'=>$vv
                        ));
                       if($kk=='order_sn_sh') $rowkey = $vv;
                    }
                    self::$client->mutateRow($SqlData['table'],$rowkey,$record,$attributes);
                    $result=$rowkey;
                    break;
                case 'select':
                    $columns=[
                        'column' =>$column
                    ];
                    $scan= self::$client->scannerOpen($SqlData['table'],$page,$columns,$attributes);
                    $result_tmp = self::$client->scannerGet($scan);
                    foreach ($result_tmp as $ky => $va) {
                        if(count($va->columns)>0){
                            foreach ($va->columns as $m => $n) {
                                $tmp_arr=explode(':',$m);
                                $result[$ky][$tmp_arr[1]]=$n->value;
                            }
                        }
                    }
                    break;
                
            }
        }
        return $result;
    }

    /**
     * 创建Hbase数据表
     * 
     * @param 
     * @author: qieangel2013 2018/10/26
     * 
     */
    public static function CreateTable($table,$fields,$attributes=array(),$column='BillSnap'){ 
        self::__init();
        $columns[]=new ColumnDescriptor(array(  
                'name' => $table 
            ));
        self::$client->createTable($database, $columns);
        $fieldArr=array_keys($fields);
        $record =[];
        foreach ($fields as $k=>$v) { 
            $record[]=new Mutation(array(
                'column'=>$table.':'.$k,
                'value'=>$v
                ));
        } 
        return self::$client->mutateRow($database,$fields['order_sn_sh'],$record,$attributes);
    }

    /**
     * 获取数据
     * 
     * @param 
     * @author: qieangel2013 2018/10/26
     * 
     */
    public static function getTableField($table,$row,$attributes=array()){ 
        self::__init();
        return self::$client->getRow($table,$row,$attributes);;
    }

    /**
     * 删除Hbase数据表
     * 
     * @param 
     * @author: qieangel2013 2018/10/26
     * 
     */
    public static function delTable($table){ 
        self::__init();
        self::$client->disableTable($table);
        return self::$client->deleteTable($table);
    }

   
}

