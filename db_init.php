<?php
include "db_para.php";
include_once "./ezSQL/shared/ez_sql_core.php";
include_once "./ezSQL/pdo/ez_sql_pdo.php";

function initDB(){
	include "db_para.php";
	$db=new PDO($db_type.":host=".$db_host.";",$db_user,$db_password);
	
	//$con = mysql_connect($db_host,$db_user,$db_password);
	/*
	if (!$con)
	{
		die('Could not connect: ' . mysql_error());
	}
	*/
	//create database
	$strCreareDB="create database if not exists ".$db_name.";";
	try{
		$db->exec($strCreareDB);
		echo "Database created<br/>";
	}catch(PDOException $e){
		echo "Error creating database: " . $e->getMessage() . "<br/>";
		die();
	}
	
	//create table
	//mysql_select_db($db_name, $con);
	$db=new PDO($db_type.":host=".$db_host.";dbname=".$db_name,$db_user,$db_password);
	$strCreareTbl="create table if not exists Tbl_SQL
	(
	SQLID int not null auto_increment,
	PRIMARY KEY(SQLID),
	QueryName varchar(50) not null,
	StringSQL text not null
	)comment='system';";
	try{
		$db->exec($strCreareTbl);
		echo "Table Tbl_SQL created<br/>";
	}catch(PDOException $e){
		echo "Error creating table: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strCreareTbl="create table if not exists Tbl_TradeRecord
	(
	TradeID int not null auto_increment,
	PRIMARY KEY(TradeID),
	Fund varchar(25) null comment 'Name of Fund',
	Strategy int null,
	AssetClass int null,
	BuySell tinyint null,
	InstrumentClass int null,
	ListingJurisdiction int null,
	BusinessJurisdiction int null,
	Security int null,
	SecurityPrice_Base decimal(64,16) null,
	TradeDate datetime null,
	Quantity decimal(64,16) null,
	UnderlyingSecurity int null
	)comment='business';";
	try{
		$db->exec($strCreareTbl);
		echo "Table Tbl_TradeRecord created<br/>";
	}catch(PDOException $e){
		echo "Error creating table: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strCreareTbl="create table if not exists Tbl_DailyData
	(
	DailyID int not null auto_increment,
	PRIMARY KEY(DailyID),
	Fund varchar(25) null,
	Security int null,
	Date date null,
	Position decimal(64,16) null,
	Cost decimal(64,16) null
	)comment='business';";
	try{
		$db->exec($strCreareTbl);
		echo "Table Tbl_DailyData created<br/>";
	}catch(PDOException $e){
		echo "Error creating table: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strCreareTbl="create table if not exists Tbl_SecurityInfo
	(
	SecurityID int not null auto_increment,
	PRIMARY KEY(SecurityID),
	SecurityName varchar(50) null,
	SettlementCurrency int null,
	BBGCode varchar(25) null,
	ISIN varchar(25) null
	)comment='businesssupport';";
	try{
		$db->exec($strCreareTbl);
		echo "Table Tbl_SecurityInfo created<br/>";
	}catch(PDOException $e){
		echo "Error creating table: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strCreareTbl="create table if not exists Tbl_Strategy
	(
	StrategyID int not null auto_increment,
	PRIMARY KEY(StrategyID),
	Strategy varchar(50) null
	)comment='businesssupport';";
	try{
		$db->exec($strCreareTbl);
		echo "Table Tbl_Strategy created<br/>";
	}catch(PDOException $e){
		echo "Error creating table: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strCreareTbl="create table if not exists Tbl_AssetClass
	(
	AssetID int not null auto_increment,
	PRIMARY KEY(AssetID),
	AssetClass varchar(50) null
	)comment='businesssupport';";
	try{
		$db->exec($strCreareTbl);
		echo "Table Tbl_AssetClass created<br/>";
	}catch(PDOException $e){
		echo "Error creating table: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strCreareTbl="create table if not exists Tbl_InstrumentClass
	(
	InstrumentID int not null auto_increment,
	PRIMARY KEY(InstrumentID),
	InstrumentClass varchar(50) null
	)comment='businesssupport';";
	try{
		$db->exec($strCreareTbl);
		echo "Table Tbl_InstrumentClass created<br/>";
	}catch(PDOException $e){
		echo "Error creating table: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strCreareTbl="create table if not exists Tbl_Currency
	(
	CurrencyID int not null auto_increment,
	PRIMARY KEY(CurrencyID),
	Currency varchar(50) null
	)comment='businesssupport';";
	try{
		$db->exec($strCreareTbl);
		echo "Table Tbl_Currency created<br/>";
	}catch(PDOException $e){
		echo "Error creating table: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strCreareTbl="create table if not exists Tbl_Jurisdiction
	(
	JurisdictionID int not null auto_increment,
	PRIMARY KEY(JurisdictionID),
	Jurisdiction varchar(50) null
	)comment='businesssupport';";
	try{
		$db->exec($strCreareTbl);
		echo "Table Tbl_Jurisdiction created<br/>";
	}catch(PDOException $e){
		echo "Error creating table: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strCreareTbl="create table if not exists Tbl_SecurityPrice
	(
	PriceID int not null auto_increment,
	PRIMARY KEY(PriceID),
	Security int null,
	Date date null,
	Price decimal(64,16)
	)comment='business';";
	try{
		$db->exec($strCreareTbl);
		echo "Table Tbl_SecurityPrice created<br/>";
	}catch(PDOException $e){
		echo "Error creating table: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strCreareTbl="create table if not exists Tbl_FX
	(
	FXID int not null auto_increment,
	PRIMARY KEY(FXID),
	Currency int null,
	Date date null,
	FX decimal(64,16)
	)comment='business';
	";
	try{
		$db->exec($strCreareTbl);
		echo "Table Tbl_FX created<br/>";
	}catch(PDOException $e){
		echo "Error creating table: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strCreareTbl="create table if not exists Tbl_Matches
	(
	MatchID int not null auto_increment,
	PRIMARY KEY(MatchID),
	MTableName varchar(50) null,
	MField varchar(50) null,
	FieldType varchar(25) null,
	MColumn varchar(50) null,
	MReference varchar(100) null
	)comment='system';
	";
	try{
		$db->exec($strCreareTbl);
		echo "Table Tbl_Matches created<br/>";
	}catch(PDOException $e){
		echo "Error creating table: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strDelete="delete from tbl_matches";
	try{
		$db->exec($strDelete);
	}catch(PDOException $e){
		echo "Error initialize data: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strInsert="insert into tbl_matches(MTableName,MField,MReference) values
				('tbl_traderecord','Strategy','tbl_strategy.Strategy,StrategyID'),
				('tbl_traderecord','AssetClass','tbl_assetclass.AssetClass,AssetID'),
				('tbl_traderecord','InstrumentClass','tbl_instrumentclass.InstrumentClass,InstrumentID'),
				('tbl_securityInfo','SettlementCurrency','tbl_currency.Currency,CurrencyID'),
				('tbl_traderecord','ListingJurisdiction','tbl_jurisdiction.Jurisdiction,JurisdictionID'),
				('tbl_traderecord','BusinessJurisdiction','tbl_jurisdiction.Jurisdiction,JurisdictionID'),
				('tbl_traderecord','Security','tbl_securityInfo.SecurityName,SecurityID'),
				('tbl_traderecord','UnderlyingSecurity','tbl_securityInfo.SecurityName,SecurityID'),
				('tbl_securityprice','Security','tbl_securityInfo.SecurityName,SecurityID'),
				('tbl_fx','Currency','tbl_currency.Currency,CurrencyID'),
				('tbl_dailydata','Security','tbl_securityInfo.SecurityName,SecurityID')";
	try{
		$db->exec($strInsert);
		echo "Tbl_Matches data initialized<br/>";
	}catch(PDOException $e){
		echo "Error initialize data: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strInsert="insert into tbl_Strategy(Strategy) values
				('Long/Short'),('Long')";
	try{
		$db->exec($strInsert);
		echo "Tbl_Strategy data initialized<br/>";
	}catch(PDOException $e){
		echo "Error initialize data: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strInsert="insert into tbl_assetclass(AssetClass) values
				('Equity'),('Bond')";
	try{
		$db->exec($strInsert);
		echo "Tbl_AssetClass data initialized<br/>";
	}catch(PDOException $e){
		echo "Error initialize data: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strInsert="insert into tbl_instrumentclass(InstrumentClass) values
				('Ordinary Share'),('Depository Receipt'),('Contract for Difference'),('Warrant'),('Swap')";
	try{
		$db->exec($strInsert);
		echo "Tbl_InstrumentClass data initialized<br/>";
	}catch(PDOException $e){
		echo "Error initialize data: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strInsert="insert into tbl_currency(Currency) values
				('USD'),('HKD'),('INR'),('IDR'),('JPY'),('KRW')";
	try{
		$db->exec($strInsert);
		echo "Tbl_Currency data initialized<br/>";
	}catch(PDOException $e){
		echo "Error initialize data: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$strInsert="insert into tbl_jurisdiction(Jurisdiction) values
				('HK'),('India'),('Indonesia'),('Japan'),('South Korea')";
	try{
		$db->exec($strInsert);
		echo "Tbl_Jurisdiction data initialized<br/>";
	}catch(PDOException $e){
		echo "Error initialize data: " . $e->getMessage() . "<br/>";
		die();
	}
	
	$db=null;
}
?>