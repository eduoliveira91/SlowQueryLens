<!--======================================================================================================
Autor: Eduardo de Oliveira
Data Inicial: 15/10/2024
Descrição: Sistema para mineração de slow_logs do MYSQL desenvolvido em servidor local XAMPP.
=======================================================================================================-->
<?php

require_once "controllers/template.controller.php";


require_once "models/connection.php";


$template = new ControllerTemplate();
$template -> ctrlTemplate();
