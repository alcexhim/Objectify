<?php
	namespace Objectify\Objects;
	
	use Phast\System;
	use PDO;
use Phast\Data\DataSystem;
	
	class Relationship
	{
		public function __construct($relationshipInstance, $sourceInstance)
		{
			$this->RelationshipInstance = $relationshipInstance;
			$this->SourceInstance = $sourceInstance;
		}
		
		/**
		 * The instance that specifies the relationship between the source and the target. Must be an instance of Relationship class (1$3).
		 * @var Instance
		 */
		public $RelationshipInstance;
		/**
		 * The source of this relationship.
		 * @var Instance
		 */
		public $SourceInstance;
		
		/**
		 * Gets the first target of this Relationship. 
		 * @return Instance
		 */
		public function GetDestinationInstance()
		{
			$insts = $this->GetDestinationInstances();
			if (count($insts) > 0)
			{
				return $insts[0];
			}
			return null;
		}
		
		/**
		 * Gets the target(s) of this Relationship.
		 * @return Instance[]
		 */
		public function GetDestinationInstances()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT relationship_DestinationTenantID, relationship_DestinationObjectID, relationship_DestinationInstanceID FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Relationships"
				. " WHERE relationship_RelationshipTenantID = :relationship_RelationshipTenantID AND relationship_RelationshipObjectID = :relationship_RelationshipObjectID AND relationship_RelationshipInstanceID = :relationship_RelationshipInstanceID"
				. " AND relationship_SourceTenantID = :relationship_SourceTenantID AND relationship_SourceObjectID = :relationship_SourceObjectID AND relationship_SourceInstanceID = :relationship_SourceInstanceID"
				. " ORDER BY relationship_Order";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":relationship_RelationshipTenantID" => $this->RelationshipInstance->Tenant->ID,
				":relationship_RelationshipObjectID" => $this->RelationshipInstance->ParentObject->ID,
				":relationship_RelationshipInstanceID" => $this->RelationshipInstance->ID,
				":relationship_SourceTenantID" => $this->SourceInstance->Tenant->ID,
				":relationship_SourceObjectID" => $this->SourceInstance->ParentObject->ID,
				":relationship_SourceInstanceID" => $this->SourceInstance->ID
			));
			$count = $statement->rowCount();
			$retval = array();
			
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$item = Instance::GetByID($values["relationship_DestinationInstanceID"], $values["relationship_DestinationObjectID"]);
				$retval[] = $item;
			}
			return $retval;
		}
		
		/**
		 * Adds the specified instance as a target for this Relationship.
		 * @param Instance|Instance[] $inst
		 */
		public function AddDestinationInstance($inst, $order = null)
		{
			if ($this->RelationshipInstance->GetAttributeValue(KnownAttributes::get___Boolean___Singular(), false))
			{
				$destInsts = $this->GetDestinationInstances();
				if (count($destInsts) > 0)
				{
					trigger_error("XquizIT: attempted to add another instance to a Singular relationship");
					return false;
				}
			}
			
			if (is_array($inst))
			{
				$count = count($inst);
				for ($i = 0; $i < $count; $i++)
				{
					$result = $this->AddDestinationInstance($inst[$i]);
					if (!$result) return false;
				}
				return true;
			}
			else if (is_object($inst))
			{
				if (get_class($inst) === "Objectify\\Objects\\Instance")
				{
					$pdo = DataSystem::GetPDO();
					
					if ($order == null)
					{
						$query = "SELECT MAX(relationship_Order) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Relationships WHERE "
							. "relationship_SourceTenantID = :relationship_SourceTenantID AND relationship_SourceObjectID = :relationship_SourceObjectID AND relationship_SourceInstanceID = :relationship_SourceInstanceID AND relationship_RelationshipTenantID = :relationship_RelationshipTenantID AND relationship_RelationshipObjectID = :relationship_RelationshipObjectID AND relationship_RelationshipInstanceID = :relationship_RelationshipInstanceID";
						$statement = $pdo->prepare($query);
						$result = $statement->execute(array
						(
							":relationship_SourceTenantID" => $this->SourceInstance->Tenant->ID,
							":relationship_SourceObjectID" => $this->SourceInstance->ParentObject->ID,
							":relationship_SourceInstanceID" => $this->SourceInstance->ID,
							":relationship_RelationshipTenantID" => $this->RelationshipInstance->Tenant->ID,
							":relationship_RelationshipObjectID" => $this->RelationshipInstance->ParentObject->ID,
							":relationship_RelationshipInstanceID" => $this->RelationshipInstance->ID
						));
						if ($result !== false)
						{
							$count = $statement->rowCount();
							$order = 0;
							if ($count > 0) 
							{
								$values = $statement->fetch(PDO::FETCH_NUM);
								$order = $values[0] + 1;
							}
						}
					}
					
					$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "Relationships ("
						. "relationship_RelationshipTenantID, relationship_RelationshipObjectID, relationship_RelationshipInstanceID, relationship_SourceTenantID, relationship_SourceObjectID, relationship_SourceInstanceID, relationship_DestinationTenantID, relationship_DestinationObjectID, relationship_DestinationInstanceID, relationship_Order"
						. ") VALUES ("
						. ":relationship_RelationshipTenantID, :relationship_RelationshipObjectID, :relationship_RelationshipInstanceID, :relationship_SourceTenantID, :relationship_SourceObjectID, :relationship_SourceInstanceID, :relationship_DestinationTenantID, :relationship_DestinationObjectID, :relationship_DestinationInstanceID, :relationship_Order)";
					$statement = $pdo->prepare($query);
					$result = $statement->execute(array
					(
						":relationship_SourceTenantID" => $this->SourceInstance->Tenant->ID,
						":relationship_SourceObjectID" => $this->SourceInstance->ParentObject->ID,
						":relationship_SourceInstanceID" => $this->SourceInstance->ID,
						":relationship_RelationshipTenantID" => $this->RelationshipInstance->Tenant->ID,
						":relationship_RelationshipObjectID" => $this->RelationshipInstance->ParentObject->ID,
						":relationship_RelationshipInstanceID" => $this->RelationshipInstance->ID,
						":relationship_DestinationTenantID" => $inst->Tenant->ID,
						":relationship_DestinationObjectID" => $inst->ParentObject->ID,
						":relationship_DestinationInstanceID" => $inst->ID,
						":relationship_Order" => ($order == null ? 0 : $order)
					));
					if ($result === false)
					{
						$ei = $statement->errorInfo();
						trigger_error("xq-relationship-destination-add: " . $ei[2]);
					}
					return true;
				}
				else
				{
					Objectify::Log("Could not add the specified object as a target instance to a Relationship since it is not an Instance", array
					(
						"Relationship Instance ID" => $this->RelationshipInstance->ID,
						"Source Instance ID" => $this->SourceInstance->ID
					));
					return false;
				}
			}
		}
		public function RemoveDestinationInstance($inst)
		{
			$pdo = DataSystem::GetPDO();
			$query = "DELETE FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Relationships WHERE "
				. "relationship_RelationshipTenantID = :relationship_RelationshipTenantID AND relationship_RelationshipObjectID = :relationship_RelationshipObjectID AND relationship_RelationshipInstanceID = :relationship_RelationshipInstanceID AND relationship_SourceTenantID = :relationship_SourceTenantID AND relationship_SourceObjectID = :relationship_SourceObjectID AND relationship_SourceInstanceID = :relationship_SourceInstanceID AND relationship_DestinationTenantID = :relationship_DestinationTenantID AND relationship_DestinationObjectID = :relationship_DestinationObjectID AND relationship_DestinationInstanceID = :relationship_DestinationInstanceID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":relationship_SourceTenantID" => $this->SourceInstance->Tenant->ID,
				":relationship_SourceObjectID" => $this->SourceInstance->ParentObject->ID,
				":relationship_SourceInstanceID" => $this->SourceInstance->ID,
				":relationship_RelationshipTenantID" => $this->RelationshipInstance->Tenant->ID,
				":relationship_RelationshipObjectID" => $this->RelationshipInstance->ParentObject->ID,
				":relationship_RelationshipInstanceID" => $this->RelationshipInstance->ID,
				":relationship_DestinationTenantID" => $inst->Tenant->ID,
				":relationship_DestinationObjectID" => $inst->ParentObject->ID,
				":relationship_DestinationInstanceID" => $inst->ID
			));
			if ($statement->errorCode() !== 0)
			{
				$ei = $statement->errorInfo();
				trigger_error("xq-relationship-destination-remove: " . $ei[2]);
				return false;
			}
			
			return true;
		}
		
		public static function GetByAssoc($values)
		{
			$item = new Relationship();
			$item->Tenant = Tenant::GetByID($values["relationship_SourceTenantID"]);
			$item->RelationshipInstance = Instance::GetByID($values["relationship_RelationshipInstanceID"], $values["relationship_RelationshipObjectID"]);
			$item->SourceInstance = Instance::GetByID($values["relationship_SourceInstanceID"], $values["relationship_SourceObjectID"]);
			return $item;
		}
		public static function Get($tenant = null)
		{
			if ($tenant == null) $tenant = Tenant::GetCurrent();
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT DISTINCT relationship_SourceTenantID, relationship_SourceObjectID, relationship_SourceInstanceID, relationship_RelationshipTenantID, relationship_RelationshipObjectID, relationship_RelationshipInstanceID FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Relationships WHERE relationship_RelationshipTenantID = :relationship_RelationshipTenantID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":relationship_RelationshipTenantID" => $tenant->ID
			));
			
			$retval = array();
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$item = Relationship::GetByAssoc($values);
				$retval[] = $item;
			}
			return $retval;
		}
		
		/**
		 * Gets all the Relationships associated with the specified source instance.
		 * @param Instance $inst The source instance whose relationships should be retrieved.
		 * @param Instance $relationshipInstance The instance of the Relationship to retrieve.
		 * @param boolean $includeParentObjects DO NOT SET THIS TO TRUE. YOU WILL BREAK EVERYTHING.
		 * @return Relationship[]
		 */
		public static function GetBySourceInstance($inst, $relationshipInstance, $includeParentObjects = false, $maxParentObjectLevels = 1, $tenant = null)
		{
			if ($tenant == null) $tenant = Tenant::GetCurrent();
			
			$pdo = DataSystem::GetPDO();
			$paramz = array
			(
				":relationship_SourceTenantID" => $inst->Tenant->ID,
				":relationship_SourceObjectID" => $inst->ParentObject->ID,
				":relationship_SourceInstanceID" => $inst->ID
			);
			$query = "SELECT DISTINCT relationship_SourceTenantID, relationship_SourceObjectID, relationship_SourceInstanceID, relationship_RelationshipTenantID, relationship_RelationshipObjectID, relationship_RelationshipInstanceID FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Relationships WHERE relationship_SourceTenantID = :relationship_SourceTenantID AND (relationship_SourceObjectID = :relationship_SourceObjectID AND relationship_SourceInstanceID = :relationship_SourceInstanceID";
			/*
			if ($includeParentObjects)
			{
				self::Build_Get_Relationship_Query($query, $paramz, TenantObject::GetByGlobalIdentifier($inst->GlobalIdentifier), $maxParentObjectLevels);
			}
			*/
			$query .= ") AND ((:relationship_RelationshipTenantID IS NULL AND :relationship_RelationshipObjectID IS NULL AND :relationship_RelationshipInstanceID IS NULL) OR (relationship_RelationshipTenantID = :relationship_RelationshipTenantID AND relationship_RelationshipObjectID = :relationship_RelationshipObjectID AND relationship_RelationshipInstanceID = :relationship_RelationshipInstanceID))";
			
			if ($relationshipInstance == null)
			{
				$paramz[":relationship_RelationshipTenantID"] = null;
				$paramz[":relationship_RelationshipObjectID"] = null;
				$paramz[":relationship_RelationshipInstanceID"] = null;
			}
			else
			{
				$paramz[":relationship_RelationshipTenantID"] = $relationshipInstance->Tenant->ID;
				$paramz[":relationship_RelationshipObjectID"] = $relationshipInstance->ParentObject->ID;
				$paramz[":relationship_RelationshipInstanceID"] = $relationshipInstance->ID;
			}
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute($paramz);
			if ($result === false)
			{
				$ei = $statement->errorInfo();
				trigger_error("xq-relationship-get: " . $ei[2]);
			}
			
			$count = $statement->rowCount();
			$retval = array();
			
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$item = Relationship::GetByAssoc($values);
				$retval[] = $item;
			}
			return $retval;
		}
		
		/**
		 * Builds a "Get Relationship Query"
		 * @param unknown $query
		 * @param unknown $paramz
		 * @param TenantObject $parentObject
		 */
		private static function Build_Get_Relationship_Query(&$query, &$paramz, $parentObject, $level = 0, $maxParentObjectLevels = 1)
		{
			if ($parentObject == null) return;
			if ($level > $maxParentObjectLevels) return;
			
			$parentObjects = $parentObject->GetParentObjects();
			$parentObjectCount = count($parentObjects);
			for ($i = 0; $i < $parentObjectCount; $i++)
			{
				$query .= " OR relationship_SourceInstanceID = :relationship_SourceInstanceID" . $parentObjects[$i]->ID;
				$paramz[":relationship_SourceInstanceID" . $parentObjects[$i]->ID] = $parentObjects[$i]->ID;
				self::Build_Get_Relationship_Query($query, $paramz, $parentObjects[$i], $level + 1, $maxParentObjectLevels);
			}
		}
		
		/**
		 * Creates a Relationship between a source instance and one or more destination instances.
		 * @param Instance $relationshipInstance Instance of the Relationship (1$3) to create.
		 * @param Instance $sourceInstance Source instance to associate with this Relationship.
		 * @param Instance[] $destinationInstances Array of target instances to associate with this Relationship.
		 */
		public static function Create($relationshipInstance, $sourceInstance, $destinationInstances, $tenant = null)
		{
			if ($tenant === null) $tenant = Tenant::GetCurrent();
			
			if ($relationshipInstance === null)
			{
				Objectify::Log("Relationship instance cannot be null when creating a new Relationship");
				return false;
			}
			if ($sourceInstance === null)
			{
				Objectify::Log("Source instance cannot be null when creating a new Relationship");
				return false;
			}
			
			if (!is_array($destinationInstances))
			{
				$destinationInstances = array($destinationInstances);
			}
			
			if (is_object($relationshipInstance) && is_object($sourceInstance))
			{
				if (!((get_class($relationshipInstance) === "Objectify\\Objects\\Instance")
						&& (get_class($sourceInstance) === "Objectify\\Objects\\Instance")))
				{
					Objectify::Log("Relationship instance or source instance invalid - are not Instances");
					return false;
				}
			}
			else
			{
				Objectify::Log("Relationship instance or source instance invalid - are not objects");
				return false;
			}
			
			$rel = new Relationship($relationshipInstance, $sourceInstance);
			foreach ($destinationInstances as $inst)
			{
				$rel->AddDestinationInstance($inst);
			}
			return $rel;
		}
	}
?>