<?php
class MyDB {
    private $spojeni;

    function __construct($type,$host,$user,$pass,$name){
        $options=array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        );
        $this->spojeni= @new PDO("$type:host=$host;dbname=$name",$user,$pass,$options);
    }

    function query($query,$param=Array()){
        $navrat=$this->spojeni->prepare($query);
        $navrat->execute($param);
        return $navrat->rowCount();
    }

    function queryOne($query,$param=Array()){
        $navrat=$this->spojeni->prepare($query);
        $navrat->execute($param);
        return $navrat->fetch(PDO::FETCH_ASSOC);
    }

    function queryAll($query,$param=Array()){
        $navrat=$this->spojeni->prepare($query);
        $navrat->execute($param);
        return $navrat->fetchAll(PDO::FETCH_ASSOC);
    }
    function lastID() {
        return $this->spojeni->lastInsertId();
    }
}

$database = new MyDB("mysql",DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
//include "site_settings.php";
