<?php
	namespace Objectify\Objects;
	
	use Phast\System;
	use Phast\Data\DataSystem;
	
	use PDO;
	
	class TenantObject
	{
		/**
		 * The internal identifier assigned to this TenantObject.
		 * @var int
		 */
		public $ID;
		/**
		 * The Tenant that owns this TenantObject.
		 * @var Tenant
		 */
		public $Tenant;
		public $Name;
		
		/**
		 * The global identifier used to uniquely identify this TenantObject across migrations.
		 * @var string
		 */
		public $GlobalIdentifier;
		
		/**
		 * Gets the TenantObject represented by the given database table row values.
		 * @param array $values
		 */
		public static function GetByAssoc($values)
		{
			$item = new TenantObject();
			$item->ID = $values["object_ID"];
			$item->Tenant = Tenant::GetByID($values["object_TenantID"]);
			$item->Name = $values["object_Name"];
			$item->GlobalIdentifier = $values["object_GlobalIdentifier"];
			return $item;
		}
		
		/**
		 * Gets all TenantObjects on the server.
		 * @param int $max
		 * @return TenantObject[]
		 */
		public static function Get($max = null)
		{
			$pdo = DataSystem::GetPDO();
			
			$retval = array();
			
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjects";
			if (is_numeric($max)) $query .= " LIMIT " . $max;

			$statement = $pdo->prepare($query);
			$result = $statement->execute();
			
			if ($result === false) return $retval;
			
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$item = TenantObject::GetByAssoc($values);
				if ($item == null) continue;
				$retval[] = $item;
			}
			return $retval;
		}
		
		public static function GetByID($id, $tenant = null)
		{
			if (!is_numeric($id)) return null;
			if ($tenant == null) $tenant = Tenant::GetCurrent();
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjects WHERE object_TenantID = :object_TenantID AND object_ID = :object_ID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":object_TenantID" => $tenant->ID,
				":object_ID" => $id
			));
			if ($result === false) return null;
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return TenantObject::GetByAssoc($values);
		}
		
		public static function GetByGlobalIdentifier($globalIdentifier)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjects WHERE object_GlobalIdentifier = :object_GlobalIdentifier";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":object_GlobalIdentifier" => $globalIdentifier
			));
			if ($result === false) return null;
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return TenantObject::GetByAssoc($values);
		}
		
		/**
		 * Gets the TenantObject with the specified name.
		 * @param string $name The name of the TenantObject to search for.
		 * @param string $tenant The tenant on which to search for the object.
		 * @return TenantObject The TenantObject with the specified name, or NULL if no TenantObject with the specified name exists.
		 */
		public static function GetByName($name, $tenant = null)
		{
			$pdo = DataSystem::GetPDO();
			if ($tenant == null) $tenant = Tenant::GetCurrent();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjects WHERE object_Name = :object_Name AND object_TenantID = :object_TenantID";
			$statement = $pdo->prepare($query);
			
			$result = $statement->execute(array
			(
				":object_Name" => $name,
				":object_TenantID" => ($tenant == null ? null : $tenant->ID)
			));
			
			if ($result === false) return null;
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return TenantObject::GetByAssoc($values);
		}
		
		public function GetParentObjects()
		{
			$instRel_Class__has_super_Class = Instance::GetByGlobalIdentifier("{100F0308-855D-4EC5-99FA-D8976CA20053}");
			$instThisClass = Instance::GetByGlobalIdentifier($this->GlobalIdentifier);
			$rels = Relationship::GetBySourceInstance($instThisClass, $instRel_Class__has_super_Class, false);
			
			$retval = array();
			
			if (count($rels) > 0)
			{
				$rels = $rels[0];
				if ($rels != null)
				{
					$instTargets = $rels->GetDestinationInstances();
					foreach ($instTargets as $inst)
					{
						$retval[] = TenantObject::GetByGlobalIdentifier($inst->GlobalIdentifier);
					}
				}
			}
			return $retval;
		}
		
		public function GetChildObjects()
		{
			$instRel_Class__has_sub_Class = Instance::GetByGlobalIdentifier("{C14BC80D-879C-4E6F-9123-E8DFB13F4666}");
			$instThisClass = Instance::GetByGlobalIdentifier($this->GlobalIdentifier);
			$rels = Relationship::GetBySourceInstance($instThisClass, $instRel_Class__has_sub_Class);
			$rels = $rels[0];
			
			$retval = array();
			
			if ($rels != null)
			{
				$instTargets = $rels->GetDestinationInstances();
				foreach ($instTargets as $inst)
				{
					$retval[] = TenantObject::GetByGlobalIdentifier($inst->GlobalIdentifier);
				}
			}
			return $retval;
		}
		
		/**
		 * Adds the specified TenantObject as a parent of this TenantObject.
		 * @param TenantObject $obj
		 */
		public function AddParentObject($obj)
		{
			$instRel_Class__has_super_Class = Instance::GetByGlobalIdentifier("{100F0308-855D-4EC5-99FA-D8976CA20053}");
			
			$instThisClass = Instance::GetByGlobalIdentifier($this->GlobalIdentifier);
			$instThatClass = Instance::GetByGlobalIdentifier($obj->GlobalIdentifier);
			
			$retval = Relationship::Create($instRel_Class__has_super_Class, $instThisClass, $instThatClass);
			if (!$retval) return false;
			
			return true;
		}
		
		/**
		 * Creates a TenantObject.
		 * @param string $name
		 * @param TenantObject $parentObject
		 * @return TenantObject
		 */
		public static function Create($name, $parentObjects = null, $globalIdentifier = null, $tenant = null)
		{
			$pdo = DataSystem::GetPDO();
			
			if ($parentObjects == null) $parentObjects = array();
			if ($tenant == null) $tenant = Tenant::GetCurrent();
			
			if (is_object($parentObjects))
			{
				if (get_class($parentObjects) == "Objectify\\Objects\\TenantObject")
				{
					$parentObjects = array($parentObjects);
				}
			}
			if (!is_array($parentObjects)) $parentObjects = array();
			
			$retval = array();
			
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjects (";
			$query .= "object_ID, object_TenantID, object_Name, object_GlobalIdentifier";
			$query .= ") VALUES (";
			$query .= ":object_ID, :object_TenantID, :object_Name, :object_GlobalIdentifier";
			$query .= ")";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":object_ID" => $tenant->GetNextObjectID(),
				":object_TenantID" => $tenant->ID,
				":object_Name" => $name,
				":object_GlobalIdentifier" => $globalIdentifier
			));
			
			if ($result === false) return null;
			$obj = TenantObject::GetByName($name);
			if ($obj == null) return null;
			
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "Instances (";
			$query .= "instance_TenantID, instance_ObjectID, instance_GlobalIdentifier";
			$query .= ") VALUES (";
			$query .= ":instance_TenantID, :instance_ObjectID, :instance_GlobalIdentifier";
			$query .= ")";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":instance_TenantID" => $tenant->ID,
				":instance_ObjectID" => 1,
				":instance_GlobalIdentifier" => $globalIdentifier
			));
			
			foreach ($parentObjects as $obj1)
			{
				$obj->AddParentObject($obj1);
			}
			
			return $obj;
		}
		
		/**
		 * Creates an instance of this Objectify object with the specified properties.
		 * @param TenantObjectInstancePropertyValue[] $properties
		 * @param string $globalIdentifier The global identifier for this instance.
		 * @return Instance
		 */
		public function CreateInstance($properties = null, $globalIdentifier = null)
		{
			$inst = new Instance($this);
			if ($globalIdentifier == null)
			{
				// $globalIdentifier = \uuid_create(\UUID_TYPE_RANDOM);
				Objectify::Log("Created a new instance of an object without a predefined global identifier", array
				(
					"Object" => $this->Name //,
					// "Global Identifier" => $globalIdentifier
				));
			}
			$inst->GlobalIdentifier = $globalIdentifier;
			$inst->Update();
			if (is_array($properties))
			{
				trigger_error("XquizIT: calling CreateInstance() with properties for '" . $globalIdentifier . "'");
			}
			return $inst;
		}
		
		public function GetNextInstanceID()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT MAX(instance_ID) + 1 FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Instances WHERE instance_ObjectID = :instance_ObjectID AND instance_TenantID = :instance_TenantID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":instance_ObjectID" => $this->ID,
				":instance_TenantID" => $this->Tenant->ID
			));
			
			$count = $statement->rowCount();
			if ($count == 0)
			{
				return 1;
			}
			else
			{
				$values = $statement->fetch(PDO::FETCH_NUM);
				$instanceID = $values[0];
				
				return $instanceID + 1;
			}
		}
		
		public function CreateInstanceProperty($propertyName, $dataType, $defaultValue = null, $isRequired = false)
		{
			$pdo = DataSystem::GetPDO();
			
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstanceProperties "
			. "(property_TenantID, property_ObjectID, property_Name, property_DataTypeID, property_DefaultValue, property_IsRequired)"
			. " VALUES "
			. "(:property_TenantID, :property_ObjectID, :property_Name, :property_DataTypeID, :property_DefaultValue, :property_IsRequired)";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":property_TenantID" => $this->Tenant->ID,
				":property_ObjectID" => $this->ID,
				":property_Name" => $propertyName,
				":property_DataTypeID" => $dataType->ID,
				":property_DefaultValue" => $dataType->Encode($defaultValue),
				":property_IsRequired" => $isRequired
			));
			
			if ($result === false)
			{
				$ei = $statement->errorInfo();
				Objectify::Log("Database error when trying to create an instance property for the specified tenant object.", array
				(
					"DatabaseError" => $ei[2] . " (" . $ei[1] . ")",
					"Query" => $query
				));
				return false;
			}
		}
		
		public function CreateProperty($propertyName, $dataType, $defaultValue = null, $isRequired = false)
		{
			$pdo = DataSystem::GetPDO();
				
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectProperties "
			. "(property_TenantID, property_ObjectID, property_Name, property_DataTypeID, property_DefaultValue, property_IsRequired)"
			. " VALUES "
			. "(:property_TenantID, :property_ObjectID, :property_Name, :property_DataTypeID, :property_DefaultValue, :property_IsRequired)";
				
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":property_TenantID" => $this->Tenant->ID,
				":property_ObjectID" => $this->ID,
				":property_Name" => $propertyName,
				":property_DataTypeID" => $dataType->ID,
				":property_DefaultValue" => $dataType->Encode($defaultValue),
				":property_IsRequired" => $isRequired
			));
		
			if ($result === false)
			{
				$ei = $statement->errorInfo();
				Objectify::Log("Database error when trying to create a static property for the specified tenant object.", array
				(
					"DatabaseError" => $ei[2] . " (" . $ei[1] . ")",
					"Query" => $query
				));
				return false;
			}
		}
		
		/**
		 * Builds a "Get Properties Query"
		 * @param unknown $query
		 * @param unknown $paramz
		 * @param TenantObject $parentObject
		 */
		private static function Build_Get_Properties_Query(&$query, &$paramz, $parentObject, $prefix)
		{
			$parentObjects = $parentObject->GetParentObjects();
			$parentObjectCount = count($parentObjects);
			for ($i = 0; $i < $parentObjectCount; $i++)
			{
				$query .= " OR " . $prefix . "ObjectID = :" . $prefix . "ObjectID" . $parentObjects[$i]->ID;
				$paramz[":" . $prefix . "ObjectID" . $parentObjects[$i]->ID] = $parentObjects[$i]->ID;
				TenantObject::Build_Get_Properties_Query($query, $paramz, $parentObjects[$i], $prefix);
			}
		}
		
		/**
		 * Recursively adds subclasses to a query
		 * @param unknown $query
		 * @param unknown $paramz
		 * @param TenantObject $parentObject
		 */
		private static function Build_Subclass_Query(&$query, &$paramz, $parentObject, $prefix)
		{
			$parentObjects = $parentObject->GetChildObjects();
			$parentObjectCount = count($parentObjects);
			for ($i = 0; $i < $parentObjectCount; $i++)
			{
				$query .= " OR " . $prefix . "ObjectID = :" . $prefix . "ObjectID" . $parentObjects[$i]->ID;
				$paramz[":" . $prefix . "ObjectID" . $parentObjects[$i]->ID] = $parentObjects[$i]->ID;
				TenantObject::Build_Subclass_Query($query, $paramz, $parentObjects[$i], $prefix);
			}
		}
		
		public function CountInstances($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT COUNT(instance_ID) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Instances WHERE instance_ObjectID = :instance_ObjectID";
			$statement = $pdo->prepare($query);
			$statement->execute(array
			(
				":instance_ObjectID" => $this->ID
			));
			
			if ($result === false) return 0;
			$count = $statement->rowCount();
			if ($count == 0) return 0;
			
			$values = $statement->fetch(PDO::FETCH_NUM);
			return $values[0];
		}
		
		public function GetThisInstance()
		{
			return Instance::GetByGlobalIdentifier($this->GlobalIdentifier);
		}
		
		public function GetAttribute($name)
		{
			$instatt_Name = Instance::GetByGlobalIdentifier("{9153A637-992E-4712-ADF2-B03F0D9EDEA6}");
			$insts = $this->GetAttributes();
			foreach ($insts as $inst) {
				if ($inst->GetAttributeValue($instatt_Name) == $name) {
					return $inst;
				}
			}
			return null;
		}
		public function GetAttributes()
		{
			$instrel_Tenant_has_Attribute = Instance::GetByGlobalIdentifier("{DECBB61A-2C6C-4BC8-9042-0B5B701E08DE}");
			$rels = Relationship::GetBySourceInstance($this->GetThisInstance(), $instrel_Tenant_has_Attribute);
			if (count($rels) > 0)
			{
				$rel = $rels[0];
				$insts = $rel->GetDestinationInstances();
			}
			else
			{
				$insts = array();
			}
			/*
			$parentObjects = $this->GetParentObjects();
			foreach ($parentObjects as $pobj)
			{
				$insts2 = $pobj->GetAttributes();
				foreach ($insts2 as $inst)
				{
					$insts[] = $inst;
				}
			}
			*/
			return $insts;
		}
		
		/**
		 * 
		 * @param unknown $parameters
		 * @return Instance[]
		 */
		public function GetInstanceUsingAttributes($parameters)
		{
			if (!is_array($parameters))
			{
				Objectify::Log("No parameters were specified by which to extract a single instance of the object.", array
				(
					"Object" => $this->Name
				));
				return null;
			}
			
			$insts = $this->GetInstances();
			$atts = $this->GetAttributes();
			
			$retval = array();
			// TODO: figure out what the @#$! this is
			foreach ($atts as $att)
			{
				foreach ($parameters as $parm) {
					if ($att->GetAttributeValue("Name") == $parm->Property) {
						foreach ($insts as $inst) {	
							if ($inst->GetAttributeValue($att) == $parm->Value) {
								$retval[] = $inst;
							}
						}
					}
				}
			}
			
			return $retval;
		}
		
		/**
		 * Gets all instances of the current TenantObject.
		 * @return Instance[]
		 */
		public function GetInstances()
		{
			// don't return instances for Class object since... you're gonna have a bad time
			if ($this->ID == 1) return array();
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Instances WHERE instance_ObjectID = :instance_ObjectID AND instance_TenantID = :instance_TenantID";
			$paramz = array
			(
				":instance_ObjectID" => $this->ID,
				":instance_TenantID" => $this->Tenant->ID
			);
			
			TenantObject::Build_Subclass_Query($query, $paramz, $this, "instance_");
			$statement = $pdo->prepare($query);
			$result = $statement->execute($paramz);
			$retval = array();
			
			if ($result === false)
			{
				$ei = $statement->errorInfo();
				Objectify::Log("Database error when trying to obtain an instance of an object on the tenant.", array
				(
					"DatabaseError" => $ei[2] . " (" . $ei[1] . ")",
					"Query" => $query
				));
				return $retval;
			}
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = Instance::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function GetInstanceByInstanceID($instanceID)
		{
			$iDs = stripos($instanceID, "$");
			if ($iDs === null) return Instance::GetByID($instanceID);
			$instanceIDParts = explode("$", $instanceID);
			if ($instanceIDParts[0] != $this->ID) return null;
			return Instance::GetByID($instanceIDParts[1]);
		}

		public function GetInstanceByGlobalIdentifier($globalIdentifier, $suppressComplaints = false)
		{
			$globalIdentifier = Objectify::SanitizeGlobalIdentifier($globalIdentifier);
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Instances WHERE instance_GlobalIdentifier = :instance_GlobalIdentifier";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":instance_GlobalIdentifier" => $globalIdentifier
			));
			$retval = null;
				
			if ($result === false)
			{
				$ei = $statement->errorInfo();
				Objectify::Log("Database error when trying to obtain an instance of an object on the tenant.", array
				(
					"DatabaseError" => $ei[2] . " (" . $ei[1] . ")",
					"Query" => $query,
					"Instance Global Identifier" => $globalIdentifier
				));
				return $retval;
			}
			
			$count = $statement->rowCount();
			if ($count == 0)
			{
				if (!$suppressComplaints)
				{
					Objectify::Log("An instance with the specified Global Identifier was not found.", array
					(
						"Query" => $query,
						"Instance Global Identifier" => $globalIdentifier
					));
				}
				return $retval;
			}
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			$retval = Instance::GetByAssoc($values);
			return $retval;
		}
		
		public function ToString()
		{
			$instObject = Instance::GetByGlobalIdentifier($this->GlobalIdentifier);
			if ($instObject == null) return "no inst for Object '" . $this->Name . "'";
			return $instObject->ToString();
		}
	}
?>