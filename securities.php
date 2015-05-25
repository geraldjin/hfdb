<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>

</head>

<body>

<div id="linechart" style="height:300px;border:1px solid #ccc;padding:10px;"></div>
<div id="piechart" style="height:300px;border:1px solid #ccc;padding:10px;"></div>
<script src="js/echarts.js"></script>
<?php
include_once "getsql.php";
include_once "echarts.php";

$gs=new GetSQL("show securityprice");
$result=$gs->GetResults();
$option=$gs->GetOption($result,"SecurityName","Date","Price","line");
$ec = new Echarts();
echo $ec->show('linechart', $option); 

$gs=new GetSQL("show position");
$result=$gs->GetResults();
$option=$gs->GetPieOption($result,"SecurityName","Position");
$ec = new Echarts();
echo $ec->show('piechart', $option); 
?>
</body>
</html>