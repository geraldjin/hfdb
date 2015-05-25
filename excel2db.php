<?php
include_once "db_para.php";
include_once "./ezSQL/shared/ez_sql_core.php";
include_once "./ezSQL/pdo/ez_sql_pdo.php";

if($_FILES['Filedata'] and $_POST['Datatype'])
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
	
	$DataType=$_POST['Datatype'];
	$tabename;
	switch($DataType)
	{
		case "TradeRecord":
			$tabename="Tbl_TradeRecord";
			break;
		case "SecurityPrice":
			$tabename="Tbl_SecurityPrice";
			break;
		case "FX":
			$tabename="Tbl_FX";
			break;
		case "Security":
			$tabename="Tbl_SecurityInfo";
			break;	
		case "Daily":
			$tabename="Tbl_DailyData";
			break;	
		//to be added
	}
	
	$PHPExcel = $PHPReader->load($targetFile); 
	$currentSheet = $PHPExcel->getSheet(0); 
	$allColumn = PHPExcel_Cell::columnIndexFromString($currentSheet->getHighestColumn());
	$allRow = $currentSheet->getHighestRow(); 
	$db=new ezSQL_pdo($db_type.":host=".$db_host.";dbname=".$db_name,$db_user,$db_password);
	$tempArray=array();
	for($col=0;$col<=$allColumn;$col++)
	{
		$row=$db->get_row("select * from Tbl_Matches where MTableName='".$tabename."' and MColumn='". $currentSheet->getCellByColumnAndRow($col,1)->getValue()."'");
		if(!(empty($row->MTableName)||empty($row->MField)))
		{
			$tempArray0=array();
			//$tempArray0["MTableName"]=	$row->MTableName;
			$tempArray0["MField"]=	$row->MField;
			$tempArray0["MColumn"]= $row->MColumn;
			$tempArray0["FieldType"]= $row->FieldType;
			$tempArray0["col"]= $col;
			if(!empty($row->MReference))
			{
				$tempArray0["MReference"]=$row->MReference;
				/*
				for($col1=0;$col1<=$allColumn;$col1++)
				{
					$row1=$db->get_row("select * from Tbl_Matches where MTableName='".$$row->MReference."' and MColumn='". $currentSheet->getCellByColumnAndRow($col,1)->getValue()."'");
					if(!(empty($row1->MTableName)||empty($row1->MField)))
					{
						$temprefer0["MField"]=	$row1->MField;
						$temprefer0["MColumn"]= $row1->MColumn;
						$temprefer0["FieldType"]= $row1->FieldType;
						$temprefer0["col"]= $col1;	
						$temprefer[]=$temprefer0;
					}	
				}
				$refers[$row->MField]=$temprefer;
				*/
			}
			$tempArray[]=$tempArray0;
			//array_push($tempArray,$tempArray0);			
		}
	}
	
	for($rowIndex=2;$rowIndex<=$allRow;$rowIndex++)
	{
		$insertField="";
		$insertValue="";
		$fromTable="";
		$strWhere="";
		
		foreach($tempArray as $rowdata)	
		{
			//$table=	$rowdata["MTableName"];
			$field= $rowdata["MField"];
			$insertField.=",".$field;
			$FieldType=$rowdata["FieldType"];
			$value=$currentSheet->getCellByColumnAndRow($rowdata["col"],$rowIndex)->getValue();
			if(stripos($FieldType,'date')>-1)
			{
				$time=true;
				$jd = GregorianToJD(1, 1, 1970);
        		$gregorian = JDToGregorian($jd+intval($value)-25569);	
				$myDate = explode('/',$gregorian);
				$myDateStr = str_pad($myDate[2],4,'0', STR_PAD_LEFT)
						."-".str_pad($myDate[0],2,'0', STR_PAD_LEFT)
						."-".str_pad($myDate[1],2,'0', STR_PAD_LEFT)
						.($time?" 00:00:00":'');
				$value=$myDateStr;
			}
			if(!empty($rowdata["MReference"]))
			{/*
				foreach($refers[$field] as $refer)
				{
					if(strpos($refer["FieldType"],'int')||strpos($refer["FieldType"],'decimal'))
					$referWhere.=" and ".$rowdata["MReference"].".".$refer["MField"]."=".$currentSheet->getCellByColumnAndRow($refer["col"],$rowIndex)->getValue();
					else
					$referWhere.=" and ".$rowdata["MReference"].".".$refer["MField"]."='".$currentSheet->getCellByColumnAndRow($refer["col"],$rowIndex)->getValue()."'";
				}*/
				$MReference=explode(",",$rowdata["MReference"]);
				$referTableField=$MReference[0];
				$referTableID=$MReference[1];
				$TableField=explode(".",$referTableField);
				$referTable=$TableField[0];
				$referField=$TableField[1];
				//$key=$db->get_row("select COLUMN_KEY,COLUMN_NAME from INFORMATION_SCHEMA.COLUMNS where table_name='".$referTable."' AND COLUMN_KEY='PRI'");
				$rowrefer=$db->get_var("select ".$referTableID." from ".$referTable." where ".$referField."='".$value."'");
				if(!empty($rowrefer))
				{
					$insertValue.=",".$rowrefer;	
				}
			}
			else
			{
				if(stripos($FieldType,'int')>-1||stripos($FieldType,'decimal')>-1)
					$insertValue.=",".$value;
				else
					$insertValue.=",'".$value."'";
			}
		}
		$strInsert="insert into ".$tabename."(".ltrim($insertField,",").") values(".ltrim($insertValue,",").")";
		$db->query($strInsert);
	}
	echo "Import data succeed"; 	
}
?>