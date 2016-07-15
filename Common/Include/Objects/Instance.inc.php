<?php
	namespace Objectify\Objects;
	
	use Phast\System;
	use Phast\Data\DataSystem;
	
	use PDO;
	
	class Instance
	{
		/**
		 * The internal identifier of this Instance in the database.
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
		
		public function HasParentObject($obj)
		{
			$b = false;
			if (is_string($obj)) $obj = TenantObject::GetByName($objectOrName);
			
			$po = $this->GetParentObjects();
			while (count($po) > 0)
			{
				foreach ($po as $p)
				{
					// I have no idea what the fuck I'm doing.
					// It's 11:35 PM and I'm tired and I'm going to bed.
				}
			}
			
			return $b;
		}
		
		public function GetInstanceID()
		{
			return $this->ParentObject->ID . "$" . $this->ID;
		}
		
		public static function GetByAssoc($values)
		{
			$item = new Instance(TenantObject::GetByID($values["instance_ObjectID"]));
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
			return Instance::GetByAssoc($values);
		}
		
		public static function GetByGlobalIdentifier($globalIdentifier)
		{
			$globalIdentifier = Objectify::SanitizeGlobalIdentifier($globalIdentifier);
			
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
			return Instance::GetByAssoc($values);
		}
		
		/* ********** BEGIN: New Attribute functions to replace deprecated Property functions ********** */
		
		/**
		 * Gets the value of the specified Attribute for this Instance as of the given date.
		 * @param Instance|string $attribute
		 * @param mixed $defaultValue
		 * @param \DateTime $effectiveDateTime
		 */
		public function GetAttributeValue($attribute, $defaultValue = null, $effectiveDateTime = null)
		{
			$pdo = DataSystem::GetPDO();
			
			if (is_string($attribute))
			{
				$attribute = $this->ParentObject->GetAttribute($attribute);
			}
			
			if (is_object($attribute))
			{
				if (get_class($attribute) != "Objectify\\Objects\\Instance")
				{
				}
			}
			else
			{
				return false;
			}
			
			$paramz = array
			(
				":attval_TenantID" => $this->ParentObject->Tenant->ID,
				":attval_InstanceID" => $this->ID,
				":attval_AttributeInstanceID" => $attribute->ID
			);
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "AttributeValues WHERE "
				. "attval_TenantID = :attval_TenantID"
				. " AND attval_InstanceID = :attval_InstanceID"
				. " AND attval_AttributeInstanceID = :attval_AttributeInstanceID";

			if ($effectiveDateTime == null) $effectiveDateTime = date("Y-m-d H:i:s");
			if ($effectiveDateTime != null)
			{
				$query .= " AND attval_EffectiveDateTime <= :attval_EffectiveDateTime";
				$paramz[":attval_EffectiveDateTime"] = $effectiveDateTime;
			}
			
			$query .= " ORDER BY attval_EffectiveDateTime DESC";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute($paramz);
			
			if ($result === false)
			{
				return false;
			}
			
			$count = $statement->rowCount();
			if ($count == 0) return $defaultValue;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			
			return $values["attval_Value"];
		}
		
		/**
		 * Sets the value of the specified Attribute for this Instance to the given value.
		 * @param Instance|string $attribute
		 * @param mixed $value
		 */
		public function SetAttributeValue($attribute, $value)
		{
			$pdo = DataSystem::GetPDO();
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "AttributeValues ("
				. "attval_TenantID, attval_AttributeInstanceID, attval_InstanceID, attval_EffectiveDateTime, attval_UserInstanceID, attval_Value"
				. ") VALUES ("
				. ":attval_TenantID, :attval_AttributeInstanceID, :attval_InstanceID, NOW(), :attval_UserInstanceID, :attval_Value"
				. ")";
			
			$user = User::GetCurrent();
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":attval_TenantID" => $this->ParentObject->Tenant->ID,
				":attval_AttributeInstanceID" => $attribute->ID,
				":attval_InstanceID" => $this->ID,
				":attval_UserInstanceID" => ($user == null ? null : $user->ID),
				":attval_Value" => $value
			));
		}
		
		/* ********** END: New Attribute functions to replace deprecated Property functions ********** */
		
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
			$parentObjectInstance = Instance::GetByGlobalIdentifier($this->ParentObject->GlobalIdentifier);
			$rels = Relationship::GetBySourceInstance($parentObjectInstance, KnownRelationships::get___Class__instance_labeled_by__String());
			
			if (count($rels) > 0)
			{
				$rels = $rels[0];
				$insts = $rels->GetDestinationInstances();
				$inst = $insts[0];
				
				if ($inst->ParentObject->Name == "String")
				{
					$components = $inst->GetPropertyValue("Components");
					if ($components === null)
					{
						trigger_error("XquizIT: Instance Display Title - Components property is NULL");
						return "";
					}
					
					$insts = $components->GetInstances();
					foreach ($insts as $inst)
					{
						switch ($inst->ParentObject->Name)
						{
							case "TextConstantStringComponent":
							{
								$value = $inst->GetPropertyValue("Value");
								$retval .= $value;
								break;
							}
							case "InstanceAttributeStringComponent":
							{
								$propertyName = $inst->GetAttributeValue("PropertyName");
								$propertyValue = $this->GetAttributeValue($propertyName, "[ATT: " . $propertyName . " on " . $this->ParentObject->Name . "]");
								$retval .= $propertyValue;
								break;
							}
							case "ExtractSingleInstanceStringComponent":
							{
								// Extracts a single instance from the given Relationship.
								$propertyName = $inst->GetAttributeValue("PropertyName");
								
								$instRel = Instance::GetByGlobalIdentifier($propertyName);
								$rels = Relationship::GetBySourceInstance($this, $instRel);
								$rel = $rels[0];
								$insts = $rel->GetDestinationInstances();
								$inst = $insts[0];
								
								$propertyValue = $inst->ToString();
								$retval .= $propertyValue;
								break;
							}
							case "InstancePropertyStringComponent":
							{
								$propertyName = $inst->GetAttributeValue("PropertyName");
								$propertyValue = $this->GetPropertyValue($propertyName, "[" . $propertyName . " on " . $this->ParentObject->Name . "]");
								
								if ($propertyName == "Values" && $this->ParentObject->Name == "TranslatableTextConstant")
								{
									// HACK: look up the "has Translatable Text Constant Value" relationship for this TTC
									$retval = "";
									$instrel = KnownRelationships::get___Translatable_Text_Constant__has__Translatable_Text_Constant_Value();
									
									$rels = Relationship::GetBySourceInstance($this, $instrel);
									if (count($rels) > 0)
									{
										$rels = $rels[0];
										$insts = $rels->GetDestinationInstances();
										$insts = $insts[0];
										return $insts->ToString();
									}
								}
								
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
				}
				return $retval;
			}
			else
			{
				// HACK HACK HACK UGLY HACK REMOVE WHEN WE HAVE ATTRIBUTES WORKING CORRECTLY
				$propval_Name = $this->GetPropertyValue("Name");
				if ($propval_Name != null) return $propval_Name;
				// end ugly hack

				// HACK HACK HACK UGLY HACK REMOVE WHEN WE HAVE ATTRIBUTES WORKING CORRECTLY
				$instAttName = Instance::GetByGlobalIdentifier("{9153A637-992E-4712-ADF2-B03F0D9EDEA6}");
				$propval_Name = $this->GetAttributeValue($instAttName);
				if ($propval_Name != null) return $propval_Name;
				// end ugly hack
				
				return "[" . $this->ParentObject->Name . "]";
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