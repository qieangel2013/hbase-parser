# hbase-parser
php的操作类库，通过写sql来来查询Hbase
### composer使用
    {
        "require": {
            "qieangel2013/hbase-parser": "dev-master"
        }
    }
    composer install
    require __DIR__.'/vendor/autoload.php';
    // $sql= 'insert into ThridPlatform(pay_time,APP_ID,MCH_ID,IMEI,order_sn_no,order_sn_sh,user_tag,pay_type,app_type,pay_status,bank,money_type,total_amount,mach_total_amount,coupon_amount,refund_number_orderno,refund_number_sh,refund_amount,coupon_refund_amount,refund_type,refund_status,goods_name,service_charge,rate)values("2018-10-22 00:16:56","","","","20181022220014075510051173","1165640138859051","*nwt)","交易","0","SUCCESS","","","35.00","35.00","0.00","",""," 35.00","0.00","","","ORACLE触发器视频课程","-0.19","")';
    $sql='select * from students' ;
    $result = HbaseClient::getMethod(['HbaseParseModel','QuerySinTable'],$sql);//统一方法调用
    var_dump($result);//打印结果
### 普通调用
	require_once __DIR__ .'/src/HbaseClient.php';
	// $sql= 'insert into ThridPlatform(pay_time,APP_ID,MCH_ID,IMEI,order_sn_no,order_sn_sh,user_tag,pay_type,app_type,pay_status,bank,money_type,total_amount,mach_total_amount,coupon_amount,refund_number_orderno,refund_number_sh,refund_amount,coupon_refund_amount,refund_type,refund_status,goods_name,service_charge,rate)values("2018-10-22 00:16:56","","","","20181022220014075510051173","1165640138859051","*nwt)","交易","0","SUCCESS","","","35.00","35.00","0.00","",""," 35.00","0.00","","","ORACLE触发器视频课程","-0.19","")';
    $sql='select * from students' ;
    $result = HbaseClient::getMethod(['HbaseParseModel','QuerySinTable'],$sql);//统一方法调用
    var_dump($result);//打印结果
### 目前支持的sql函数
    *  SQL Insert
    *  SQL Select
### TODO
    *  SQL Delete
    *  SQL Update
    *  SQL Where
    *  SQL Order By
    *  SQL Group By
    *  SQL AND 
    *  SQL OR (多重or如:((a=1 and b=2) or (c=3 and d=4)) and e=5)
    *  SQL Like
    *  SQL Not Like
    *  SQL Is NULL
    *  SQL Is Not NULL
    *  SQL COUNT distinct
    *  SQL In
    *  SQL Not In
    *  SQL =
    *  SQL !=
    *  SQL <>
    *  SQL avg()
    *  SQL count()
    *  SQL max()
    *  SQL min()
    *  SQL sum()
    *  SQL Between
    *  SQL Aliases
    *  SQL concat_ws
    *  SQL DATE_FORMATE
    *  SQL Having
### 交流使用
    qq群：578276199
### 项目地址
    github：https://github.com/qieangel2013/HbaseParser
    oschina：https://gitee.com/qieangel2013/HbaseParser
### 如果你对我的辛勤劳动给予肯定，请给我捐赠，你的捐赠是我最大的动力
![](https://github.com/qieangel2013/zys/blob/master/public/images/pw.jpg)
![](https://github.com/qieangel2013/zys/blob/master/public/images/pay.png)
[项目捐赠列表](https://github.com/qieangel2013/zys/wiki/%E9%A1%B9%E7%9B%AE%E6%8D%90%E8%B5%A0)
