<?php
include_once "db_para.php";
include_once "./ezSQL/shared/ez_sql_core.php";
include_once "./ezSQL/pdo/ez_sql_pdo.php";

$db = new ezSQL_pdo($db_type.":host=".$db_host.";dbname=".$db_name,$db_user,$db_password);

class GetSQL{
	
	var $strSQL;
	//var $QueryPara;
	public function __construct($QueryName,$QueryPara=null)
	{
		global $strSQL;
		global $db;
		$row=$db->get_row("select StringSQL from Tbl_SQL where QueryName='".$QueryName."'");
		$this->strSQL=$row->StringSQL;
		if($QueryPara!=null)
		{
			$ParaCount=count($QueryPara);
			$ParainSQL=substr_count($this->strSQL,"%")-substr_count($this->strSQL,"%%");
			if($ParaCount==$ParainSQL)
			{
				for($i=0;$i<$ParaCount;$i++)
				{
					$this->strSQL=sprintf($this->strSQL,mysql_real_escape_string($QueryPara[$i]));
				}	
			}
			else
			{
				echo "Parameters not matched.";
			}
		}
	}
	
	public function GetResults($type="A")
	{
		global $strSQL;
		global $db;
		if($type=="O")
		{
			return $db->get_results($this->strSQL);	
		}
		else
		{
			return $db->get_results($this->strSQL,ARRAY_A);		
		}
	}
	
	public function GetRow($type="A")
	{
		global $strSQL;
		global $db;
		if($type=="O")
		{
			return $db->get_row($this->strSQL);
		}
		else
		{
			return $db->get_results($this->strSQL,ARRAY_A);
		}
	}
	
	public function GetVar()
	{
		global $strSQL;
		global $db;
		return $db->get_var($this->strSQL);	
	}
	
	public function ExcuteSQL()
	{
		global $strSQL;
		global $db;
		$db->query($this->strSQL);
	}
	
	public function EchoTable()
	{
		global $strSQL;
		global $db;
		if($results=$db->get_results($this->strSQL,ARRAY_A))
		{
			$strTable="<table border='1' style='border-collapse:collapse'>
			<tr>";
			foreach($results[0] as $key=>$value)
			{
				$strTable.="<th>".$key."</th>";
			}
			$strTable.="</tr>";
			foreach($results as $result)
			{
				$strTable.="<tr>";
				foreach($result as $key=>$value)
				{
					$strTable.="<td>".$value."</td>";
				}
				$strTable.="</tr>";
			}
			$strTable.="</table>";
			echo $strTable;
		}
	}
	
	public function GetOption($results,$flegend,$fxaxis,$fseries,$charttype)
	{
		$option=array();
		$legends=array();
		$xaxises=array();
		$series=array();
		foreach($results as $result)
		{
			if(!in_array($result[$flegend],$legends))
			{
				array_push($legends,$result[$flegend]);	
			}
			if(!in_array($result[$fxaxis],$xaxises))
			{
				array_push($xaxises,$result[$fxaxis]);	
			}	
		}
		foreach($legends as $l)
		{
			$lseries=array();
			$data=array();
			foreach($xaxises as $x)
			{
				foreach($results as $result)
				{
					if(in_array($l,$result) and in_array($x,$result))
					{
						$data[]=$result[$fseries];
						break;
					}	
				}	
			}
			$lseries["name"]=$l;
			$lseries["type"]=$charttype;
			$lseries["data"]=$data;		
			$series[]=$lseries;
		}
		$option["legend"]=$legends;
		$option["xaxis"]=array("type"=>"category","boundaryGap"=>"true","data"=>$xaxises);
		$option["series"]=$series;
		return $option;
	}
	
	public function GetPieOption($results,$flegend,$fdata)
	{
		$legends=array();
		$datas=array();
		foreach($results as $result)
		{
			$data=array();
			array_push($legends,$result[$flegend]);	
			$data["value"]=$result[$fdata];
			$data["name"]=$result[$flegend];
			$datas[]=$data;
		}	
		$optionPie =array(
					 "legend"=>$legends,
					 "series"=>array(
                        array("name"=>"","type"=>"pie","stack"=>"",
                                "data"=>$datas),
								));
		return $optionPie;
	}
}
?>