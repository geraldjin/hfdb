<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>

<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="./uploadify/jquery.uploadify.min.js"></script>
<link rel="stylesheet" type="text/css" href="./uploadify/uploadify.css" />
<script type="text/javascript" >
$(document).ready(function(){ 
	$('#sqllist').change(function(){
		var v = $('#sqllist').children('option:selected').val();
		var t = $('#sqllist').children('option:selected').text();
		document.getElementById("QueryName").value=t;
		$.post("getsqlbyname.php",{val:v},function(data){
			//$("#StringSQL").text(data);
			document.getElementById("StringSQL").value=data;
			})
		})
	
	$('#btnAdd').click(function(){
		var QueryName=$("#QueryName").val();
		var StringSQL=$("#StringSQL").val();
		$.post("tbl_sql.php",{QueryNameA:QueryName,StringSQLA:StringSQL},function(data){
			$("#info").html(data);
			$("#info").show();
			$("#info").fadeOut(5000);
			$.post("tbl_sql.php",{sqllist:"sqllist"},function(list){
					$('#sqllist').html(list);
					})
			//if(data=="Query successfully added.")
			//{
				//$('#sqllist').append('<option value="' + QueryName + '">' + QueryName + '</option>');
				//$('#sqllist').val(QueryName);
			//}
			})
		})
		
	$('#btnUpdate').click(function(){
		var v = $('#sqllist').children('option:selected').val();
		var QueryName=$("#QueryName").val();
		var StringSQL=$("#StringSQL").val();
		$.post("tbl_sql.php",{SQLIDU:v,QueryNameU:QueryName,StringSQLU:StringSQL},function(data){
			$("#info").html(data);
			$("#info").show();
			$("#info").fadeOut(5000);
			$.post("tbl_sql.php",{sqllist:"sqllist"},function(list){
					$('#sqllist').html(list);
					})
			})
		})
		
	$('#btnDelete').click(function(){
		if(confirm("Sure to delete?"))
		{
			var v = $('#sqllist').children('option:selected').val();
			var QueryName=$("#QueryName").val();
			var StringSQL=$("#StringSQL").val();
			$.post("tbl_sql.php",{SQLIDD:v,QueryNameD:QueryName,StringSQLD:StringSQL},function(data){
			$("#info").html(data);
			$("#info").show();
			$("#info").fadeOut(5000);
			$.post("tbl_sql.php",{sqllist:"sqllist"},function(list){
					$('#sqllist').html(list);
					})
			document.getElementById("QueryName").value="";
			document.getElementById("StringSQL").value="";
			})
		}
	})
		
	$('#btnExe').click(function(){
		var QueryExe=document.getElementById("QueryName").value;
		var StringPara=document.getElementById("sqlpara").value;
		var a = new Object();
		if(StringPara!="")
		{
			a={QueryExe:QueryExe,StringPara:StringPara};
		}
		else
		{
			a={QueryExe:QueryExe};
		}
		$.post("tbl_sql.php",a,function(data){
			//$('#sqlresult').html(data);
			document.getElementById("sqlresult").innerHTML=data;
		})
	})
	
	$(function() {
	$('#import').uploadify({
		'fileObjName' : 'Filedata',
		'uploader' : 'tbl_sql.php',
		'swf'      : './uploadify/uploadify.swf',
		'fileTypeExts' : '*.xls;*.xlsx;*.csv',
		'fileTypeDesc' : 'select excel file',
		'onUploadSuccess' : function(respnose,data) {
			document.getElementById("excel").style.display="";
			//document.getElementById("columns").innerHTML=respnose;
			document.getElementById("columns").innerHTML=data;
			//$('#import').html(data);
			},
		'onUploadError' : function(file, errorCode, errorMsg, errorString) {
            alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
        }
		});
	});
	
	$('#tbllist').change(function(){
		var tbl = $('#tbllist').children('option:selected').val();
		var array = new Array(); //定义数组
		$("#collist option").each(function(){ //遍历全部option
			var val = $(this).val(); //获取option的内容”
			array.push(val); //添加到数组中
		});
		$.post("tbl_sql.php",{Table:tbl,Columns:array},function(data){
			//$("#StringSQL").text(data);
			document.getElementById("Fields").innerHTML=data;
			})
		})
		
	$('#SaveMatch').click(function(){
		var tbl = $('#tbllist').children('option:selected').val();
		var fields = new Array();
		var columns = new Array();
		var types = new Array();
		$("#FieldsMatch tr").each(function(){
			if($(this).find('td').eq(0).text()=="")
			{
				return true;	
			}
			//alert($(this).find('td').eq(3).find('select').val());
			var field=$(this).find('td').eq(0).text();
			var type=$(this).find('td').eq(1).text();
			var column=$(this).find('td').eq(3).find('select').val();
			fields.push(field);
			types.push(type);
			columns.push(column);
		})
		$.post("tbl_sql.php",{MFields:fields,MColumns:columns,MTypes:types,MatchTable:tbl},function(data){
			document.getElementById("MatchResult").innerHTML=data;
		})
	})
	
	$(function() {
	$('#iptTrade').uploadify({
		'fileObjName' : 'Filedata',
		'formData'  : {'Datatype':'TradeRecord'},
		'uploader' : 'excel2db.php',
		'swf'      : './uploadify/uploadify.swf',
		'buttonText' : 'Import TradeRecord',
		'fileTypeExts' : '*.xls;*.xlsx;*.csv',
		'fileTypeDesc' : 'select excel file',
		'onUploadSuccess' : function(respnose,data) {
			alert(data);
			},
		'onUploadError' : function(file, errorCode, errorMsg, errorString) {
            alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
        }
		});
	});
	
	$(function() {
	$('#iptSecurity').uploadify({
		'fileObjName' : 'Filedata',
		'formData'  : {'Datatype':'Security'},
		'uploader' : 'excel2db.php',
		'swf'      : './uploadify/uploadify.swf',
		'buttonText' : 'Import SecurityInfo',
		'fileTypeExts' : '*.xls;*.xlsx;*.csv',
		'fileTypeDesc' : 'select excel file',
		'onUploadSuccess' : function(respnose,data) {
			alert(data);
			},
		'onUploadError' : function(file, errorCode, errorMsg, errorString) {
            alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
        }
		});
	});
	
	$(function() {
	$('#iptPrice').uploadify({
		'fileObjName' : 'Filedata',
		'formData'  : {'Datatype':'SecurityPrice'},
		'uploader' : 'excel2db.php',
		'swf'      : './uploadify/uploadify.swf',
		'buttonText' : 'Import SecurityPrice',
		'fileTypeExts' : '*.xls;*.xlsx;*.csv',
		'fileTypeDesc' : 'select excel file',
		'onUploadSuccess' : function(respnose,data) {
			alert(data);
			},
		'onUploadError' : function(file, errorCode, errorMsg, errorString) {
            alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
        }
		});
	});
	
	$(function() {
	$('#iptFX').uploadify({
		'fileObjName' : 'Filedata',
		'formData'  : {'Datatype':'FX'},
		'uploader' : 'excel2db.php',
		'swf'      : './uploadify/uploadify.swf',
		'buttonText' : 'Import FX',
		'fileTypeExts' : '*.xls;*.xlsx;*.csv',
		'fileTypeDesc' : 'select excel file',
		'onUploadSuccess' : function(respnose,data) {
			alert(data);
			},
		'onUploadError' : function(file, errorCode, errorMsg, errorString) {
            alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
        }
		});
	});
	
	$(function() {
	$('#iptDaily').uploadify({
		'fileObjName' : 'Filedata',
		'formData'  : {'Datatype':'Daily'},
		'uploader' : 'excel2db.php',
		'swf'      : './uploadify/uploadify.swf',
		'buttonText' : 'Import DailyData',
		'fileTypeExts' : '*.xls;*.xlsx;*.csv',
		'fileTypeDesc' : 'select excel file',
		'onUploadSuccess' : function(respnose,data) {
			alert(data);
			},
		'onUploadError' : function(file, errorCode, errorMsg, errorString) {
            alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
        }
		});
	});
	
})


