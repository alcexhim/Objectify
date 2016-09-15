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
		 * The tenant on which this instance is defined.
		 * @var Tenant
		 */
		public $Tenant;
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
		
		private $relatedInstances = array();
		
		public function GetRelatedInstance($relationshipInstance)
		{
			$insts = $this->GetRelatedInstances($relationshipInstance);
			if (count($insts) > 0) return $insts[0];
			return null;
		}
		
		/**
		 * Gets the instances related to this instance by the specified relationship.
		 * @param Instance $relationshipInstance
		 */
		public function GetRelatedInstances($relationshipInstance)
		{
			$tenant = Tenant::GetCurrent();
			if (!array_key_exists($tenant->ID . "_" . $relationshipInstance->GetInstanceID(), $this->relatedInstances))
			{
				$pdo = DataSystem::GetPDO();
				
				$query = "SELECT relationship_DestinationInstanceID, relationship_DestinationObjectID FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Relationships WHERE "
					. "relationship_SourceTenantID = :relationship_SourceTenantID"
					. " AND relationship_SourceObjectID = :relationship_SourceObjectID"
					. " AND relationship_SourceInstanceID = :relationship_SourceInstanceID"
					. " AND relationship_RelationshipTenantID = :relationship_RelationshipTenantID"
					. " AND relationship_RelationshipObjectID = :relationship_RelationshipObjectID"
					. " AND relationship_RelationshipInstanceID = :relationship_RelationshipInstanceID"
					. " ORDER BY relationship_Order ASC";
				
				$statement = $pdo->prepare($query);
				$statement->execute(array
				(
					":relationship_SourceTenantID" => $this->Tenant->ID,
					":relationship_SourceObjectID" => $this->ParentObject->ID,
					":relationship_SourceInstanceID" => $this->ID,
					":relationship_RelationshipTenantID" => $relationshipInstance->Tenant->ID,
					":relationship_RelationshipObjectID" => $relationshipInstance->ParentObject->ID,
					":relationship_RelationshipInstanceID" => $relationshipInstance->ID
				));
				
				$count = $statement->rowCount();
				$array = array();
				for ($i = 0; $i < $count; $i++)
				{
					$values = $statement->fetch(PDO::FETCH_ASSOC);
					$relinst = Instance::GetByID($values["relationship_DestinationInstanceID"], $values["relationship_DestinationObjectID"]);
					$array[] = $relinst;
				}
				$this->relatedInstances[$tenant->ID . "_" . $relationshipInstance->GetInstanceID()] = $array;
			}
			return $this->relatedInstances[$tenant->ID . "_" . $relationshipInstance->GetInstanceID()];
		}
		
		/**
		 * 
		 * @param Instance $relationshipInstance
		 * @param boolean $includeParentObjects
		 * @param number $maxParentObjectLevels
		 * @return Relationship
		 */
		public function GetRelationship($relationshipInstance, $includeParentObjects = false, $maxParentObjectLevels = 1)
		{
			$rels = $this->GetRelationships($relationshipInstance, $includeParentObjects, $maxParentObjectLevels);
			if (count($rels) == 0) return null;
			
			return $rels[0];
		}
		/**
		 * 
		 * @param Instance $relationshipInstance
		 * @param boolean $includeParentObjects
		 * @param number $maxParentObjectLevels
		 * @return Relationship[]
		 */
		public function GetRelationships($relationshipInstance, $includeParentObjects = false, $maxParentObjectLevels = 1)
		{
			$rels = Relationship::GetBySourceInstance($this, $relationshipInstance, $includeParentObjects, $maxParentObjectLevels);
			return $rels;
		}
		
		public static function GetByAssoc($values)
		{
			$item = new Instance(TenantObject::GetByID($values["instance_ObjectID"]));
			$item->Tenant = Tenant::GetByID($values["instance_TenantID"]);
			$item->ID = $values["instance_ID"];
			$item->GlobalIdentifier = $values["instance_GlobalIdentifier"];
			return $item;
		}
		
		public static function Get($tenant = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Instances WHERE instance_TenantID = :instance_TenantID";
			if ($tenant == null) $tenant = Tenant::GetCurrent();
			$statement = $pdo->prepare($query);
			$retval = array();
			$result = $statement->execute(array
			(
				":instance_TenantID" => $tenant->ID
			));
			if ($result === false) return $retval;
			
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = Instance::GetByAssoc($values);
			}
			return $retval;
		}
		
		private static $instancesByID;
		public static function GetByID($id, $parentObjectID, $tenant = null)
		{
			if (Instance::$instancesByID == null)
			{
				Instance::$instancesByID = array();
			}
			if (!is_numeric($id)) return null;
			if (!is_numeric($parentObjectID)) return null;
			if ($tenant == null) $tenant = Tenant::GetCurrent();
			
			if (!array_key_exists($tenant->ID . "_" . $parentObjectID . "_" . $id, Instance::$instancesByID))
			{
				$pdo = DataSystem::GetPDO();
				$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Instances WHERE instance_ID = :instance_ID AND instance_ObjectID = :instance_ObjectID AND instance_TenantID = :instance_TenantID";
				$statement = $pdo->prepare($query);
				$result = $statement->execute(array
				(
					":instance_ID" => $id,
					":instance_ObjectID" => $parentObjectID,
					":instance_TenantID" => $tenant->ID
				));
				if ($result === false) return null;
				if ($statement->rowCount() == 0) return null;
				
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				Instance::$instancesByID[$tenant->ID . "_" . $parentObjectID . "_" . $id] = Instance::GetByAssoc($values);
			}
			return Instance::$instancesByID[$tenant->ID . "_" . $parentObjectID . "_" . $id];
		}
		
		public static function GetByInstanceID($instanceID, $tenant = null)
		{
			if (!is_string($instanceID)) return null;
			if ($tenant == null) $tenant = Tenant::GetCurrent();
			
			$instanceIDParts = explode("$", $instanceID, 2);
			if (count($instanceIDParts) != 2) return null;	
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Instances WHERE instance_TenantID = :instance_TenantID AND instance_ID = :instance_ID AND instance_ObjectID = :instance_ObjectID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":instance_TenantID" => $tenant->ID,
				":instance_ObjectID" => $instanceIDParts[0],
				":instance_ID" => $instanceIDParts[1]
			));
			if ($result === false) return null;
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
				
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return Instance::GetByAssoc($values);
		}
		
		private static $instancesByGID;
		/**
		 * Gets the Instance with the specified global identifier on the specified tenant (or the default tenant if no tenant is specified).
		 * @param string $globalIdentifier
		 * @param Tenant $tenant
		 * @return Instance
		 */
		public static function GetByGlobalIdentifier($globalIdentifier, $tenant = null)
		{
			if (Instance::$instancesByGID == null)
			{
				Instance::$instancesByGID = array();
			}
			
			if ($tenant == null) $tenant = Tenant::GetCurrent();
			
			$globalIdentifier = Objectify::SanitizeGlobalIdentifier($globalIdentifier);
			
			if (!array_key_exists($tenant->ID . "_" . $globalIdentifier, Instance::$instancesByGID))
			{
				$pdo = DataSystem::GetPDO();
				$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Instances WHERE instance_GlobalIdentifier = :instance_GlobalIdentifier AND instance_TenantID = :instance_TenantID";
				$statement = $pdo->prepare($query);
				$result = $statement->execute(array
				(
					":instance_GlobalIdentifier" => $globalIdentifier,
					":instance_TenantID" => $tenant->ID
				));
				
				if ($result === false) return null;
				if ($statement->rowCount() == 0) return null;
				
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				Instance::$instancesByGID[$tenant->ID . "_" . $globalIdentifier] = Instance::GetByAssoc($values);
			}
			return Instance::$instancesByGID[$tenant->ID . "_" . $globalIdentifier]; 
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
			if ($attribute == null) return false;
			
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
				":attval_TenantID" => $this->Tenant->ID,
				":attval_ObjectID" => $this->ParentObject->ID,
				":attval_InstanceID" => $this->ID,
				":attval_AttributeTenantID" => $attribute->Tenant->ID,
				":attval_AttributeObjectID" => $attribute->ParentObject->ID,
				":attval_AttributeInstanceID" => $attribute->ID
			);
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Attributes WHERE "
				. "attval_TenantID = :attval_TenantID"
				. " AND attval_ObjectID = :attval_ObjectID"
				. " AND attval_InstanceID = :attval_InstanceID"
				. " AND attval_AttributeTenantID = :attval_AttributeTenantID"
				. " AND attval_AttributeObjectID = :attval_AttributeObjectID"
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
			if ($attribute->ParentObject->Name == "BooleanAttribute")
			{
				if ($values["attval_Value"] == null) return null;
				if ($values["attval_Value"] == 1) return true;
				return false;
			}
			else if ($attribute->ParentObject->Name == "DateAttribute")
			{
				if ($values["attval_Value"] == null) return null;
				$dt = new \DateTime($values["attval_Value"]);
				return $dt;
			}
			else if ($attribute->ParentObject->Name == "NumericAttribute")
			{
				// we use floatval here instead of intval because Numeric Attributes can store Float values
				return floatval($values["attval_Value"]);
			}
			
			return $values["attval_Value"];
		}
		
		/**
		 * Sets the value of the specified Attribute for this Instance to the given value.
		 * @param Instance|string $attribute
		 * @param mixed $value
		 */
		public function SetAttributeValue($attribute, $value)
		{
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
			
			if (is_object($value))
			{
				if (get_class($value) == "DateTime")
				{
					$value = $value->format("Y-m-d H:i:s");
				}
			}
			
			$pdo = DataSystem::GetPDO();
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "Attributes ("
				. "attval_TenantID, attval_ObjectID, attval_InstanceID, attval_AttributeTenantID, attval_AttributeObjectID, attval_AttributeInstanceID, attval_EffectiveDateTime, attval_UserInstanceID, attval_Value"
				. ") VALUES ("
				. ":attval_TenantID, :attval_ObjectID, :attval_InstanceID, :attval_AttributeTenantID, :attval_AttributeObjectID, :attval_AttributeInstanceID, NOW(), :attval_UserInstanceID, :attval_Value"
				. ")";
			
			$user = User::GetCurrent();
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":attval_TenantID" => $this->Tenant->ID,
				":attval_ObjectID" => $this->ParentObject->ID,
				":attval_InstanceID" => $this->ID,
				":attval_AttributeTenantID" => $this->Tenant->ID, // ($attribute->Tenant == null ? null : $attribute->Tenant->ID),
				":attval_AttributeObjectID" => $attribute->ParentObject->ID,
				":attval_AttributeInstanceID" => $attribute->ID,
				":attval_UserInstanceID" => ($user == null ? null : $user->ID),
				":attval_Value" => $value
			));
			
			if ($statement->errorCode() != 0)
			{
				$ei = $statement->errorInfo();
				trigger_error("INST_SET_ATT: " . $ei[2]);
			}
		}
		
		/* ********** END: New Attribute functions to replace deprecated Property functions ********** */
		
		public function Update()
		{
			$pdo = DataSystem::GetPDO();
			if ($this->ID == null)
			{
				$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "Instances (instance_ID, instance_TenantID, instance_ObjectID, instance_GlobalIdentifier) VALUES (:instance_ID, :instance_TenantID, :instance_ObjectID, :instance_GlobalIdentifier)";
			}
			else
			{
				$query = "UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "Instances SET instance_GlobalIdentifier = :instance_GlobalIdentifier WHERE instance_ObjectID = :instance_ObjectID AND instance_ID = :instance_ID AND instance_TenantID = :instance_TenantID";
			}
			
			if ($this->ID == null) $this->ID = $this->ParentObject->GetNextInstanceID();
			
			$statement = $pdo->prepare($query);
			$paramz = array
			(
				":instance_ID" => $this->ID,
				":instance_TenantID" => $this->Tenant->ID,
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
				// $this->ID = $pdo->lastInsertId();
			}
			return true;
		}
		
		public function ToString()
		{
			// First get the Instance Display Title on the parent object and see if we have a format
			$parentObjectInstance = Instance::GetByGlobalIdentifier($this->ParentObject->GlobalIdentifier);
			$inst = $parentObjectInstance->GetRelatedInstance(KnownRelationships::get___Class__instance_labeled_by__String());
			
			if ($inst->ParentObject->Name == "String")
			{
				$componentInsts = $inst->GetRelatedInstances(KnownRelationships::get___String__has__String_Component());
				foreach ($componentInsts as $componentInst)
				{
					switch ($componentInst->ParentObject->Name)
					{
						case "TextConstantStringComponent":
						{
							$attValue = Instance::GetByGlobalIdentifier("{041DD7FD-2D9C-412B-8B9D-D7125C166FE0}");
							$value = $componentInst->GetAttributeValue($attValue);
							$retval .= $value;
							break;
						}
						case "InstanceAttributeStringComponent":
						{
							$instAttribute = $componentInst->GetRelatedInstance(KnownRelationships::get___Instance_Attribute_String_Component__has__Attribute());
							if ($instAttribute != null)
							{
								$propertyValue = $this->GetAttributeValue($instAttribute);
								$retval .= $propertyValue;
							}
							else
							{
								$propertyName = $componentInst->GetAttributeValue("PropertyName");
								$propertyValue = $this->GetAttributeValue($propertyName, "[ATT: " . $propertyName . " on " . $this->ParentObject->Name . "]");
								$retval .= $propertyValue;
							}
							break;
						}
						case "ExtractSingleInstanceStringComponent":
						{	
							// Extracts a single instance from the given Relationship.
							$instRel = $componentInst->GetRelatedInstance(KnownRelationships::get___Extract_Single_Instance_String_Component__has__Relationship());
							$inst = $this->GetRelatedInstance($instRel);
							if ($inst == null)
							{
								$retval .= "[ESI: no insts found for " . $instRel->GetInstanceID() . " on " . $this->GetInstanceID() . "]";
								break;
							}
							
							$propertyValue = $inst->ToString();
							$retval .= $propertyValue;
							break;
						}
					}
				}
			}
			return $retval;
		}
	}
?>