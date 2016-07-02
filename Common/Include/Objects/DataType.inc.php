<?php

namespace Objectify\Objects;

use Phast\System;
use Phast\Data\DataSystem;
use PDO;

class DataType
{
	public $ID;
	public $Name;
	public $Description;
	public $EncoderCodeBlob;
	public $DecoderCodeBlob;
	public $ColumnRendererCodeBlob;
	public $EditorRendererCodeBlob;
	public static function GetByAssoc($values)
	{
		$item = new DataType();
		$item->ID = $values["datatype_ID"];
		$item->Name = $values["datatype_Name"];
		$item->Description = $values["datatype_Description"];
		
		$item->EncoderCodeBlob = $values["datatype_EncoderCodeBlob"];
		$item->DecoderCodeBlob = $values["datatype_DecoderCodeBlob"];
		$item->ColumnRendererCodeBlob = $values["datatype_ColumnRendererCodeBlob"];
		$item->EditorRendererCodeBlob = $values["datatype_EditorRendererCodeBlob"];
		return $item;
	}
	public static function GetByID($id)
	{
		if (!is_numeric($id)) return null;
		
		$pdo = DataSystem::GetPDO();
		$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "DataTypes WHERE datatype_ID = :datatype_ID";
		$statement = $pdo->prepare($query);
		
		$result = $statement->execute(array
		(
			":datatype_ID" => $id
		));
		if ($result === false) return null;
		
		$count = $statement->rowCount();
		if ($count == 0) return null;
		
		$values = $statement->fetch(PDO::FETCH_ASSOC);
		return DataType::GetByAssoc($values);
	}
	public static function GetByName($name)
	{
		$pdo = DataSystem::GetPDO();
		$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "DataTypes WHERE datatype_Name = :datatype_Name";
		
		$statement = $pdo->prepare($query);
		$result = $statement->execute(array (
				":datatype_Name" => $name 
		));
		if ($result === false) return null;
		
		$count = $statement->rowCount();
		if ($count == 0)
		{
			Objectify::Log("No data type with the specified name was found.", array (
					"DataType" => $name 
			));
			return null;
		}
		
		$values = $statement->fetch(PDO::FETCH_ASSOC);
		return DataType::GetByAssoc($values);
	}
	public function Encode($value)
	{
		if ($this->EncoderCodeBlob == null) return $value;
		$q = '';
		$q .= 'use Objectify\Objects\Objectify; ';
		$q .= 'use Objectify\Objects\MultipleInstanceProperty; ';
		$q .= 'use Objectify\Objects\SingleInstanceProperty; ';
		$q .= 'use Objectify\Objects\TenantObject; ';
		$q .= 'use Objectify\Objects\Instance; ';
		$q .= '$x = function($input) { ' . $this->EncoderCodeBlob . ' };';
		// trigger_error("calling EncoderCodeBlob on DataType '" . $this->Name . "'", E_USER_NOTICE);
		eval($q);
		return $x($value);
	}
	public function Decode($value)
	{
		if ($this->DecoderCodeBlob == null) return $value;
		$q = '';
		$q .= 'use Objectify\Objects\Objectify; ';
		$q .= 'use Objectify\Objects\MultipleInstanceProperty; ';
		$q .= 'use Objectify\Objects\SingleInstanceProperty; ';
		$q .= 'use Objectify\Objects\TenantObject; ';
		$q .= 'use Objectify\Objects\Instance; ';
		$q .= '$x = function($input) { ' . $this->DecoderCodeBlob . ' };';
		// trigger_error("calling DecoderCodeBlob on DataType '" . $this->Name . "'", E_USER_NOTICE);
		eval($q);
		return $x($value);
	}
	public function RenderColumn($value)
	{
		if ($this->ColumnRendererCodeBlob == null) return;
		$q = '';
		$q .= 'use Objectify\Objects\MultipleInstanceProperty; ';
		$q .= 'use Objectify\Objects\SingleInstanceProperty; ';
		$q .= 'use Objectify\Objects\TenantObject; ';
		$q .= 'use Objectify\Objects\Instance; ';
		$q .= '$x = function($input) { ' . $this->ColumnRendererCodeBlob . ' };';
		// trigger_error("calling ColumnRendererCodeBlob on DataType '" . $this->Name . "'", E_USER_NOTICE);
		eval($q);
		$x($value);
	}
	public function RenderEditor($value, $name)
	{
		if ($this->EditorRendererCodeBlob == null) return;
		
		$q = '';
		$q .= 'use Objectify\Objects\MultipleInstanceProperty; ';
		$q .= 'use Objectify\Objects\SingleInstanceProperty; ';
		$q .= 'use Objectify\Objects\TenantObject; ';
		$q .= 'use Objectify\Objects\Instance; ';
		$q .= '$x = function($input, $name) { ' . $this->EditorRendererCodeBlob . ' };';
		// trigger_error("calling EditorRendererCodeBlob on DataType '" . $this->Name . "'", E_USER_NOTICE);
		
		eval($q);
		
		// if $x is not set, then there must have been an error in parsing so stop rendering
		if (!isset($x)) return;
		
		$x($value, $name);
	}
}
?>