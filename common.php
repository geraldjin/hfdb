<?php
function ArrayToTable($arrays)
{
	if(count($arrays)>0)
	{
		$strTable="<table border='1' style='border-collapse:collapse'>
		<tr>";
		foreach($arrays[0] as $key=>$value)
		{
			$strTable.="<th>".$key."</th>";
		}
		$strTable.="</tr>";
		foreach($arrays as $array)
		{
			$strTable.="<tr>";
			foreach($array as $key=>$value)
			{
				$strTable.="<td>".$value."</td>";
			}
			$strTable.="</tr>";
		}
		$strTable.="</table>";
		echo $strTable;
	}	
}

?>