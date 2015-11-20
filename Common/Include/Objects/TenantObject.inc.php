<?php
	namespace Objectify\Objects;
	
	use Phast\System;
	use Phast\Data\DataSystem;
	
	use PDO;
	
	class TenantObject
	{
		public $ID;
		public $Tenant;
		public $Name;
		
		public static function GetByAssoc($values)
		{
			$item = new TenantObject();
			$item->ID = $values["object_ID"];
			$item->Tenant = Tenant::GetByID($values["object_TenantID"]);
			$item->Name = $values["object_Name"];
			return $item;
		}
		
		/**
		 * Gets all TenantObjects on the server.
		 * @param int $max
		 * @param unknown $tenant
		 * @return TenantObject[]
		 */
		public static function Get($max = null, $tenant = null)
		{
			$pdo = DataSystem::GetPDO();
			
			$retval = array();
			
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjects";
			
			$tenantID = null;
			if ($tenant != null)
			{
				$query .= " WHERE object_TenantID = :object_TenantID";
				$tenantID = $tenant->ID;
			}
			if (is_numeric($max)) $query .= " LIMIT " . $max;

			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":object_TenantID" => $tenantID
			));
			
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
		
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjects WHERE object_ID = :object_ID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":object_ID" => $id
			));
			if ($result === false) return null;
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return TenantObject::GetByAssoc($values);
		}
		
		public static function GetByName($name)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjects WHERE object_Name = :object_Name";
			$statement = $pdo->prepare($query);
			
			$result = $statement->execute(array
			(
				":object_Name" => $name
			));
			
			if ($result === false) return null;
			
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return TenantObject::GetByAssoc($values);
		}
		
		public function GetParentObjects()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectParentObjects WHERE parentobject_ObjectID = :parentobject_ObjectID";
			$statement = $pdo->prepare($query);
			
			$result = $statement->execute(array
			(
				":parentobject_ObjectID" => $this->ID
			));
			
			if ($result === false)
			{
				$ei = $statement->errorInfo();
				Objectify::Log("Database error when trying to retrieve a list of parent objects from a child object.", array
				(
					"DatabaseError" => $ei[2] . " (" . $ei[1] . ")",
					"Query" => $query,
					"Object ID" => $obj->ID
				));
				return null;
			}
			
			$count = $statement->rowCount();
			$retval = array();
			
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = TenantObject::GetByID($values["parentobject_ParentObjectID"]);
			}
			return $retval;
		}
		
		/**
		 * Adds the specified TenantObject as a parent of this TenantObject.
		 * @param TenantObject $obj
		 */
		public function AddParentObject($obj)
		{
			$pdo = DataSystem::GetPDO();
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectParentObjects (parentobject_ObjectID, parentobject_ParentObjectID) VALUES (:parentobject_ObjectID, :parentobject_ParentObjectID)";
			$statement = $pdo->prepare($query);
			
			$result = $statement->execute(array
			(
				":parentobject_ObjectID" => $this->ID,
				":parentobject_ParentObjectID" => $obj->ID
			));
			
			if ($result === false)
			{
				$ei = $statement->errorInfo();
				Objectify::Log("Database error when trying to add a parent object to a child object.", array
				(
					"DatabaseError" => $ei[2] . " (" . $ei[1] . ")",
					"Query" => $query,
					"Object ID" => $this->ID,
					"Parent Object ID" => $obj->ID
				));
				return false;
			}
			
			return true;
		}
		
		/**
		 * Creates a TenantObject.
		 * @param string $name
		 * @param TenantObject $parentObject
		 * @return TenantObject
		 */
		public static function Create($name, $parentObjects = null, $globalIdentifier = null)
		{
			$pdo = DataSystem::GetPDO();
			
			if ($parentObjects == null) $parentObjects = array();
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
			$query .= "object_Name, object_GlobalIdentifier";
			$query .= ") VALUES (";
			$query .= ":object_Name, :object_GlobalIdentifier";
			$query .= ")";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":object_Name" => $name,
				":object_GlobalIdentifier" => $globalIdentifier
			));
			
			if ($result === false) return null;
			$obj = TenantObject::GetByName($name);
			if ($obj == null) return null;
			
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
		 * @return TenantObjectInstance
		 */
		public function CreateInstance($properties, $globalIdentifier)
		{
			$inst = new TenantObjectInstance($this);
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
				$property = $this->GetProperty($property);
			}
			if ($property == null) return $defaultValue;
			
			if ($defaultValue == null) $defaultValue = $property->DefaultValue;
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT propval_Value FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectPropertyValues WHERE propval_PropertyID = :propval_PropertyID AND (propval_ObjectID IS NULL OR propval_ObjectID = :propval_ObjectID)";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":propval_PropertyID" => $property->ID,
				":propval_ObjectID" => $this->ID
			));
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
			
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectPropertyValues (propval_PropertyID, propval_ObjectID, propval_Value) VALUES (:propval_PropertyID, :propval_ObjectID, :propval_Value)";
			$query .= " ON DUPLICATE KEY UPDATE ";
			$query .= "propval_PropertyID = values(propval_PropertyID), ";
			$query .= "propval_ObjectID = values(propval_ObjectID), ";
			$query .= "propval_Value = values(propval_Value)";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":propval_PropertyID" => $property->ID,
				":propval_ObjectID" => $this->ID,
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
			. "(property_ObjectID, property_Name, property_DataTypeID, property_DefaultValue, property_IsRequired)"
			. " VALUES "
			. "(:property_ObjectID, :property_Name, :property_DataTypeID, :property_DefaultValue, :property_IsRequired)";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
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
			. "(property_ObjectID, property_Name, property_DataTypeID, property_DefaultValue, property_IsRequired)"
			. " VALUES "
			. "(:property_ObjectID, :property_Name, :property_DataTypeID, :property_DefaultValue, :property_IsRequired)";
				
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
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
		
		public function CreateMethod($name, $parameters, $codeblob, $description = null, $namespaceReferences = null)
		{
			global $MySQL;
			
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantObjectMethods (method_ObjectID, method_Name, method_Description, method_CodeBlob) VALUES (";
			$query .= $this->ID . ", ";
			$query .= "'" . $MySQL->real_escape_string($name) . "', ";
			$query .= ($description == null ? "NULL" : ("'" . $MySQL->real_escape_string($description) . "'")) . ", ";
			$query .= "'" . $MySQL->real_escape_string($codeblob) . "'";
			$query .= ")";
			$result = $MySQL->query($query);
			if ($result === false)
			{
				Objectify::Log("Database error when trying to create a static method for the specified tenant object.", array
				(
					"DatabaseError" => $MySQL->error . " (" . $MySQL->errno . ")",
					"Query" => $query
				));
				return false;
			}
			
			$method = TenantObjectMethod::GetByID($MySQL->insert_id);
			
			if (is_array($namespaceReferences))
			{
				foreach ($namespaceReferences as $ref)
				{
					$method->AddNamespaceReference($ref);
				}
			}
			return $method;
		}
		public function CreateInstanceMethod($name, $parameters, $codeblob, $namespaceReferences = null)
		{
			$pdo = DataSystem::GetPDO();
			
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstanceMethods (method_ObjectID, method_Name, method_CodeBlob) VALUES (:method_ObjectID, :method_Name, :method_CodeBlob)";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":method_ObjectID" => $this->ID,
				":method_Name" => $name,
				":method_CodeBlob" => $codeblob
			));
			
			if ($result === false)
			{
				$ei = $pdo->errorInfo();
				Objectify::Log("Database error when trying to create an instance method for the specified tenant object.", array
				(
					"DatabaseError" => $ei[2] . " (" . $ei[1] . ")",
					"Query" => $query
				));
				return false;
			}
			
			$method = TenantObjectInstanceMethod::GetByID($pdo->lastInsertId());
			
			if (is_array($namespaceReferences))
			{
				foreach ($namespaceReferences as $ref)
				{
					$method->AddNamespaceReference($ref);
				}
			}
			return $method;
		}
		
		public function GetProperty($propertyName, $searchInherited = true)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectProperties WHERE property_ObjectID = :property_ObjectID AND property_Name = :property_Name";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":property_ObjectID" => $this->ID,
				":property_Name" => $propertyName
			));
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
		public function GetProperties($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectProperties WHERE property_ObjectID = :property_ObjectID";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":property_ObjectID" => $this->ID
			));
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
			$query = "SELECT COUNT(*) FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstanceProperties WHERE property_ObjectID = :property_ObjectID AND property_Name = :property_Name";
			$statement = $pdo->prepare($query);
			
			$result = $statement->execute(array
			(
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
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstanceProperties WHERE property_ObjectID = :property_ObjectID AND property_Name = :property_Name";
			$statement = $pdo->prepare($query);
			
			$result = $statement->execute(array
			(
				":property_ObjectID" => $this->ID,
				":property_Name" => $propertyName
			));
			
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
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstanceProperties WHERE property_ObjectID = :property_ObjectID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":property_ObjectID" => $this->ID
			));
			
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
		
		public function GetMethod($name)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectMethods WHERE method_ObjectID = :method_ObjectID AND method_Name = :method_Name";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":method_ObjectID" => $this->ID,
				":method_Name" => $name
			));
			
			if ($result === false) return null;
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return TenantObjectMethod::GetByAssoc($values);
		}
		public function GetMethods($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectMethods WHERE method_ObjectID = :method_ObjectID";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":method_ObjectID" => $this->ID
			));
			
			$retval = array();
			if ($result === false) return $retval;
			
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = TenantObjectMethod::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function GetInstanceMethod($name)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstanceMethods WHERE method_ObjectID = :method_ObjectID AND method_Name = :method_Name";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":method_ObjectID" => $this->ID,
				":method_Name" => $name
			));
			if ($result === false) return null;
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			
			return TenantObjectInstanceMethod::GetByAssoc($values);
		}
		public function GetInstanceMethods($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstanceMethods WHERE method_ObjectID = :method_ObjectID";
			if (is_numeric($max)) $query .= " LIMIT " . $max;
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":method_ObjectID" => $this->ID
			));
			
			$retval = array();
			if ($result === false) return $retval;
			
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = TenantObjectInstanceMethod::GetByAssoc($values);
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
						System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstanceProperties, " .
						System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstancePropertyValues" .
						" WHERE " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances.instance_ObjectID = :instance_ObjectID";
			
			foreach ($parameters as $parm)
			{
				if (is_string($parm->Property)) $parm->Property = $this->GetInstanceProperty($parm->Property);
				
				$query .= " AND (";
				$query .= System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstancePropertyValues.propval_PropertyID = " . $parm->Property->ID . " AND ";
				$query .= System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstancePropertyValues.propval_Value = :propval_" . $parm->Property->ID . "_Value";
				$query .= ")";
			}
			
			$statement = $pdo->prepare($query);
			
			$parmz = array
			(
				":instance_ObjectID" => $this->ID
			);
			
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
				$inst = TenantObjectInstance::GetByAssoc($values);
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
		
		public function GetInstances()
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjectInstances WHERE instance_ObjectID = :instance_ObjectID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":instance_ObjectID" => $this->ID
			));
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
				$retval[] = TenantObjectInstance::GetByAssoc($values);
			}
			return $retval;
		}

		public function GetInstanceByGlobalIdentifier($globalIdentifier)
		{
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
			$retval = TenantObjectInstance::GetByAssoc($values);
			return $retval;
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
			return $this->Name;
		}
	}
?>