<?php
	namespace Objectify\Objects;
	
	use Phast\Data\DataSystem;
	use Phast\System;
	use Phast\UUID;
	use PDO;
		
	class Tenant
	{
		public $ID;
		public $Name;
		public $GlobalIdentifier;
		
		public static function GetByAssoc($values)
		{
			$item = new Tenant();
			$item->ID = $values["tenant_ID"];
			$item->Name = $values["tenant_Name"];
			$item->GlobalIdentifier = $values["tenant_GlobalIdentifier"];
			return $item;
		}
		public static function Get()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix");
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			$count = $statement->rowCount();
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$item = Tenant::GetByAssoc($values);
				$retval[] = $item;
			}
			return $retval;
		}
		
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Tenants WHERE tenant_ID = :tenant_ID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":tenant_ID" => $id
			));
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return Tenant::GetByAssoc($values);
		}
		public static function GetByName($name)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Tenants WHERE tenant_Name = :tenant_Name";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":tenant_Name" => $name
			));
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return Tenant::GetByAssoc($values);
		}

		public static function ExistsByName($name)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT COUNT(tenant_ID) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Tenants WHERE tenant_Name = :tenant_Name";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":tenant_Name" => $name
			));
				
			$values = $statement->fetch(PDO::FETCH_NUM);
			return ($values[0] > 0);
		}
		
		/**
		 * Gets the current Tenant.
		 */
		public static function GetCurrent()
		{
			$tenantName = System::GetTenantName();
			if ($tenantName != "")
			{
				return Tenant::GetByName($tenantName);
			}
			else if (isset($_SESSION["CurrentTenantID"]))
			{
				return Tenant::GetByID($_SESSION["CurrentTenantID"]);
			}
			return null;
		}
		
		public static function Create($name)
		{
			$guid = UUID::Generate();
			$guid = Objectify::SanitizeGlobalIdentifier($guid);
			
			$pdo = DataSystem::GetPDO();
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "Tenants (tenant_Name, tenant_GlobalIdentifier) VALUES (:tenant_Name, :tenant_GlobalIdentifier)";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":tenant_Name" => $name,
				":tenant_GlobalIdentifier" => $guid
			));
			if ($result === false) return null;
			
			$lastInsertId = $pdo->lastInsertId();
			return Tenant::GetByID($lastInsertId);
		}
		
		/**
		 * Gets all the TenantObjects available on this Tenant.
		 */
		public function GetObjects()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjects WHERE object_TenantID = :object_TenantID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":tenant_ID" => $this->ID
			));
			$count = $statement->rowCount();
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$item = TenantObject::GetByAssoc($values);
				$retval[] = $item;
			}
			return $retval;
		}
		
		public function GetNextObjectID()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT MAX(object_ID) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjects WHERE object_TenantID = :object_TenantID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":object_TenantID" => $this->IDD
			));
			$count = $statement->rowCount();
			if ($count == 0) return 0;
			
			$values = $statement->fetch(PDO::FETCH_NUM);
			return $values[0];
		}
		
		/**
		 * Gets the Instance representation of this Tenant.
		 * @return Instance The Instance representation of this Tenant.
		 */
		public function GetThisInstance()
		{
			return Instance::GetByGlobalIdentifier($this->GlobalIdentifier);
		}
		
	}
?>