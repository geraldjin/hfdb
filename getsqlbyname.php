<?php
include_once "db_para.php";
include_once "./ezSQL/shared/ez_sql_core.php";
include_once "./ezSQL/pdo/ez_sql_pdo.php";

$v=$_POST["val"];
$db=new ezSQL_pdo($db_type.":host=".$db_host.";dbname=".$db_name,$db_user,$db_password);
$row=$db->get_row("select * from Tbl_SQL where SQLID=".$v);
echo $row->StringSQL;
?>