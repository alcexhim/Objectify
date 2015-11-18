<?php
	namespace Objectify\Objects;
	
	use Phast\System;
	use Phast\Enumeration;
	use Phast\Data\DataSystem;
	
	use PDO;
		
	abstract class TenantStatus extends Enumeration
	{
		const Disabled = 0;
		const Enabled = 1;
	}
	
	class Tenant
	{
		public $ID;
		public $URL;
		public $Description;
		public $Status;
		public $Type;
		public $BeginTimestamp;
		public $EndTimestamp;
		
		public function __construct()
		{
		}
		
		public function IsExpired()
		{
			$date = date_create();
			if ($this->BeginTimestamp == null)
			{
				$dateBegin = null;
			}
			else
			{
				$dateBegin = date_create($this->BeginTimestamp);
			}
			if ($this->EndTimestamp == null)
			{
				$dateEnd = null;
			}
			else
			{
				$dateEnd = date_create($this->EndTimestamp);
			}
			
			return (!(($dateBegin == null || $dateBegin <= $date) && ($dateEnd == null || $dateEnd >= $date)));
		}
		
		public static function Create($url, $description = null, $status = TenantStatus::Enabled, $beginTimestamp = null, $endTimestamp = null)
		{
			$item = new Tenant();
			$item->URL = $url;
			$item->Description = $description;
			$item->Status = $status;
			$item->BeginTimestamp = $beginTimestamp;
			$item->EndTimestamp = $endTimestamp;
			
			if ($item->Update())
			{
				return $item;
			}
			return null;
		}
		
		public static function GetByAssoc($values)
		{
			$item = new Tenant();
			$item->ID = $values["tenant_ID"];
			$item->URL = $values["tenant_URL"];
			$item->Description = $values["tenant_Description"];
			switch ($values["tenant_Status"])
			{
				case 1:
				{
					$item->Status = TenantStatus::Enabled;
					break;
				}
				case 0:
				{
					$item->Status = TenantStatus::Disabled;
					break;
				}
			}
			$item->BeginTimestamp = $values["tenant_BeginTimestamp"];
			$item->EndTimestamp = $values["tenant_EndTimestamp"];
			return $item;
		}
		public static function Get($max = null)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::GetConfigurationValue("Database.TablePrefix") . "Tenants";
			$statement = $pdo->prepare($query);
			$statement->execute();
			$count = $statement->rowCount();
			$retval = array();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = Tenant::GetByAssoc($values);
			}
			return $retval;
		}
		public static function GetByID($id)
		{
			if (!is_numeric($id)) return null;
			
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Tenants WHERE tenant_ID = :tenant_ID";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":tenant_ID" => $id
			));
			$count = $statement->rowCount();
			if ($count == 0) return null;
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return Tenant::GetByAssoc($values);
		}
		
		public static function ExistsByURL($url)
		{
			return (self::GetByURL($url) != null);
		}
		public static function GetByURL($url)
		{
			$pdo = DataSystem::GetPDO();
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "Tenants WHERE tenant_URL = :tenant_URL";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":tenant_URL" => $url
			));
			
			if ($result === false)
			{
				echo("<html><head><title>Initialization Failure</title></head><body><h1>Initialization Failure</h1><p>A fatal error occurred when attempting to initialize the Objectify runtime.  Please make sure Objectify has been installed correctly on the server.</p><p>The Objectify runtime cannot be loaded (1001). Please contact the Web site administrator to inform them of this problem.</p><hr /><h3>System information</h3><table><tr><td>Tenant:</td><td>" . $url . "</td></tr><tr><td>Server: </td><td>" . $_SERVER["HTTP_HOST"] . "</td></tr></table></body></html>");
				die();
				return null;
			}

			$count = $statement->rowCount();
			if ($count == 0) return null;
			/*
			if ($count == 0)
			{
				echo("<html><head><title>Initialization Failure</title></head><body><h1>Initialization Failure</h1><p>A fatal error occurred when attempting to initialize the Objectify runtime.  Please make sure Objectify has been installed correctly on the server.</p><p>The Objectify runtime cannot find the requested tenant (1002). Please contact the Web site administrator to inform them of this problem.</p><hr /><h3>System information</h3><table><tr><td>Tenant:</td><td>" . $url . "</td></tr><tr><td>Server: </td><td>" . $_SERVER["HTTP_HOST"] . "</td></tr></table></body></html>");
				die();
				return null;
			}
			*/
			
			$values = $statement->fetch(PDO::FETCH_ASSOC);
			return Tenant::GetByAssoc($values);
		}
		
		public static function GetCurrent()
		{
			if (System::$TenantName == "") return null;
			return Tenant::GetByURL(System::$TenantName);
		}
		
		public function Update()
		{
			$pdo = DataSystem::GetPDO();
			
			if ($this->ID != null)
			{
				$query = "UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "Tenants SET ";
				$query .= "tenant_URL = :tenant_URL, ";
				$query .= "tenant_Description = :tenant_Description, ";
				$query .= "tenant_Status = :tenant_Status, ";
				$query .= "tenant_BeginTimestamp = :tenant_BeginTimestamp, ";
				$query .= "tenant_EndTimestamp = :tenant_EndTimestamp";
				$query .= " WHERE tenant_ID = :tenant_ID";
			}
			else
			{
				$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "Tenants (tenant_URL, tenant_Description, tenant_Status, tenant_BeginTimestamp, tenant_EndTimestamp) VALUES (";
				$query .= ":tenant_URL, ";
				$query .= ":tenant_Description, ";
				$query .= ":tenant_Status, ";
				$query .= ":tenant_BeginTimestamp, ";
				$query .= ":tenant_EndTimestamp";
				$query .= ")";
			}

			$array = array
			(
				":tenant_URL" => $this->URL,
				":tenant_Description" => $this->Description,
				":tenant_Status" => ($this->Status == TenantStatus::Enabled ? "1" : "0"),
				":tenant_BeginTimestamp" => ($this->BeginTimestamp != null ? ($this->BeginTimestamp) : "NULL"),
				":tenant_EndTimestamp" => ($this->EndTimestamp != null ? ($this->EndTimestamp) : "NULL")
			);
			if ($this->ID != null) $array[":tenant_ID"] = $this->ID;
			
			$statement = $pdo->prepare($query);
			
			$result = $statement->execute($array);

			if ($result === false) return false;
			
			$error = $statement->errorInfo();
			$errorCode = $error[0];
			$errorDescription = $error[2];
			
			if ($errorCode != "00000")
			{
				trigger_error("Tenant::Update - " . $errorDescription . " [" . $errorCode . "]");
				return false;
			}
			
			if ($this->ID == null)
			{
				$this->ID = $pdo->lastInsertId();
			}
			
			return true;
		}
		
		public function Delete()
		{
			global $MySQL;
			if ($this->ID == null) return false;
			
			// Relationships should cause all associated tenant data to be deleted.
			$query = "DELETE FROM " . System::$Configuration["Database.TablePrefix"] . "Tenants WHERE tenant_ID = " . $this->ID;
			$result = $MySQL->query($query);
			if ($MySQL->errno != 0) return false;
			
			return true;
		}
		
		/// <summary>
		/// Determines if an Objectify object with the specified name exists on the current tenant.
		/// </summary>
		public function HasObject($name)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjects WHERE (object_TenantID IS NULL OR object_TenantID = " . $this->ID . ") AND object_Name = '" . $MySQL->real_escape_string($name) . "'";
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			return ($count != 0);
		}
		
		/// <summary>
		/// Gets an Objectify object from the current tenant.
		/// </summary>
		public function GetObject($name)
		{
			global $MySQL;
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantObjects WHERE (object_TenantID IS NULL OR object_TenantID = " . $this->ID . ") AND object_Name = '" . $MySQL->real_escape_string($name) . "'";
			
			$result = $MySQL->query($query);
			$count = $result->num_rows;
			if ($count == 0)
			{
				Objectify::Log("No object with the specified name was found.", array
				(
					"Tenant" => $this->URL,
					"Object" => $name
				));
				return null;
			}
			$values = $result->fetch_assoc();
			$object = TenantObject::GetByAssoc($values);
			return $object;
		}
		
		public function CreateObject($name, $titles = null, $descriptions = null, $properties = null, $parentObject = null, $instances = null)
		{
			global $MySQL;
			if ($titles == null) $titles = array($name);
			if ($descriptions == null) $descriptions = array();
			if ($properties == null) $properties = array();
			if ($instances == null) $instances = array();
			
			// do not create the object if the object with the same name already exists
			if ($this->HasObject($name))
			{
				$bt = debug_backtrace();
				trigger_error("Object '" . $name . "' already exists on tenant '" . $this->URL . "' in " . $bt[0]["file"] . "::" . $bt[0]["function"] . " on line " . $bt[0]["line"] . "; ", E_USER_WARNING);
				return $this->GetObject($name);
			}
			
			$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "TenantObjects (object_TenantID, object_ParentObjectID, object_Name) VALUES (";
			$query .= $this->ID . ", ";
			$query .= ($parentObject == null ? "NULL" : $parentObject->ID) . ", ";
			$query .= "'" . $MySQL->real_escape_string($name) . "', ";
			$query .= ")";
			
			$result = $MySQL->query($query);
			if ($result === false) return false;
			
			$id = $MySQL->insert_id;
			$object = TenantObject::GetByID($id);
			
			$object->SetTitles($titles);
			$object->SetDescriptions($descriptions);
			
			foreach ($properties as $property)
			{
				$object->CreateInstanceProperty($property);
			}
			
			foreach ($instances as $instance)
			{
				$object->CreateInstance($instance);
			}
			
			return $object;
		}
		
		public function GetProperties()
		{
			$pdo = DataSystem::GetPDO();
			
			$retval = array();
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantProperties WHERE (property_TenantID = :property_TenantID OR property_TenantID IS NULL)";
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":property_TenantID" => $this->ID
			));
			
			if ($result === false) return $retval;
			
			$count = $statement->rowCount();
			for ($i = 0; $i < $count; $i++)
			{
				$values = $statement->fetch(PDO::FETCH_ASSOC);
				$retval[] = TenantProperty::GetByAssoc($values);
			}
			return $retval;
		}
		
		public function CreateProperty($property)
		{
			return TenantProperty::Create($property, $this);
		}
		
		public function GetProperty($propertyName)
		{
			global $MySQL;
			
			$retval = array();
			$query = "SELECT * FROM " . System::$Configuration["Database.TablePrefix"] . "TenantProperties WHERE (property_TenantID = " . $this->ID . " OR property_TenantID IS NULL) AND property_Name = '" . $MySQL->real_escape_string($propertyName) . "'";
			$result = $MySQL->query($query);
			if ($result === false)
			{
				Objectify::Log("Database error when trying to obtain a reference to a property on the tenant.", array
				(
					"DatabaseError" => $MySQL->error . " (" . $MySQL->errno . ")",
					"Query" => $query
				));
				return null;
			}
			
			$count = $result->num_rows;
			if ($count == 0) return null;
			
			$values = $result->fetch_assoc();
			return TenantProperty::GetByAssoc($values);
		}
		
		public function GetPropertyValue($property, $defaultValue = null)
		{
			global $MySQL;
			
			// if we passed in a string to this property (because it's easier) then let's get a reference to that property
			if (is_string($property))
			{
				$propname = $property;
				$property = $this->GetProperty($property);
			}
			
			$query = "SELECT (CASE WHEN (propval_Value IS NULL) THEN property_DefaultValue ELSE propval_Value END) FROM " . System::$Configuration["Database.TablePrefix"] . "TenantPropertyValues, " . System::$Configuration["Database.TablePrefix"] . "TenantProperties WHERE " . System::$Configuration["Database.TablePrefix"] . "TenantProperties.property_ID = " . $property->ID . " AND " . System::$Configuration["Database.TablePrefix"] . "TenantProperties.property_ID = " . System::$Configuration["Database.TablePrefix"] . "TenantPropertyValues.propval_PropertyID";
			
			$result = $MySQL->query($query);
			if ($result === false)
			{
				Objectify::Log("Database error when trying to look up a value of a property on the tenant.", array
				(
					"DatabaseError" => $MySQL->error . " (" . $MySQL->errno . ")",
					"Query" => $query,
					"Property" => ($property == null ? ($propname == null ? "(null)" : $propname) : (is_string($property) ? $property : $property->Name))
				));
				return null;
			}
			
			$count = $result->num_rows;
			if ($count == 0)
			{
				Objectify::Log("The property has no defined values on the specified tenant.", array
				(
					"Property" => ($property == null ? ($propname == null ? "(null)" : $propname) : (is_string($property) ? $property : $property->Name))
				), LogMessageSeverity::Warning);
				if ($defaultValue != null) return $defaultValue;
				return $property->DefaultValue;
			}
			
			$values = $result->fetch_array();
			return $property->Decode($values[0]);
		}
		public function SetPropertyValue($property, $value)
		{
			global $MySQL;
			
			// if we passed in a string to this property (because it's easier) then let's get a reference to that property
			if (is_string($property))
			{
				$propname = $property;
				$property = $this->GetProperty($property);
			}
			
			$query = "UPDATE " . System::$Configuration["Database.TablePrefix"] . "TenantPropertyValues SET propval_Value = '" . $MySQL->real_escape_string($property->Encode($value)) . "' WHERE propval_PropertyID = " . $property->ID;
			$result = $MySQL->query($query);
			if ($result === false)
			{
				Objectify::Log("Database error when trying to update the value of a property on the tenant.", array
				(
					"DatabaseError" => $MySQL->error . " (" . $MySQL->errno . ")",
					"Query" => $query,
					"Property" => ($property == null ? ($propname == null ? "(null)" : $propname) : (is_string($property) ? $property : $property->Name))
				));
			}
			return ($MySQL->errno == 0);
		}
		
		public function ToJSON()
		{
			echo("{");
			echo("\"ID\":" . $this->ID . ",");
			echo("\"URL\":\"" . $this->URL . "\",");
			echo("\"Description\":\"" . $this->Description . "\",");
			echo("\"Status\":\"" . $this->Status . "\",");
			echo("\"BeginTimestamp\":\"" . $this->BeginTimestamp . "\",");
			echo("\"EndTimestamp\":\"" . $this->EndTimestamp . "\",");
			echo("}");
		}
	}
?>