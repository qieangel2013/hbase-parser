<?php
/*
|---------------------------------------------------------------
|  Copyright (c) 2018
|---------------------------------------------------------------
| 作者：qieangel2013
| 联系：qieangel2013@gmail.com
| 版本：V1.0
| 日期：2018/3/13
|---------------------------------------------------------------
*/
require_once dirname(__FILE__) . '/positions/PositionCalculator.php';
require_once dirname(__FILE__) . '/processors/DefaultProcessor.php';
class Parser {
    public static $parsed;
    private static $table;
    private static $Builderarr;
    public $result;
    public $explain;
    /**
     * Constructor. It simply calls the parse() function. 
     * Use the public variable $parsed to get the output.
     * 
     * @param String  $sql           The SQL statement.
     * @param boolean $calcPositions True, if the output should contain [position], false otherwise.
     */
    public function __construct($sql = false,$calcPositions = false,array $options = array()) {
        if ($sql) {
            $this->parse($sql, $calcPositions);
        }
    }

    /**
     * 
     * @param String  $sql           The SQL statement.
     * @param boolean $calcPositions True, if the output should contain [position], false otherwise.
     * 
     * @return array An associative array with all meta information about the SQL statement.
     */
    
    public function parsesql($sql, $calcPositions = false) {
        $processor = new DefaultProcessor();
        $queries = $processor->process($sql);
        if ($calcPositions) {
            $calculator = new PositionCalculator();
            $queries = $calculator->setPositionsWithinSQL($sql, $queries);
        }
        $this->parsed = $queries;
        return $this->parsed;
    }

    public static function parse($sql, $calcPositions = false) {
        $processor = new DefaultProcessor();
        $queries = $processor->process($sql);
        if ($calcPositions) {
            $calculator = new PositionCalculator();
            $queries = $calculator->setPositionsWithinSQL($sql, $queries);
        }
        self::$parsed = $queries;
        return self::EsBuilder();
        //return self::$parsed;
    }

    private static function EsBuilder(){
        //select
        if(isset(self::$parsed['SELECT']) && !empty(self::$parsed['SELECT'])){
            self::select(self::$parsed['SELECT']);
        }
        //table
        if(isset(self::$parsed['FROM']) && !empty(self::$parsed['FROM'])){
            self::table(self::$parsed['FROM']);
        }

         //insert
        if(isset(self::$parsed['INSERT']) && !empty(self::$parsed['INSERT'])){
            self::insert(self::$parsed['INSERT']);
        }

        //update
        if(isset(self::$parsed['UPDATE']) && !empty(self::$parsed['UPDATE'])){
            self::update(self::$parsed['UPDATE']);
        }
        //set
        if(isset(self::$parsed['SET']) && !empty(self::$parsed['SET'])){
            self::updateset(self::$parsed['SET']);
        }
        //delete
        if(isset(self::$parsed['DELETE']) && !empty(self::$parsed['DELETE'])){
            self::delete(self::$parsed['DELETE']);
        }
        //limit
        if(isset(self::$parsed['LIMIT']) && !empty(self::$parsed['LIMIT'])){
            self::$limit(self::$parsed['LIMIT']);
            if(isset(self::$parsed['GROUP']) && !empty(self::$parsed['GROUP'])){
                self::$Builderarr['size']=0;
            }else{
                self::$Builderarr['from']=self::$limit['from'] * self::$limit['size'];
                self::$Builderarr['size']=self::$limit['size'];
            }
        }
        //where
        if(isset(self::$parsed['WHERE']) && !empty(self::$parsed['WHERE'])){
            self::where(self::$parsed['WHERE']);
        }
       
        //request
        return self::$Builderarr;
    }

    public function explain(){
        $this->explain=json_encode(self::$Builderarr,true);
        return $this->explain;
    }

    private static function table($arr){
        if(isset(self::$parsed['DELETE']) && !empty(self::$parsed['DELETE'])){
            foreach ($arr as $v) {
                if($v['table']){
                    self::$Builderarr['table']=$v['table'];
                }
            }
        }else{
            foreach ($arr as $v) {
                if($v['table']){
                    self::$Builderarr['table']=$v['table'];
                }
            }
        }
        
    }

    private static function update($arr){
        foreach ($arr as $v) {
            if($v['table']){
                self::$table=$v['table'];
            }
        }
    }

    private static function insert($arr){
        foreach ($arr as $k=>$v) {
            self::$Builderarr['table']=$v['table'];
            if(count($v['columns'])>0){
                self::$Builderarr['insert']=self::resdata($v['columns'],self::$parsed['VALUES'][$k]['data']);
            }
        }
    }

     private static function resdata($data,$value){
        foreach ($data as $v) {
            if($v['base_expr']){
                $fielddata=str_replace('`','',$v['base_expr']);
                $fieldarr[]=$fielddata;
            }
        }
        foreach ($value as $vv) {
            if($vv['base_expr']){
                $fielddata=str_replace("'",'',$vv['base_expr']);
                $fielddata=str_replace('"','',$fielddata);
                $valuearr[]=$fielddata;
            }
        }
        return array_combine($fieldarr,$valuearr);
    }

