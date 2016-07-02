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
			$instRel_Class__has_sub_Class = Instance::GetByGlobalIdentifier("{C14BC80D-879C-4E6F-9123-E8DFB13F4666}");
			
			$instThisClass = Instance::GetByGlobalIdentifier($this->GlobalIdentifier);
			$instThatClass = Instance::GetByGlobalIdentifier($obj->GlobalIdentifier);
			
			$retval = Relationship::Create($instRel_Class__has_super_Class, $instThisClass, $instThatClass);
			if (!$retval) return false;
			
			$retval = Relationship::Create($instRel_Class__has_sub_Class, $instThatClass, $instThisClass);
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
			
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances (";
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
		public function CreateInstance($properties, $globalIdentifier = null)
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
				foreach ($properties as $instprop)
				{
					$inst->SetPropertyValue($instprop->Property, $instprop->Value);
				}
			}
			return $inst;
		}
		
		public function GetNextInstanceID()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT COUNT(instance_ID) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances WHERE instance_ObjectID = :instance_ObjectID AND instance_TenantID = :instance_TenantID";
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
		
		/**
		 * Gets the value of the specified static property on this TenantObject.
		 * @param TenantObjectProperty $property
		 * @param string|MultipleInstanceProperty|SingleInstanceProperty $defaultValue
		 * @return string|MultipleInstanceProperty|SingleInstanceProperty
		 */
		public function GetPropertyValue($property, $defaultValue = null)
		{
			if (is_string($property))
			{
				$propertyName = $property;
				$property = $this->GetProperty($property);
			}
			if ($property == null) return $defaultValue;
			
			if ($defaultValue == null) $defaultValue = $property->DefaultValue;
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT propval_Value FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectPropertyValues WHERE propval_PropertyID = :propval_PropertyID AND (propval_ObjectID IS NULL OR (propval_ObjectID = :propval_ObjectID";
			
			$paramz = array
			(
				":propval_PropertyID" => $property->ID,
				":propval_ObjectID" => $this->ID
			);
			TenantObject::Build_Get_Properties_Query($query, $paramz, $this, "propval_");
			
			$query .= ")) ORDER BY propval_ObjectID DESC";
			$statement = $pdo->prepare($query);
			$result = $statement->execute($paramz);
			
			if ($result === false) return $defaultValue;
			
			$count = $statement->rowCount();
			if ($count == 0) return $defaultValue;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			
			return $property->DataType->Decode($values["propval_Value"]);
		}
		public function SetPropertyValue($property, $value)
		{
			$pdo = DataSystem::GetPDO();
			if (is_string($property))
			{
				$property = $this->GetProperty($property);
			}
			if ($property == null) return false;
			
			// prevent subsequent get_class from complaining if $value is not actually an object 
			if (is_object($value))
			{
				// this might not look like it does anything, but it DOES... those properties get encoded
				// by the InstanceDataType into ValidObjects:Instances string...
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
			
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectPropertyValues (propval_TenantID, propval_ObjectID, propval_PropertyID, propval_Value) VALUES (:propval_TenantID, :propval_ObjectID, :propval_PropertyID, :propval_Value)";
			$query .= " ON DUPLICATE KEY UPDATE ";
			$query .= "propval_PropertyID = values(propval_PropertyID), ";
			$query .= "propval_ObjectID = values(propval_ObjectID), ";
			$query .= "propval_Value = values(propval_Value)";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":propval_TenantID" => $this->Tenant->ID,
				":propval_ObjectID" => $this->ID,
				":propval_PropertyID" => $property->ID,
				":propval_Value" => $property->DataType->Encode($value)
			));
			if ($result === false)
			{
				$ei = $statement->errorInfo();
				Objectify::Log("Database error when trying to update a property value for the specified object.", array
				(
					"DatabaseError" => $ei[2] . " (" . $ei[1] . ")",
					"Query" => $query
				));
				return false;
			}
			
			return true;
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
		
		public function GetProperty($propertyName, $searchInherited = true)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectProperties WHERE property_Name = :property_Name AND (property_ObjectID = :property_ObjectID";
			
			$paramz = array
			(
				":property_Name" => $propertyName,
				":property_ObjectID" => $this->ID
			);
			TenantObject::Build_Get_Properties_Query($query, $paramz, $this, "property_");
			
			$query .= ")";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute($paramz);
			if ($result === false)
			{
				$ei = $pdo->errorInfo();
				Objectify::Log("Database error when trying to fetch a static property for the specified tenant object.", array
				(
					"DatabaseError" => $ei[2] . " (" . $ei[1] . ")",
					"Query" => $query,
					"Property Name" => $propertyName,
					"Object ID" => $this->ID
				));
				return null;
			}
			$count = $statement->rowCount();
			if ($count == 0)
			{
				if ($searchInherited)
				{
					$inheritedObjs = $this->GetParentObjects();
					foreach ($inheritedObjs as $obj)
					{
						$prop = $obj->GetProperty($propertyName);
						if ($prop != null) return $prop;
					}
				}
				return null;
			}
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return TenantObjectProperty::GetByAssoc($values);
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
		
		public function GetProperties()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectProperties WHERE property_ObjectID = :property_ObjectID";
			
			$paramz = array
			(
				":property_ObjectID" => $this->ID
			);
			
			TenantObject::Build_Get_Properties_Query($query, $paramz, $this, "property_");
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute($paramz);
			
			$retval = array();
			
			if ($result === false) return $retval;
			
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = TenantObjectProperty::GetByAssoc($values);
			}
			return $retval;
		}
		public function HasInstanceProperty($propertyName)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT COUNT(*) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstanceProperties WHERE property_TenantID = :property_TenantID AND property_ObjectID = :property_ObjectID AND property_Name = :property_Name";
			$statement = $pdo->prepare($query);
			
			$result = $statement->execute(array
			(
				":property_TenantID" => $this->Tenant->ID,
				":property_ObjectID" => $this->ID,
				":property_Name" => $propertyName
			));
			
			if ($result === false) return false;
			$values = $statement->fetch(PDO::FETCH_NUM);
			return ($values[0] > 0);
		}
		public function GetInstanceProperty($propertyName)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstanceProperties WHERE property_TenantID = :property_TenantID AND property_Name = :property_Name AND (property_ObjectID = :property_ObjectID";
			
			$paramz = array
			(
				":property_TenantID" => $this->Tenant->ID,
				":property_ObjectID" => $this->ID,
				":property_Name" => $propertyName
			);
			
			TenantObject::Build_Get_Properties_Query($query, $paramz, $this, "property_");

			$query .= ")";
			
			$statement = $pdo->prepare($query);
			
			$result = $statement->execute($paramz);
			
			if ($result === false) return null;
			$count = $statement->rowCount();
			if ($count == 0)
			{
				Objectify::Log("Could not fetch the specified instance property on the object.", array
				(
					"Object" => $this->Name,
					"Property" => $propertyName
				));
				return null;
			}
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			
			return TenantObjectInstanceProperty::GetByAssoc($values);
		}
		public function GetInstanceProperties($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstanceProperties WHERE property_TenantID = :property_TenantID AND property_ObjectID = :property_ObjectID";
			$paramz = array
			(
				":property_TenantID" => $this->Tenant->ID,
				":property_ObjectID" => $this->ID
			);
			TenantObject::Build_Get_Properties_Query($query, $paramz, $this, "property_");
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute($paramz);
			
			$retval = array();
			if ($result === false) return $retval;
			
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = TenantObjectInstanceProperty::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function CountInstances($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT COUNT(instance_ID) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances WHERE instance_ObjectID = :instance_ObjectID";
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
		
		public function GetAttributes()
		{
			$retval = array();
			return $retval;
		}
		
		public function GetInstance($parameters)
		{
			// $defaultLanguage = $objLanguage->GetInstance(array
			// (
			// 		new TenantObjectInstancePropertyValue("Code", "en-US")
			// ));
			
			
			if (!is_array($parameters))
			{
				Objectify::Log("No parameters were specified by which to extract a single instance of the object.", array
				(
					"Object" => $this->Name,
					"Property" => $propertyName
				));
				return null;
			}
			
			$pdo = DataSystem::GetPDO();
			$query =	"SELECT " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances.* " .
						" FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances, " .
						System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstancePropertyValues" .
						" WHERE (" . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances.instance_ObjectID = :instance_ObjectID" .
						" AND " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances.instance_TenantID = :instance_TenantID)";
			
			foreach ($parameters as $parm)
			{
				if (is_string($parm->Property)) $parm->Property = $this->GetInstanceProperty($parm->Property);
				
				$query .= " AND (";
				$query .= System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstancePropertyValues.propval_PropertyID = " . $parm->Property->ID . " AND ";
				$query .= System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstancePropertyValues.propval_Value = :propval_" . $parm->Property->ID . "_Value";
				$query .= ")";
			}
			
			$parmz = array
			(
				":instance_ObjectID" => $this->ID,
				":instance_TenantID" => $this->Tenant->ID
			);
			
			// TODO: Figure out why Build_Subclass_Query takes TOO DAMN LONG!!! to execute for some objects
			$this->Build_Subclass_Query($query, $parmz, $this, "propval_");
			
			$statement = $pdo->prepare($query);
			
			foreach ($parameters as $parm)
			{
				$parmz[":propval_" . $parm->Property->ID . "_Value"] = $parm->Value;
			}
			
			$result = $statement->execute($parmz);
			
			if ($result === false)
			{
				$ei = $statement->errorInfo();
				Objectify::Log("Database error when trying to obtain an instance of an object on the tenant.", array
				(
					"DatabaseError" => $ei[2] . " (" . $ei[1] . ")",
					"Query" => $query
				));
				return null;
			}
			
			$count = $statement->rowCount();
			if ($count == 0)
			{
				$errorParms = array
				(
					"Object" => $this->Name,
					"Query" => $query
				);
				
				foreach ($parameters as $parm)
				{
					$errorParms["Specified Parameter " . $parm->Property->ID] = $parm->Value;
				}
				
				Objectify::Log("Could not obtain an instance of the object with the specified parameters.", $errorParms);
				return null;
			}
			
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$inst = Instance::GetByAssoc($values);
				$found = true;
				foreach ($parameters as $parameter)
				{
					if ($inst->GetPropertyValue($parameter->Property) != $parameter->Value)
					{
						$found = false;
						break;
					}
				}
				if ($found) return $inst;
			}
			return null;
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
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances WHERE instance_ObjectID = :instance_ObjectID AND instance_TenantID = :instance_TenantID";
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

		public function GetInstanceByGlobalIdentifier($globalIdentifier)
		{
			$globalIdentifier = Objectify::SanitizeGlobalIdentifier($globalIdentifier);
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances WHERE instance_GlobalIdentifier = :instance_GlobalIdentifier";
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
				Objectify::Log("An instance with the specified Global Identifier was not found.", array
				(
					"Query" => $query,
					"Instance Global Identifier" => $globalIdentifier
				));
				return $retval;
			}
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			$retval = Instance::GetByAssoc($values);
			return $retval;
		}
		
		public function ToString()
		{
			// return $this->Name;	// this takes 20 spins
			// without takes 23 spins, a difference of +3
			// so getting the Title property is slower, but not by much...
			
			$instObject = Instance::GetByGlobalIdentifier($this->GlobalIdentifier);
			$instRelationship_LabeledBy = Instance::GetByGlobalIdentifier("{B8BDB905-69DD-49CD-B557-0781F7EF2C50}");
			$relLabeledBy = Relationship::GetBySourceInstance($instObject, $instRelationship_LabeledBy);
			$relLabeledBy = $relLabeledBy[0];
			
			if ($relLabeledBy != null)
			{
				$insts = $relLabeledBy->GetDestinationInstances();
				
				$instRelationship_HasValue = Instance::GetByGlobalIdentifier("{F9B60C00-FF1D-438F-AC74-6EDFA8DD7324}");
				$relHasValue = Relationship::GetBySourceInstance($insts[0], $instRelationship_HasValue);
				if (count($relHasValue) > 0)
				{
					$relHasValue = $relHasValue[0];
					
					$insts = $relHasValue->GetDestinationInstances();
					
					$str = $insts[0]->ToString();
					return $str;
				}
			}
			return "";
			
			$propTitle = $this->GetPropertyValue("Title");
			if ($propTitle != null)
			{
				$insts = $propTitle->GetInstances();
				$objLanguage = TenantObject::GetByName("Language");
				
				if ($objLanguage == null)
				{
					Objectify::Log("The 'Language' object is missing! You may need to re-install Objectify.");
				}
				else
				{
					$defaultLanguage = $objLanguage->GetInstance(array
					(
						new TenantObjectInstancePropertyValue("Code", "en-US")
					));
					
					// TODO: figure out how not to loop through all the instances
					// e.g. $inst = $propTitle->GetInstance(array(new TenantObjectInstancePropertyValue("Language", ...)))
					foreach ($insts as $inst)
					{
						$propLang = $inst->GetPropertyValue("Language");
						if ($propLang == null)
						{
							Objectify::Log("The 'Language' instance property is missing! You may need to re-install Objectify.", array
							(
								"Global Identifier" => $inst->GlobalIdentifier,
								"Object" => $inst->ParentObject->Name
							));
							continue;
						}
						if ($propLang->GetInstance() == $defaultLanguage) return $inst->GetPropertyValue("Value");
					}
				}
			}
			return $this->Name;
		}
	}
?>