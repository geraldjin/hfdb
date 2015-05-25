<?php
include_once "db_para.php";
include_once "./ezSQL/shared/ez_sql_core.php";
include_once "./ezSQL/pdo/ez_sql_pdo.php";

if($_POST["QueryNameA"] and $_POST["StringSQLA"])
{
	$QueryName=$_POST["QueryNameA"];
	$StringSQL=$_POST["StringSQLA"];
	$db=new ezSQL_pdo($db_type.":host=".$db_host.";dbname=".$db_name,$db_user,$db_password);
	$var = $db->get_var("SELECT count(*) FROM Tbl_SQL where QueryName='".$QueryName."'");
	if($var>0)
	{
		echo "This queryname exists.";
		//die();	
	}
	else
	{
		$db->show_errors();
		$db->query("insert into Tbl_SQL(QueryName,StringSQL) values(\"".$QueryName."\",\"".$StringSQL."\")");
		echo "Query successfully added.";
		//die();
	}	
}
else if($_POST["SQLIDU"] and $_POST["QueryNameU"] and $_POST["StringSQLU"])
{
	$SQLID=$_POST["SQLIDU"];
	$QueryName=$_POST["QueryNameU"];
	$StringSQL=$_POST["StringSQLU"];
	$db=new ezSQL_pdo($db_type.":host=".$db_host.";dbname=".$db_name,$db_user,$db_password);
	$var = $db->get_var("SELECT count(*) FROM Tbl_SQL where SQLID=".$SQLID);
	if($var>0)
	{
		$db->show_errors();
		$db->query("update Tbl_SQL set StringSQL=\"".$StringSQL."\", QueryName=\"".$QueryName."\" where SQLID=".$SQLID);
		echo "Query successfully updated.";
		//die();
	}
	else
	{
		$db->show_errors();
		$db->query("insert into Tbl_SQL(QueryName,StringSQL) values(\"".$QueryName."\",\"".$StringSQL."\")");
		echo "Query dose not exist, new query added.";
		//die();	
	}
}
else if($_POST["SQLIDD"] and $_POST["QueryNameD"] and $_POST["StringSQLD"])
{
	$SQLID=$_POST["SQLIDD"];
	$QueryName=$_POST["QueryNameD"];
	$StringSQL=$_POST["StringSQLD"];
	$db=new ezSQL_pdo($db_type.":host=".$db_host.";dbname=".$db_name,$db_user,$db_password);
	$var = $db->get_var("SELECT count(*) FROM Tbl_SQL where QueryName='".$QueryName."'");
	if($var>0)
	{
		$db->show_errors();
		$db->query("delete FROM Tbl_SQL where SQLID=".$SQLID);
		echo "Query removed.";	
	}
	else
	{
		echo "Queryname not found.";	
	}
}
else if($_POST["sqllist"])
{
	
	$db=new ezSQL_pdo($db_type.":host=".$db_host.";dbname=".$db_name,$db_user,$db_password);
	$table = $db->get_results("select * from Tbl_SQL");	
	foreach($table as $query)
	{
		echo "<option value='".$query->SQLID."'>".$query->QueryName."</option>";	
	}	
}
else if($_POST["QueryExe"])
{
	//echo "SQL executed";
	include "getsql.php";
	$QueryExe=$_POST["QueryExe"];
	$SqlPara=$_POST["SqlPara"];
	if($_POST["SqlPara"])
	{
		$gs=new GetSQL($QueryExe,explode(",",$SqlPara));
		$gs->EchoTable();
	}
	else
	{
		//echo "SQL executed";
		$gs=new GetSQL($QueryExe);
		$gs->EchoTable();	
	}
}
else if($_FILES['Filedata'])
{
	include_once "./PHPExcel/Classes/PHPExcel.php";
	$tempFile=$_FILES['Filedata']['tmp_name'];	
	$targetFile="./uploads/".$_FILES['Filedata']['name'];
	$fileTypes = array('csv','xls','xlsx'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	if (in_array($fileParts['extension'],$fileTypes))
	{
		move_uploaded_file($tempFile,$targetFile);
	}
	else
	{
		echo 'Invalid file type.';
	}
	//echo $targetFile;
	
	$PHPExcel = new PHPExcel(); 
	
	$PHPReader = new PHPExcel_Reader_Excel2007(); 
	if(!$PHPReader->canRead($targetFile))
	{ 
		$PHPReader = new PHPExcel_Reader_Excel5(); 
		if(!$PHPReader->canRead($targetFile))
		{ 
			echo 'no Excel'; 
			return ; 
		} 
	} 
	
	$PHPExcel = $PHPReader->load($targetFile); 
	$currentSheet = $PHPExcel->getSheet(0); 
	$allColumn = PHPExcel_Cell::columnIndexFromString($currentSheet->getHighestColumn()); 
	$html="<select id='collist' name='collist' multiple='true' size='10' > ";
	$cols;
	for($currentColumn=0;$currentColumn<= $allColumn; $currentColumn++)	
	{
		$val = $currentSheet->getCellByColumnAndRow($currentColumn,1)->getValue();	
		$html.="<option value='".$val."'>".$val."</option>";
	}
	$html.="</select>";
	unlink($targetFile);
	echo $html;
}
else if($_POST["Table"] and $_POST["Columns"])
{
	$table=$_POST["Table"];
	$Columns=$_POST["Columns"];
	$db=new ezSQL_pdo($db_type.":host=".$db_host.";dbname=".$db_name,$db_user,$db_password);
	$fields=$db->get_results("show full columns from ".$table,ARRAY_A);
	$strTable="<table id='FieldsMatch' border='1' style='border-collapse:collapse'>
			<tr>";
	$strTable.="<th>Field</th><th>Type</th><th>Comment</th><th>Match</th>";
	$strTable.="</tr>";
	$fieldsshow = array('Field','Type','Comment');
	foreach($fields as $field)
	{
		$strTable.="<tr>";
		foreach($field as $key=>$value)
		{
			if(in_array($key,$fieldsshow))
			{
				$strTable.="<td>".$value."</td>";
			}
		}
		$strTable.="<td><select id='list".$field['Field']."' >";
		$row=$db->get_row("SELECT * FROM Tbl_Matches where MTableName='".$table."' and MField='".$field['Field']."'");
		if(!empty($row->MColumn))
		{
			if($row->MColumn!="none")
			{
				if(!in_array($row->MColumn,$Columns))
				{
					$strTable.="<option selected='selected' value='".$row->MColumn."'>".$row->MColumn."</option>";	
				}	
			}	
		}
		$strTable.="<option value='none'>none</option>";
		foreach($Columns as $Column)
		{
			if(!empty($row->MColumn))
			{
				if($row->MColumn!="none")
				{
					if($row->MColumn==$Column)
					{
						$strTable.="<option selected='selected' value='".$Column."'>".$Column."</option>";
						continue;
					}
				}
			}
			$strTable.="<option value='".$Column."'>".$Column."</option>";
		}
		/*
		$refertables = $db->get_results("SHOW TABLES",ARRAY_N);	
		$tableshid = array('tbl_sql','tbl_matches');
		foreach($refertables as $refertable)
		{
			if(!in_array($refertable[0],$tableshid))
			{
				$strTable.= "<option value='{refer}".$refertable[0]."'>refer to [".$refertable[0]."]</option>";
			}
		}
		*/
		$strTable.="</select></td></tr>";
	}
	$strTable.="</table>";
	echo $strTable;
}
else if($_POST["MFields"] and $_POST["MColumns"] and $_POST["MTypes"] and $_POST["MatchTable"])
{
	$Check=array();
	$MFields=$_POST["MFields"];
	$MColumns=$_POST["MColumns"];
	$MTypes=$_POST["MTypes"];
	$Matches=array();
	for($i=0;$i<count($MFields);$i++)
	{
		$Matches[$MFields[$i]]=	$MColumns[$i];
		$FieldTypes[$MFields[$i]]=$MTypes[$i];
	}
	
	$Table=$_POST["MatchTable"];
	//echo $Table;
	/*
	foreach($Matches as $Field=>$Column)
	{
		if($Column!="none")
		array_push($Check,$Column);	
	}
	if(count($Check) != count(array_unique($Check)))
	{
		echo "A column can only match into one field.";	
	}*/
	$db=new ezSQL_pdo($db_type.":host=".$db_host.";dbname=".$db_name,$db_user,$db_password);
	foreach($Matches as $Field=>$Column)
	{
		/*
		if($Column!="none")
		{
			$col = $db->get_var("SELECT count(*) FROM Tbl_Matches where MColumn='".$Column."'");
			if($col>0)
			{
				$row=$db->get_row("SELECT * FROM Tbl_Matches where MColumn='".$Column."'");
				if($row->MTableName!=$Table)	
				{
					echo "A column can only match into one field.";	
					return;	
				}
				else
				{
					if($Matches[$row->MField]!="none" and $row->MField!=$Field)	
					{
						echo "A column can only match into one field.";	
						return;		
					}
				}
			}
		}*/
		$var = $db->get_var("SELECT count(*) FROM Tbl_Matches where MTableName='".$Table."' and MField='".$Field."'");
		if($var>0)
		{
			$db->show_errors();
			if($Column!="none")
			$db->query("update Tbl_Matches set MColumn=\"".$Column."\" where MTableName=\"".$Table."\" and MField='".$Field."'");
			else
			$db->query("update Tbl_Matches set MColumn=NULL where MTableName=\"".$Table."\" and MField='".$Field."'");
			//echo "Updated";
		}
		else
		{
			$db->show_errors();
			if($Column!="none")
			$db->query("insert into Tbl_Matches(MTableName,MField,MColumn) values(\"".$Table."\",\"".$Field."\",\"".$Column."\")");
			else
			$db->query("insert into Tbl_Matches(MTableName,MField,MColumn) values(\"".$Table."\",\"".$Field."\",NULL)");
			//echo "Added";
		}
	}
	foreach($FieldTypes as $Field=>$Type)
	{
		$db->query("update Tbl_Matches set FieldType=\"".$Type."\" where MTableName=\"".$Table."\" and MField='".$Field."'");	
	}
}
?>