    private static function delete($arr){
    }

   
    private static function where($arr){
        for($i=0;$i<count($arr);$i++){
            if(!is_numeric($arr[$i]['base_expr'])){
                $lowerstr = strtolower($arr[$i]['base_expr']);
            }else{
                $lowerstr = $arr[$i]['base_expr'];
            }
            switch ($lowerstr) {
                case '=':
                    if(strrpos($arr[$i-1]['base_expr'],".")){
                        $term_tmp_arr=explode(".",$arr[$i-1]['base_expr']);
                        $termk=$term_tmp_arr[1];
                    }else{
                        $termk=$arr[$i-1]['base_expr'];
                    }
                    if(isset(self::$parsed['UPDATE']) && !empty(self::$parsed['UPDATE'])){
                        // $this->url .=$arr[$i+1]['base_expr'] ."/_update?pretty";
                    }else{
                        if(!is_numeric($arr[$i+1]['base_expr'])){
                            $term['term'][$termk.'.keyword']=$arr[$i+1]['base_expr'];
                            self::$Builderarr['query']['bool']['must'][0]['bool']['must'][]=$term;
                        }else{
                            $term['term'][$termk]=$arr[$i+1]['base_expr'];
                            self::$Builderarr['query']['bool']['must'][0]['bool']['must'][]=$term;
                        }
                            unset($term['term']);
                    }
                    break;
                case 'in':
                    if(strrpos($arr[$i-1]['base_expr'],".")){
                        $term_tmp_arr=explode(".",$arr[$i-1]['base_expr']);
                        $termk=$term_tmp_arr[1];
                    }else{
                        $termk=$arr[$i-1]['base_expr'];
                    }
                    if(isset($arr[$i+1]['sub_tree']) && !empty($arr[$i+1]['sub_tree'])){
                        foreach ($arr[$i+1]['sub_tree'] as &$vv) {
                            if(!is_numeric($vv['base_expr'])){
                                $termk .='.keyword';
                            }
                            self::$Builderarr['query']['bool']['filter']['terms'][$termk][]=$vv['base_expr'];
                        }
                    }
                    unset($termk);
                    break;
                case '>':
                    if(strrpos($arr[$i-1]['base_expr'],".")){
                        $term_tmp_arr=explode(".",$arr[$i-1]['base_expr']);
                        $termk=$term_tmp_arr[1];
                    }else{
                        $termk=$arr[$i-1]['base_expr'];
                    }
                    if(isset(self::$Builderarr['query']['bool']['must'][0])){
                        if($this->tmp_str==''){
                            $this->count_tmp++;
                        }else if($this->tmp_str!='' && $this->tmp_str!=$termk){
                            $this->count_tmp++;
                        }
                    }
                    $tmp_da_str=str_replace('"','',$arr[$i+1]['base_expr']);
                    $tmp_da_str=str_replace("'","",$tmp_da_str);
                    $is_date=strtotime($tmp_da_str)?strtotime($tmp_da_str):false;
                    self::$Builderarr['query']['bool']['must'][$this->count_tmp]['range'][$termk]['gt']=$tmp_da_str;
                    if(!isset(self::$Builderarr['query']['bool']['must'][$this->count_tmp]['range'][$termk]['time_zone']) && $is_date){
                        self::$Builderarr['query']['bool']['must'][$this->count_tmp]['range'][$termk]['time_zone']="+08:00";
                    }
                    $this->tmp_str=$termk;
                    break;
                case '>=':
                    if(strrpos($arr[$i-1]['base_expr'],".")){
                        $term_tmp_arr=explode(".",$arr[$i-1]['base_expr']);
                        $termk=$term_tmp_arr[1];
                    }else{
                        $termk=$arr[$i-1]['base_expr'];
                    }
                    if(isset(self::$Builderarr['query']['bool']['must'][0])){
                        if($this->tmp_str==''){
                            $this->count_tmp++;
                        }else if($this->tmp_str!='' && $this->tmp_str!=$termk){
                            $this->count_tmp++;
                        }
                    }
                    $tmp_da_str=str_replace('"','',$arr[$i+1]['base_expr']);
                    $tmp_da_str=str_replace("'","",$tmp_da_str);
                    $is_date=strtotime($tmp_da_str)?strtotime($tmp_da_str):false;
                    self::$Builderarr['query']['bool']['must'][$this->count_tmp]['range'][$termk]['gte']=$tmp_da_str;
                    if(!isset(self::$Builderarr['query']['bool']['must'][$this->count_tmp]['range'][$termk]['time_zone']) && $is_date){
                        self::$Builderarr['query']['bool']['must'][$this->count_tmp]['range'][$termk]['time_zone']="+08:00";
                    }
                    $this->tmp_str=$termk;
                    break;
                case '<':
                    if(strrpos($arr[$i-1]['base_expr'],".")){
                        $term_tmp_arr=explode(".",$arr[$i-1]['base_expr']);
                        $termk=$term_tmp_arr[1];
                    }else{
                        $termk=$arr[$i-1]['base_expr'];
                    }
                    if(isset(self::$Builderarr['query']['bool']['must'][0])){
                        if($this->tmp_str==''){
                            $this->count_tmp++;
                        }else if($this->tmp_str!='' && $this->tmp_str!=$termk){
                            $this->count_tmp++;
                        }
                    }
                    $tmp_da_str=str_replace('"','',$arr[$i+1]['base_expr']);
                    $tmp_da_str=str_replace("'","",$tmp_da_str);
                    $is_date=strtotime($tmp_da_str)?strtotime($tmp_da_str):false;
                    self::$Builderarr['query']['bool']['must'][$this->count_tmp]['range'][$termk]['lt']=$tmp_da_str;
                    if(!isset(self::$Builderarr['query']['bool']['must'][$this->count_tmp]['range'][$termk]['time_zone']) && $is_date){
                        self::$Builderarr['query']['bool']['must'][$this->count_tmp]['range'][$termk]['time_zone']="+08:00";
                    }
                    $this->tmp_str=$termk;
                    break;
                case '<=':
                    if(strrpos($arr[$i-1]['base_expr'],".")){
                        $term_tmp_arr=explode(".",$arr[$i-1]['base_expr']);
                        $termk=$term_tmp_arr[1];
                    }else{
                        $termk=$arr[$i-1]['base_expr'];
                    }
                    if(isset(self::$Builderarr['query']['bool']['must'][0])){
                        if($this->tmp_str==''){
                            $this->count_tmp++;
                        }else if($this->tmp_str!='' && $this->tmp_str!=$termk){
                            $this->count_tmp++;
                        }
                    }
                    $tmp_da_str=str_replace('"','',$arr[$i+1]['base_expr']);
                    $tmp_da_str=str_replace("'","",$tmp_da_str);
                    $is_date=strtotime($tmp_da_str)?strtotime($tmp_da_str):false;
                    self::$Builderarr['query']['bool']['must'][$this->count_tmp]['range'][$termk]['lte']=$tmp_da_str;
                    if(!isset(self::$Builderarr['query']['bool']['must'][$this->count_tmp]['range'][$termk]['time_zone']) && $is_date){
                        self::$Builderarr['query']['bool']['must'][$this->count_tmp]['range'][$termk]['time_zone']="+08:00";
                    }
                    $this->tmp_str=$termk;
                    break;
                case 'like':
                    if(strrpos($arr[$i-1]['base_expr'],".")){
                        $term_tmp_arr=explode(".",$arr[$i-1]['base_expr']);
                        $termk=$term_tmp_arr[1];
                    }else{
                        $termk=$arr[$i-1]['base_expr'];
                    }
                    $tmp_la_str=str_replace('"','',$arr[$i+1]['base_expr']);
                    $tmp_la_str=str_replace("'","",$tmp_la_str);
                    if(isset(self::$Builderarr['query']['bool']['must'][0])){
                        if($this->tmp_str==''){
                            $this->count_tmp++;
                        }else if($this->tmp_str!='' && $this->tmp_str!=$termk){
                            $this->count_tmp++;
                        }
                    }
                    if(!is_numeric($arr[$i+1]['base_expr'])){
                        $term['wildcard'][$termk.'.keyword']=str_replace("%","*",$tmp_la_str);
                        self::$Builderarr['query']['bool']['must'][$this->count_tmp]['bool']['must'][]=$term;
                    }else{
                        $term['wildcard'][$termk]=str_replace("%","*",$tmp_la_str);
                        self::$Builderarr['query']['bool']['must'][$this->count_tmp]['bool']['must'][]=$term;
                    }
                    unset($term['wildcard']);
                    break;
            }
        }
    }

    
    private static function limit($arr){
        if(!$arr['offset']){
            $this->limit['from']=0;
        }else{
            $this->limit['from']=$arr['offset'];
        }
        $this->limit['size']=$arr['rowcount'];
    }

    private function inverted($arr){
        for($i=count($arr)-1;$i>=0;$i--){
            if($i>0){
                $arr[$i-1]['aggs']=$arr[$i];
            }
        }
        if(empty($arr)){
            return array();
        }else{
            return $arr[0];
        }
    }

    private static function select($arr){
        foreach ($arr as &$v) {
            if($v['base_expr']=="*"){
                self::$Builderarr['select']['colums']=$v['base_expr'];
            }
        }
    }

    private static function updateset($arr){
        foreach ($arr as &$v) {
            if($v['sub_tree']){
                $tmp_sub[$v['sub_tree'][0]['base_expr']]=$v['sub_tree'][2]['base_expr'];
                self::$Builderarr['doc']=$tmp_sub;
                unset($tmp_sub);
            }
        }
    }








}
?>