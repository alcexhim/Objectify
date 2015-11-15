<?php
	namespace Objectify\Objects;
	
	use Phast\System;
	use Phast\Data\DataSystem;
	
	use PDO;
	
	class TenantObjectInstance
	{
		public $ID;
		public $ParentObject;
		
		public function __construct($parentObject)
		{
			$this->ParentObject = $parentObject;
		}
		
		public static function GetByAssoc($values)
		{
			$item = new TenantObjectInstance(TenantObject::GetByID($values["instance_ObjectID"]));
			$item->ID = $values["instance_ID"];
			return $item;
		}
		
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstances WHERE instance_ID = " . $id;
			$result = $MySQL->query($query);
			if ($result === false) return null;
			
			$values = $result->fetch_assoc();
			return TenantObjectInstance::GetByAssoc($values);
		}
		
		public function GetPropertyValue($property, $defaultValue = null)
		{
			global $MySQL;
			
			if (is_string($property))
			{
				$property = $this->ParentObject->GetInstanceProperty($property);
			}
			if ($property == null) return $defaultValue;
			
			$query = "SELECT propval_Value FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstancePropertyValues WHERE propval_InstanceID = " . $this->ID . " AND propval_PropertyID = " . $property->ID;
			
			$result = $MySQL->query($query);
			if ($result === false) return $defaultValue;
			
			$count = $result->num_rows;
			if ($count == 0) return $defaultValue;
			
			$values = $result->fetch_array();
			return $property->Decode($values[0]);
		}
		public function SetPropertyValue($property, $value)
		{
			$pdo = DataSystem::GetPDO();
			
			if (is_string($property))
			{
				$property = $this->ParentObject->GetInstanceProperty($property);
			}
			if ($property == null) return false;
			
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstancePropertyValues (propval_InstanceID, propval_PropertyID, propval_Value) VALUES (:propval_InstanceID, :propval_PropertyID, :propval_Value)";
			$query .= " ON DUPLICATE KEY UPDATE ";
			$query .= "propval_PropertyID = values(propval_PropertyID), ";
			$query .= "propval_Value = values(propval_Value)";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
					":propval_InstanceID" => $this->ID,
					":propval_PropertyID" => $property->ID,
					":propval_Value" => $property->Encode($value)
			));
			
			if ($result === false) return false;
			
			return true;
		}
		public function HasPropertyValue($property)
		{
			global $MySQL;
			
			if ($property == null) return false;
			
			$query = "SELECT COUNT(propval_Value) FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjectInstancePropertyValues WHERE propval_InstanceID = " . $this->ID . " AND propval_PropertyID = " . $property->ID;
			
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			$count = $result->num_rows;
			if ($count == 0) return false;
			
			$values = $result->fetch_array();
			return ($values[0] > 0);
		}
		
		public function Update()
		{
			$pdo = DataSystem::GetPDO();
			if ($this->ID == null)
			{
				$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances (instance_ObjectID) VALUES (:instance_ObjectID)";
			}
			else
			{
				$query = "UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances SET instance_ObjectID = :instance_ObjectID WHERE instance_ID = :instance_ID";
			}
			
			$statement = $pdo->prepare($query);
			$paramz = array
			(
				":instance_ObjectID" => $this->ParentObject->ID
			);
			if ($this->ID != null) $paramz[":instance_ID"] = $this->ID;
			$result = $statement->execute($paramz);
			if ($result === false)
			{
				$ei = $statement->errorInfo();
				trigger_error("TenantObjectInstance->Update (" . $ei[1] . "): " . $ei[2]);
				return false;
			}
			
			if ($this->ID == null)
			{
				$this->ID = $pdo->lastInsertId();
			}
			return true;
		}
		
		public function ToString()
		{
			return $this->GetPropertyValue("Name");
		}
	}
?>