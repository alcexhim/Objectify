<?php
	namespace Objectify\Objects;
	
	use Phast\System;

	use Phast\Data\DataSystem;
	use PDO;
		
	class Language
	{
		public $ID;
		public $Name;
		
		public static function GetByAssoc($values)
		{
			$item = new Language();
			$item->ID = $values["language_ID"];
			$item->Name = $values["language_Name"];
			return $item;
		}
		public static function Get()
		{
			$pdo = DataSystem::GetPDO();
			
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Languages";
			$statement = $pdo->prepare($query);
			$statement->execute();
			
			$retval = array();
			// if ($result === false) return $retval;
			
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$item = Language::GetByAssoc($values);
				if ($item != null) $retval[] = $item;
			}
			return $retval;
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			$pdo = DataSystem::GetPDO();
			
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Languages WHERE language_ID = :language_ID";
			$statement = $pdo->prepare($query);
			
			$statement->execute(array
			(
				":language_ID" => $id
			));
			$count = $statement->rowCount();
			if ($count < 1) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return Language::GetByAssoc($values);
		}
		public static function GetCurrent()
		{
			return Language::GetByID(1);
		}
	}
?>