</script>


</head>

<body style="margin: 0 auto; width: 768px;">
<table width="100%">
	<tr>
    	<td width="50%" style="border:2px; border-color:#666; border-style:solid">
        	<table width="100%">
            	<tr>
                	<td><form action="db_config.php" method="post">
                        <input type="submit" name="btn_initdb" value="Initial Database">
                        </form> 
                        
                        <?php
                        include_once "db_init.php";
                        if(!empty($_POST["btn_initdb"]))
                        {
                            initDB();
                        }
                        ?></td>
                </tr>
                <tr>
                	<td>
                    	<p style="margin-bottom:4px">select a speadsheet to match with DB</p>
                        <input type="file" id="import" />
                    </td> 
                </tr>
                <tr>
                	<td>
                    	<p style="margin-bottom:4px">tables in DB</p>
                        <select id="tbllist" name="tbllist" multiple="true"  size="10" >
                        <?php
						include "db_para.php";
						$db=new ezSQL_pdo($db_type.":host=".$db_host.";dbname=".$db_name,$db_user,$db_password);
						$tables = $db->get_results("SHOW TABLES",ARRAY_N);	
						$tableshid = array('tbl_sql','tbl_matches');
						foreach($tables as $table)
						{
							if(!in_array($table[0],$tableshid))
							{
								echo "<option value='".$table[0]."'>".$table[0]."</option>";
							}
						}
						?>
                        </select>
                    </td>
                    <td>
                    	<div id="excel" style="display:none">
                        	<p style="margin-bottom:4px">columns in the excel</p>
                            <div id="columns"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                	<td colspan="2">
                    	<div id="Fields" style="font-size:10px"></div>
                    </td>
                </tr>
                <tr>
                	<td style="padding:0" colspan="2">
                    	<input type="button" id="SaveMatch" value="Save Matches" />
                        <p id="matchinfo" style="font-size:small; margin:4px"></p>
                        <div id="MatchResult"></div>
                    </td>
                </tr>
                <tr>
                	<td style="padding:0" colspan="2">
                   		<input type="file" id="iptSecurity" />
                    	<input type="file" id="iptTrade" />
                        <input type="file" id="iptPrice" />
                        <input type="file" id="iptFX" />
                        <input type="file" id="iptDaily" />
                    </td>
                </tr>
            </table>
        </td>
        <td width="50%" style="border:2px; border-color:#666; border-style:solid"> 
        	<select id="sqllist" name="list" multiple="true"  size="10"   > 
            <?php
			include "db_para.php";
			$db=new ezSQL_pdo($db_type.":host=".$db_host.";dbname=".$db_name,$db_user,$db_password);
			$table = $db->get_results("select * from Tbl_SQL");	
			foreach($table as $query)
			{
				echo "<option value='".$query->SQLID."'>".$query->QueryName."</option>";	
			}
			?>
            </select> 
            <div>
            <input type="text" id="QueryName" name="QueryName"/>
            <textarea id="StringSQL" name="StringSQL" style="width:90%"></textarea>
            </div>
            <div style="padding-top:4px;text-align:right;">   
                <input type="button" id="btnAdd" value="Add" />
                <input type="button" id="btnUpdate" value="Update" />
                <input type="button" id="btnDelete" value="Delete" />
                <p id="info" style="font-size:small; margin-bottom:4px"></p>
            </div>
            <div>
            	<p style="font-size:small; margin-bottom:4px">Input parameters if needed, separate with commas.</p>
            	<input type="text" id="sqlpara" name="sqlpara" style="width:312px"/>
                <input type="button" id="btnExe" value="Execute" />
            </div>    
            <div id="sqlresult" style="font-size:10px"></div>
        </td>
    </tr>
</table>

</body>
</html>