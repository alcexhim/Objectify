<?php
	namespace Objectify\Objects;
	
	use Phast\System;
	use PDO;
use Phast\Data\DataSystem;
	
	class Relationship
	{
		/**
		 * The internal identifier used to uniquely identify this relationship in the database.
		 * @var int
		 */
		public $ID;
		/**
		 * The tenant that owns this relationship. 
		 * @var Tenant
		 */
		public $Tenant;
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
		 * Gets the target(s) of this Relationship.
		 */
		public function GetDestinationInstances()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Instances, " . System::GetConfigurationValue("Database.TablePrefix") . "RelationshipTargets"
				. " WHERE " . System::GetConfigurationValue("Database.TablePrefix") . "RelationshipTargets.target_RelationshipID = :target_RelationshipID"
				. " AND " . System::GetConfigurationValue("Database.TablePrefix") . "Instances.instance_ID = " . System::GetConfigurationValue("Database.TablePrefix") . "RelationshipTargets.target_DestinationInstanceID"
				. " ORDER BY target_Order";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":target_RelationshipID" => $this->ID
			));
			$count = $statement->rowCount();
			$retval = array();
			
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$item = Instance::GetByAssoc($values);
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
						$query = "SELECT MAX(target_Order) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "RelationshipTargets WHERE target_RelationshipID = :target_RelationshipID";
						$statement = $pdo->prepare($query);
						$result = $statement->execute(array
						(
							":target_RelationshipID" => $this->ID
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
					
					$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "RelationshipTargets (target_RelationshipID, target_DestinationInstanceID, target_Order) VALUES (:target_RelationshipID, :target_DestinationInstanceID, :target_Order)";
					$statement = $pdo->prepare($query);
					$result = $statement->execute(array
					(
						":target_RelationshipID" => $this->ID,
						":target_DestinationInstanceID" => $inst->ID,
						":target_Order" => ($order == null ? 0 : $order)
					));
					
					if ($statement->errorCode() != 0)
					{
						$ei = $statement->errorInfo();
						Objectify::Log("Database error when trying to add a target instance to a Relationship", array
						(
							"Database Error Message" => $ei[2],
							"Database Error Code" => $ei[1],
							"Query" => $query,
							"Relationship Instance ID" => $this->RelationshipInstance->ID,
							"Source Instance ID" => $this->SourceInstance->ID,
							"Destination Instance ID" => $inst->ID
						));
						return false;
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
			$query = "DELETE FROM " . System::GetConfigurationValue("Database.TablePrefix") . "RelationshipTargets WHERE target_RelationshipID = :target_RelationshipID AND target_DestinationInstanceID = :target_DestinationInstanceID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":target_RelationshipID" => $this->ID,
				":target_DestinationInstanceID" => $inst->ID
			));
			if ($statement->errorCode() === 0) return true;
			return false;
		}
		
		public static function GetByAssoc($values)
		{
			$item = new Relationship();
			$item->ID = $values["relationship_ID"];
			$item->Tenant = Tenant::GetByID($values["relationship_TenantID"]);
			$item->RelationshipInstance = Instance::GetByID($values["relationship_RelationshipInstanceID"]);
			$item->SourceInstance = Instance::GetByID($values["relationship_SourceInstanceID"]);
			// $item->IsSingular = ($values["relationship_IsSingular"] == 1);
			return $item;
		}
		public static function Get()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Relationships";
			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			
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
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Relationships WHERE relationship_ID = :relationship_ID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":relationship_ID" => $id
			));
			
			$count = $statement->rowCount();
			if ($count === 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			$item = Relationship::GetByAssoc($values);
			return $item;
		}
		
		/**
		 * Gets all the Relationships associated with the specified source instance.
		 * @param Instance $inst The source instance whose relationships should be retrieved.
		 * @param Instance $relationshipInstance The instance of the Relationship to retrieve.
		 * @param boolean $includeParentObjects DO NOT SET THIS TO TRUE. YOU WILL BREAK EVERYTHING.
		 * @return Relationship[]
		 */
		public static function GetBySourceInstance($inst, $relationshipInstance = null, $includeParentObjects = false, $maxParentObjectLevels = 1)
		{
			$pdo = DataSystem::GetPDO();
			$paramz = array
			(
				":relationship_SourceInstanceID" => $inst->ID
			);
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Relationships WHERE (relationship_SourceInstanceID = :relationship_SourceInstanceID";
			if ($includeParentObjects)
			{
				self::Build_Get_Relationship_Query($query, $paramz, TenantObject::GetByGlobalIdentifier($inst->GlobalIdentifier), $maxParentObjectLevels);
			}
			$query .= ")";
			
			if ($relationshipInstance != null)
			{
				$query .= " AND relationship_RelationshipInstanceID = :relationship_RelationshipInstanceID";
				$paramz[":relationship_RelationshipInstanceID"] = $relationshipInstance->ID;
			}
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute($paramz);
			
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
			
			$rel = Relationship::GetBySourceInstance($sourceInstance, $relationshipInstance);
			if ($rel != null)
			{
				$rel = $rel[0];
				// we already have a relationship for this source instance and relationship instance, so just add a target instance
				foreach ($destinationInstances as $dest)
				{
					$retval = $rel->AddDestinationInstance($dest);
					if (!$retval) return false;
				}
				return true;
			}
			
			$pdo = DataSystem::GetPDO();
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "Relationships (relationship_TenantID, relationship_RelationshipInstanceID, relationship_SourceInstanceID) VALUES (:relationship_TenantID, :relationship_RelationshipInstanceID, :relationship_SourceInstanceID)";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":relationship_TenantID" => $tenant->ID,
				":relationship_RelationshipInstanceID" => $relationshipInstance->ID,
				":relationship_SourceInstanceID" => $sourceInstance->ID
			));
			
			if ($statement->errorCode() != 0)
			{
				$ei = $statement->errorInfo();
				Objectify::Log("Database error when trying to create a Relationship", array
				(
					"Database Error Message" => $ei[2],
					"Database Error Code" => $ei[1],
					"Query" => $query,
					"Relationship Instance ID" => $relationshipInstance->ID,
					"Source Instance ID" => $sourceInstance->ID
				));
				return false;
			}
			
			$id = $pdo->lastInsertId();
			$rel = Relationship::GetByID($id);
			
			foreach ($destinationInstances as $inst)
			{
				$rel->AddDestinationInstance($inst);
			}
			return true;
		}
	}
?>