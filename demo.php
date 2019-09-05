<?php
require_once __DIR__ .'/src/HbaseClient.php';

// $sql= 'insert into ThridPlatform(pay_time,APP_ID,MCH_ID,IMEI,order_sn_no,order_sn_sh,user_tag,pay_type,app_type,pay_status,bank,money_type,total_amount,mach_total_amount,coupon_amount,refund_number_orderno,refund_number_sh,refund_amount,coupon_refund_amount,refund_type,refund_status,goods_name,service_charge,rate)values("2018-10-22 00:16:56","","","","20181022220014075510051173","1165640138859051","*nwt)","交易","0","SUCCESS","","","35.00","35.00","0.00","",""," 35.00","0.00","","","ORACLE触发器视频课程","-0.19","")';
$sql='select * from stuents' ;

$result = HbaseClient::getMethod(['HbaseParseModel','QuerySinTable'],$sql,'cf1');//需要传column family

var_dump($result);