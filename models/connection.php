<?php

class Connection{

    static public function conectar(){

		$link = new PDO("mysql:host=localhost;dbname=slow_logs_db",
			            "root",
			            "root");

		$link->exec("set names utf8");

		return $link;
	}
}
