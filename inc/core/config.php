<?php
/**
 * Copyright (c) 2019. Filip Skerik
 * https://filipskerik.eu
 */

    //Database - config - PRODUKČNÍ WEB
    define('DB_HOST', "localhost");
    define('DB_NAME', "inter");
    define('DB_USER', "skerikme");
    define('DB_PASSWORD', "Skerik123");


//define('MAIN_MAIL','hej@filipskerik.eu');

//Page settings

//Time Zone
date_default_timezone_set("Europe/Prague");

//MySQL - připojení
include "inc/core/db.php";

//pretty url core
//$URL = explode("/", $_SERVER['QUERY_STRING']);