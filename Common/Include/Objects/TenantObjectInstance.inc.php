<?php
	namespace Objectify\Objects;
	
	use Phast\System;
	use Phast\Data\DataSystem;
	
	use PDO;
	
	class TenantObjectInstance
	{
		/**
		 * The internal identifier of this TenantObjectInstance in the database.
		 * @var int
		 */
		public $ID;
		/**
		 * The parent TenantObject of which this is an instance.
		 * @var TenantObject
		 */
		public $ParentObject;
		/**
		 * The global identifier of this instance.
		 * @var string
		 */
		public $GlobalIdentifier;
		
		public function __construct($parentObject)
		{
			$this->ParentObject = $parentObject;
		}
		
		public static function GetByAssoc($values)
		{
			$item = new TenantObjectInstance(TenantObject::GetByID($values["instance_ObjectID"]));
			$item->ID = $values["instance_ID"];
			$item->GlobalIdentifier = $values["instance_GlobalIdentifier"];
			return $item;
		}
		
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances WHERE instance_ID = :instance_ID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":instance_ID" => $id
			));
			if ($result === false) return null;
			if ($statement->rowCount() == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return TenantObjectInstance::GetByAssoc($values);
		}
		
		public static function GetByGlobalIdentifier($globalIdentifier)
		{
			$globalIdentifier = str_replace("{", "", $globalIdentifier);
			$globalIdentifier = str_replace("}", "", $globalIdentifier);
			$globalIdentifier = str_replace("-", "", $globalIdentifier);
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances WHERE instance_GlobalIdentifier = :instance_GlobalIdentifier";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":instance_GlobalIdentifier" => $globalIdentifier
			));
			if ($result === false) return null;
			if ($statement->rowCount() == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return TenantObjectInstance::GetByAssoc($values);
		}
		
		public function GetPropertyValue($property, $defaultValue = null)
		{
			$pdo = DataSystem::GetPDO();
			
			if (is_string($property))
			{
				$property = $this->ParentObject->GetInstanceProperty($property);
			}
			if ($property == null) return $defaultValue;
			
			$query = "SELECT propval_Value FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstancePropertyValues WHERE propval_InstanceID = :propval_InstanceID AND propval_PropertyID = :propval_PropertyID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":propval_InstanceID" => $this->ID,
				":propval_PropertyID" => $property->ID
			));
			
			if ($result === false) return $defaultValue;
			
			$count = $statement->rowCount();
			if ($count == 0) return $defaultValue;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return $property->Decode($values["propval_Value"]);
		}
		public function SetPropertyValue($property, $value)
		{
			$pdo = DataSystem::GetPDO();
			
			if (is_string($property))
			{
				$property = $this->ParentObject->GetInstanceProperty($property);
			}
			if ($property == null) return false;
			
			if (
					get_class($value) == "Objectify\\Objects\\MultipleInstanceProperty"
				|| get_class($value) == "Objectify\\Objects\\SingleInstanceProperty"
				)
			{
				if ($value->ValidObjects == null)
				{
					$oldvalue = $this->GetPropertyValue($property);
					$value->ValidObjects = $oldvalue->ValidObjects;
				}
			}
			
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
			$pdo = DataSystem::GetPDO();
			
			if (is_string($property))
			{
				$property = $this->ParentObject->GetInstanceProperty($property);
			}
			if ($property == null) return false;
			
			$query = "SELECT COUNT(propval_Value) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstancePropertyValues WHERE propval_InstanceID = :propval_InstanceID AND propval_PropertyID = :propval_PropertyID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":propval_InstanceID" => $this->ID,
				":propval_PropertyID" => $property->ID
			));
			if ($result === false) return false;
			
			$count = $statement->rowCount();
			if ($count == 0) return false;
			
			$values = $statement->fetch(PDO::FETCH_NUM);
			return ($values[0] > 0);
		}
		
		public function Update()
		{
			$pdo = DataSystem::GetPDO();
			if ($this->ID == null)
			{
				$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances (instance_ObjectID, instance_GlobalIdentifier) VALUES (:instance_ObjectID, :instance_GlobalIdentifier)";
			}
			else
			{
				$query = "UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances SET instance_ObjectID = :instance_ObjectID, instance_GlobalIdentifier = :instance_GlobalIdentifier WHERE instance_ID = :instance_ID";
			}
			
			$statement = $pdo->prepare($query);
			$paramz = array
			(
				":instance_ObjectID" => $this->ParentObject->ID,
				":instance_GlobalIdentifier" => $this->GlobalIdentifier
			);
			if ($this->ID != null) $paramz[":instance_ID"] = $this->ID;
			$result = $statement->execute($paramz);
			if ($result === false)
			{
				$ei = $statement->errorInfo();
				Objectify::Log("Database error when trying to create or update an instance of the specified object.", array
				(
					"DatabaseError" => $ei[2] . " (" . $ei[1] . ")",
					"Query" => $query,
					"Tenant Object Instance ID" => $this->ID
				));
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
			$propTitle = $this->GetPropertyValue("Title");
			if ($propTitle != null)
			{
				$insts = $propTitle->GetInstances();
				
				$objLanguage = TenantObject::GetByName("Language");
				$defaultLanguage = $objLanguage->GetInstance(array
				(
					new TenantObjectInstancePropertyValue("Code", "en-US")
				));
				
				foreach ($insts as $inst)
				{
					if ($inst->GetPropertyValue("Language")->GetInstance() == $defaultLanguage) return $inst->GetPropertyValue("Value");
				}
			}
			return $this->GetPropertyValue("Name");
		}
	}
?>