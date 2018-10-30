<?php
//PHP运行日志
define('RUNTIME_LOG_PATH','/data/site_log/pay/runtime/');
define('MONITOR_FILE',__DIR__.'/runtime/runtimr.log');
define('RUNTIME_DEBUG',true);
define('RUNTIME_DEBUG_LEVEL',1);//默认1(1是所有日志都打印)，2(只打印WARNING以上的错误)，3(只打印ERROR以上的错误)
//配置Hbase连接
define('HBASE_HOST', '192.168.234.129');
define('HBASE_PORT', 9090);