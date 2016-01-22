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
		
		public function GetInstanceID()
		{
			return $this->ParentObject->ID . "$" . $this->ID;
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
			
			$query = "SELECT propval_Value FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstancePropertyValues WHERE propval_TenantID = :propval_TenantID AND propval_ObjectID = :propval_ObjectID AND propval_InstanceID = :propval_InstanceID AND propval_PropertyID = :propval_PropertyID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":propval_TenantID" => $this->ParentObject->Tenant->ID,
				":propval_ObjectID" => $this->ParentObject->ID,
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

			if (is_object($parentObjects))
			{
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
			}
			
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstancePropertyValues (propval_TenantID, propval_ObjectID, propval_InstanceID, propval_PropertyID, propval_Value) VALUES (:propval_TenantID, :propval_ObjectID, :propval_InstanceID, :propval_PropertyID, :propval_Value)";
			$query .= " ON DUPLICATE KEY UPDATE ";
			$query .= "propval_PropertyID = values(propval_PropertyID), ";
			$query .= "propval_Value = values(propval_Value)";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":propval_TenantID" => $this->ParentObject->Tenant->ID,
				":propval_ObjectID" => $this->ParentObject->ID,
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
				if ($this->ParentObject->HasInstanceProperty($property))
				{
					$property = $this->ParentObject->GetInstanceProperty($property);
				}
				else
				{
					$property = null;
				}
			}
			if ($property == null) return false;
			
			$query = "SELECT COUNT(propval_Value) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstancePropertyValues WHERE propval_TenantID = :propval_TenantID AND propval_ObjectID = :propval_ObjectID AND propval_InstanceID = :propval_InstanceID AND propval_PropertyID = :propval_PropertyID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":propval_TenantID" => $this->ParentObject->Tenant->ID,
				":propval_ObjectID" => $this->ParentObject->ID,
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
				$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances (instance_TenantID, instance_ObjectID, instance_GlobalIdentifier) VALUES (:instance_TenantID, :instance_ObjectID, :instance_GlobalIdentifier)";
			}
			else
			{
				$query = "UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances SET instance_ObjectID = :instance_ObjectID, instance_GlobalIdentifier = :instance_GlobalIdentifier WHERE instance_ID = :instance_ID AND instance_TenantID = :instance_TenantID";
			}
			
			$statement = $pdo->prepare($query);
			$paramz = array
			(
				":instance_TenantID" => $this->ParentObject->Tenant->ID,
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
			// First get the Instance Display Title on the parent object and see if we have a format
			$propInstanceDisplayTitle = $this->ParentObject->GetPropertyValue("InstanceDisplayTitle");
			if ($propInstanceDisplayTitle != null)
			{
				$propInstanceDisplayTitle_Value = $propInstanceDisplayTitle->GetInstance();
				if ($propInstanceDisplayTitle_Value != null)
				{
					$components = $propInstanceDisplayTitle_Value->GetPropertyValue("Components");
					$componentInstances = $components->GetInstances();
					$retval = "";
					foreach ($componentInstances as $inst)
					{
						switch ($inst->ParentObject->Name)
						{
							case "TextConstantStringComponent":
							{
								$value = $inst->GetPropertyValue("Value");
								$retval .= $value;
								break;
							}
							case "InstancePropertyStringComponent":
							{
								$propertyName = $inst->GetPropertyValue("PropertyName");
								$propertyValue = $this->GetPropertyValue($propertyName);
								if (is_object($propertyValue))
								{
									switch (get_class($propertyValue))
									{
										case "Objectify\\Objects\\MultipleInstanceProperty":
										{
											$insts = $propertyValue->GetInstances();
											$instCount = count($insts);
											$propertyValue = "";
											for ($i = 0; $i < $instCount; $i++)
											{
												$propertyValue .= $insts[$i]->ToString();
												if ($i < $instCount - 1) $propertyValue .= " ";
											}
											break;
										}
										case "Objectify\\Objects\\SingleInstanceProperty":
										{
											$propertyValue = $propertyValue->GetInstance()->ToString();
											break;
										}
										case "Objectify\\Objects\\TenantObject":
										{
											$propertyValue = $propertyValue->ToString();
											break;
										}
									}
								}
								$retval .= $propertyValue;
								break;
							}
						}
					}
					return $retval;
				}
			}
			
			/*
			// If we do not have an Instance Display Title for the parent object, see
			// if we have an instance property named Title and use that
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
			*/
			
			// When all else fails, use the Name property
			
			// TODO: DON'T RELY ON THIS!!!
			// this works for LanguageString but we really need to implement Instance Display Title ASAP!
			if ($this->HasPropertyValue("Value")) return $this->GetPropertyValue("Value");
			return "";
		}
	}
?>