<?php
	namespace Objectify\Objects;
	use Phast\System;
	use Phast\Data\DataSystem;
	use PDO;
		
	class TenantType
	{
		public $ID;
		public $Title;
		public $Description;
		
		public static function GetByAssoc($values)
		{
			$item = new TenantType();
			$item->ID = $values["tenanttype_ID"];
			$item->URL = $values["tenanttype_Title"];
			$item->Description = $values["tenanttype_Description"];
			return $item;
		}
		public static function Get($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantTypes";
			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			
			$count = $statement->rowCount();
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = TenantType::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantTypes WHERE tenanttype_ID = :tenanttype_ID";
			$statement = $pdo->prepare($query);
			
			$result = $statement->execute(array
			(
				":tenanttype_ID" => $id
			));
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return TenantType::GetByAssoc($values);
		}
		
		public function ToJSON()
		{
			echo("{");
			echo("\"ID\":" . $this->ID . ",");
			echo("\"Title\":\"" . \JH\Utilities::JavaScriptDecode($this->Title, "\"") . "\",");
			echo("\"Description\":\"" . \JH\Utilities::JavaScriptDecode($this->Description, "\"") . "\"");
			echo("}");
		}
	}
?>