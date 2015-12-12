<?php
	namespace Objectify\Manager\Pages;
	
	use Phast\CancelEventArgs;
	use Phast\Parser\PhastPage;
	use Phast\Data\DataSystem;
	use Phast\System;
	use Phast\RandomStringGenerator;
	use Phast\RandomStringGeneratorCharacterSets;
	
	use UniversalEditor\ObjectModels\Markup\XMLParser;

	use Objectify\Objects\Objectify;
	use Objectify\Objects\DataType;
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstance;
	use Objectify\Objects\TenantObjectInstancePropertyValue;
	use Objectify\Objects\MultipleInstanceProperty;
	use Objectify\Objects\SingleInstanceProperty;
	use UniversalEditor\ObjectModels\Markup\MarkupTagElement;
	
	class SetupPage extends PhastPage
	{
		private function CreateDefaultSecurityPrivilegesAndGroups()
		{
			$objSecurityPrivilege = TenantObject::GetByName("SecurityPrivilege");
			
			$instCreateTenant = $objSecurityPrivilege->CreateInstance();
			
			$objSecurityGroup = TenantObject::GetByName("SecurityGroup");
			
			$instTenantManager = $objSecurityGroup->CreateInstance(array
			(
				new TenantObjectInstancePropertyValue
				(
					$objSecurityGroup->GetProperty("SecurityPrivileges"), array
					(
						$instCreateTenant
					)
				)
			)); // Tenant Manager
		}
		
		private function SanitizeGlobalIdentifier($id)
		{
			if ($id != null)
			{
				$id = str_replace("{", "", $id);
				$id = str_replace("}", "", $id);
				$id = str_replace("-", "", $id);
			}
			return $id;
		}
		
		/**
		 * Creates TenantObject(s) from an XquiIT JavaScript Object Notation (JSON) file and returns the array of all TenantObjects that were created.
		 * @param string $filename The file name to parse as an XquizIT object definition.
		 * @return TenantObject[]|false
		 */
		private function LoadXQJS($filename)
		{
			$retval = array();
			$filedatastr = file_get_contents($filename);
			$filedata = json_decode($filedatastr);
			
			if ($filedata->Objects != null)
			{
				foreach ($filedata->Objects as $objdef)
				{
					$id = null;
					if (isset($objdef->ID)) $id = $objdef->ID;
					
					$id = $this->SanitizeGlobalIdentifier($id);
					
					$obj = TenantObject::GetByName($objdef->Name);
					if ($obj == null)
					{
						$obj = TenantObject::Create($objdef->Name, null, $id);
						if ($obj == null)
						{
							trigger_error("XquizIT: create object failed for '" . $objdef->Name . "'");
							continue;
						}
						$retval[] = $obj;
					}
					
					if (isset($objdef->ParentObjects))
					{
						foreach ($objdef->ParentObjects as $parentObjDef)
						{
							$parentObject = null;
							if (isset($parentObjDef->ID))
							{
								$id = $this->SanitizeGlobalIdentifier($parentObjDef->ID);
								$parentObject = TenantObject::GetByGlobalIdentifier($id);
							}
							else if (isset($parentObjDef->Name))
							{
								$parentObject = TenantObject::GetByName($parentObjDef->Name);
							}
							if ($parentObject != null) $obj->AddParentObject($parentObject);
						}
					}
					
					if (isset($objdef->Properties))
					{
						foreach ($objdef->Properties as $propDef)
						{
							if (!isset($propDef->Name)) continue;
							
							$property = $obj->GetProperty($propDef->Name);
							$dataTypeName = null;
							if ($property != null) $dataTypeName = $property->DataType->Name;
							
							$value = $this->XquizitLoadPropertyValueFromJSON($propDef, $obj, false, $filename, $dataTypeName);
							
							if ($property == null)
							{
								if ($propDef->DataTypeName == null)
								{
									Objectify::Log("XquizIT attempted to create a new property without a data type name", array
									(
										"Property Name" => $propDef->Name,
										"Object Name" => $obj->Name,
										"XquizIT Source File Name" => $filename
									));
									continue;
								}
								$property = $obj->CreateProperty($propDef->Name, DataType::GetByName($propDef->DataTypeName), $value);
							}
							else
							{
								$obj->SetPropertyValue($property, $value);
							}
						}
					}
					
					if (isset($objdef->InstanceProperties))
					{
						foreach ($objdef->InstanceProperties as $propDef)
						{
							if (!isset($propDef->Name)) continue;
							
							$dataTypeName = null;
							if ($obj->HasInstanceProperty($propDef->Name))
							{
								$dataTypeName = $obj->GetInstanceProperty($propDef->Name)->DataType->Name;
							}
							
							$value = $this->XquizitLoadPropertyValueFromJSON($propDef, $obj, false, $filename, $dataTypeName);
							
							if (!$obj->HasInstanceProperty($propDef->Name))
							{
								if ($propDef->DataTypeName == null)
								{
									Objectify::Log("XquizIT attempted to create a new instance property without a data type name", array
									(
										"Property Name" => $propDef->Name,
										"Object Name" => $obj->Name,
										"XquizIT Source File Name" => $filename
									));
									continue;
								}
								$property = $obj->CreateInstanceProperty($propDef->Name, DataType::GetByName($propDef->DataTypeName), $value);
							}
							else
							{
								$property = $obj->GetInstanceProperty($propDef->Name, false);
							}
						}
					}
					
					if (isset($objdef->Instances))
					{
						foreach ($objdef->Instances as $instDef)
						{
							if (!isset($instDef->ID)) continue;
							
							$id = $this->SanitizeGlobalIdentifier($instDef->ID);
							$inst = $obj->GetInstanceByGlobalIdentifier($id);
							if ($inst == null)
							{
								$inst = $obj->CreateInstance(null, $id);
							}
							
							if (isset($instDef->PropertyValues))
							{
								foreach ($instDef->PropertyValues as $propValDef)
								{
									if (!isset($propValDef->Name)) continue;
									$prop = $obj->GetInstanceProperty($propValDef->Name);
									
									$value = $this->XquizitLoadPropertyValueFromJSON($propValDef, $obj, true, $filename, $prop->DataType->Name);
									$inst->SetPropertyValue($prop, $value);
								}
							}
						}
					}
				}
			}
			
			return $retval;
		}

		/**
		 * Creates TenantObject(s) from an XquizIT markup language file and returns the array of all TenantObjects that were created.
		 * @param string $filename The file name to parse as an XquizIT object definition.
		 * @return TenantObject[]|false
		 */
		private function LoadXQML($filename)
		{
			$parser = new XMLParser();
			$mom = $parser->LoadFile($filename);
			$retval = array(); // to store an array of all the objects that were created by this function
			
			$tagObjectify = $mom->GetElement("Objectify");
			if ($tagObjectify == null)
			{
				trigger_error("File does not contain a top-level tag 'Objectify'");
				return false;
			}
				
			$tagObjects = $tagObjectify->GetElement("Objects");
			if ($tagObjects != null)
			{
				$elems = $tagObjects->GetElements();
				foreach ($elems as $elem)
				{
					if ($elem->Name != "Object") continue;
					
					$attName = $elem->GetAttribute("Name");
					if ($attName == null) continue;
					
					$id = null;
					$attID = $elem->GetAttribute("ID");
					if ($attID != null) $id = $attID->Value;
					
					$id = $this->SanitizeGlobalIdentifier($id);
					
					$obj = TenantObject::GetByName($attName->Value);
					if ($obj == null)
					{
						$obj = TenantObject::Create($attName->Value, null, $id);
						if ($obj == null)
						{
							trigger_error("XquizIT: create object failed for '" . $attName->Value . "'");
							continue;
						}
		
						$retval[] = $obj;
					}
					
					$tagParentObjects = $elem->GetElement("ParentObjects");
					if ($tagParentObjects != null)
					{
						$elemParentObjects = $tagParentObjects->GetElements();
						foreach ($elemParentObjects as $elemParentObject)
						{
							/*
							 $attID = $elemParentObject->GetAttribute("ID");
							 if ($attID != null)
							 {
							 $objParent = TenantObject::GetByGlobalIdentifier($attID->Value);
							 }
							 */
								
							$attName = $elemParentObject->GetAttribute("Name");
							if ($attName != null)
							{
								$parentObject = TenantObject::GetByName($attName->Value);
								$obj->AddParentObject($parentObject);
							}
						}
					}
					
					$tagProperties = $elem->GetElement("Properties");
					if ($tagProperties != null)
					{
						$elemProperties = $tagProperties->GetElements();
						foreach ($elemProperties as $elemProperty)
						{
							/*
							 $attID = $elemParentObject->GetAttribute("ID");
							 if ($attID != null)
							 {
							 $objParent = TenantObject::GetByGlobalIdentifier($attID->Value);
							 }
							 */
							
							$attName = $elemProperty->GetAttribute("Name");
							$attDataTypeName = $elemProperty->GetAttribute("DataTypeName");
								
							if ($attName == null) continue;
							
							$property = $obj->GetProperty($attName->Value);
							$value = $this->XquizitLoadPropertyValueFromTag($elemProperty, $obj, false, $filename);
							
							if ($property == null)
							{
								if ($attDataTypeName == null)
								{
									Objectify::Log("XquizIT attempted to create a new property without a data type name", array
									(
										"Property Name" => $attName->Value,
										"Object Name" => $obj->Name,
										"XquizIT Source File Name" => $filename
									));
									continue;
								}
		
								$property = $obj->CreateProperty($attName->Value, DataType::GetByName($attDataTypeName->Value), $value);
							}
							else
							{
								$obj->SetPropertyValue($attName->Value, $value);
							}
						}
					}
					
					$tagInstanceProperties = $elem->GetElement("InstanceProperties");
					if ($tagInstanceProperties != null)
					{
						$elemProperties = $tagInstanceProperties->GetElements();
						foreach ($elemProperties as $elemProperty)
						{
							/*
							 $attID = $elemParentObject->GetAttribute("ID");
							 if ($attID != null)
							 {
							 $objParent = TenantObject::GetByGlobalIdentifier($attID->Value);
							 }
							 */
							
							$attName = $elemProperty->GetAttribute("Name");
							$attDataTypeName = $elemProperty->GetAttribute("DataTypeName");
								
							if ($attName == null) continue;

							$value = $this->XquizitLoadPropertyValueFromTag($elemProperty, $obj, true, $filename);
							if ($obj->HasInstanceProperty($attName->Value))
							{
								$property = $obj->GetInstanceProperty($attName->Value, false);
							}
							else
							{
								if ($attDataTypeName == null)
								{
									Objectify::Log("XquizIT attempted to create a new property without a data type name", array
									(
										"Property Name" => $attName->Value,
										"Object Name" => $obj->Name,
										"XquizIT Source File Name" => $filename
									));
									continue;
								}
								$property = $obj->CreateInstanceProperty($attName->Value, DataType::GetByName($attDataTypeName->Value), $value);
							}
						}
					}
					
					$tagInstances = $elem->GetElement("Instances");
					if ($tagInstances != null)
					{
						$elemInstances = $tagInstances->GetElements();
						foreach ($elemInstances as $elemInstance)
						{
							/*
							 $attID = $elemParentObject->GetAttribute("ID");
							 if ($attID != null)
							 {
							 $objParent = TenantObject::GetByGlobalIdentifier($attID->Value);
							 }
							 */
							
							$attInstanceGlobalID = $elemInstance->GetAttribute("ID");
							if ($attInstanceGlobalID == null) continue;
							
							$globalIdentifier = $this->SanitizeGlobalIdentifier($attInstanceGlobalID->Value);
							
							$inst = $obj->GetInstanceByGlobalIdentifier($globalIdentifier);
							if ($inst == null)
							{
								$inst = $obj->CreateInstance(null, $globalIdentifier);
							}
							
							$elemInstanceProperties = $elemInstance->GetElement("PropertyValues");
							if ($elemInstanceProperties != null)
							{
								$elemInstancePropertyValues = $elemInstanceProperties->GetElements();
								foreach ($elemInstancePropertyValues as $elemInstancePropertyValue)
								{
									$attInstancePropertyName = $elemInstancePropertyValue->GetAttribute("Name");
									if ($attInstancePropertyName == null) continue;
									
									$name = $attInstancePropertyName->Value;
									$value = $this->XquizitLoadPropertyValueFromTag($elemInstancePropertyValue, $obj, true, $filename);
									$inst->SetPropertyValue($name, $value);
								}
							}
						}
					}
				}
			}
				
			return $retval;
		}
		
		private function XquizitLoadPropertyValueFromJSON($propDef, $obj, $isInstanceProperty, $filename, $dataTypeName = null)
		{
			if (!isset($propDef->Value)) return null;
			$value = null;
			
			if ($dataTypeName == null) $dataTypeName = $propDef->DataTypeName;

			$validObjects = null;
			switch ($dataTypeName)
			{
				case "SingleInstance":
				case "MultipleInstance":
				{					
					if (isset($propDef->Value->ValidObjects))
					{
						$validObjects = array();
						foreach ($propDef->Value->ValidObjects as $validObject)
						{
							if (isset($validObject->Name))
							{
								$validObjects[] = TenantObject::GetByName($validObject->Name);
							}
							else if (isset($validObject->ID))
							{
								$id = $this->SanitizeGlobalIdentifier($validObject->ID);
								$validObjects[] = TenantObject::GetByGlobalIdentifier($id);
							}
						}
					}
					else
					{
						$op = null;
						if ($isInstanceProperty)
						{
							$op = $obj->GetInstanceProperty($propDef->Name);						}
						else
						{
							$op = $obj->GetProperty($propDef->Name);
						}
						$className = get_class($op->DefaultValue);
						if ($className == "Objectify\\Objects\\MultipleInstanceProperty"
								|| $className == "Objectify\\Objects\\SingleInstanceProperty")
						{
							$validObjects = $op->DefaultValue->ValidObjects;
						}
					}
					break;
				}
			}
			
			switch ($dataTypeName)
			{
				case "SingleInstance":
				{
					$instance = null;
					
					if (isset($propDef->Value->Instance))
					{
						$id = $this->SanitizeGlobalIdentifier($propDef->Value->Instance);
						$instance = TenantObjectInstance::GetByGlobalIdentifier($id);
					}
					
					$value = new SingleInstanceProperty($instance, $validObjects);
					break;
				}
				case "MultipleInstance":
				{
					$instances = array();
					if (isset($propDef->Value->Instances))
					{
						foreach ($propDef->Value->Instances as $instId)
						{
							$id = $this->SanitizeGlobalIdentifier($instId);
							$instances[] = TenantObjectInstance::GetByGlobalIdentifier($id);
						}
					}
					
					$value = new MultipleInstanceProperty($instances, $validObjects);
					break;
				}
				default:
				{
					$value = $propDef->Value;
					break;
				}
			}
			return $value;
		}
		
		/**
		 * Loads a property value from a tag in an Xquizit Markup Language file.
		 * @param MarkupTagElement $tag
		 * @param TenantObject $obj
		 * @param boolean $isInstanceProperty
		 * @param string $filename
		 */
		private function XquizitLoadPropertyValueFromTag($tag, $obj, $isInstanceProperty, $filename)
		{
			$attPropertyName = $tag->GetAttribute("Name");
			$attPropertyValue = $tag->GetAttribute("Value");
			$value = null;
			if ($attPropertyValue != null)
			{
				$value = $attPropertyValue->Value;
			}
			else
			{
				$elemPropertyValue = $tag->GetElement(0);
				if ($elemPropertyValue == null) return null;
				
				$validObjects = null;
				
				$elemValidObjects = $elemPropertyValue->GetElement("ValidObjects");
				if ($elemValidObjects != null)
				{
					$validObjects = array();
						
					$elemValidObjectsItems = $elemValidObjects->GetElements();
					foreach ($elemValidObjectsItems as $elemValidObjectsItem)
					{
						$attName = $elemValidObjectsItem->GetAttribute("Name");
						$validObjects[] = TenantObject::GetByName($attName->Value);
					}
				}
				else 
				{
					if ($obj != null)
					{
						$prop = null;
						if ($isInstanceProperty)
						{
							$prop = $obj->GetInstanceProperty($attPropertyName->Value);
						}
						else
						{
							$prop = $obj->GetProperty($attPropertyName->Value);
						}
						
						if ($prop != null && $prop->DefaultValue != null)
						{
							$className = get_class($prop->DefaultValue);
							if ($className == "Objectify\\Objects\\MultipleInstanceProperty"
									|| $className == "Objectify\\Objects\\SingleInstanceProperty")
							{
								$validObjects = $prop->DefaultValue->ValidObjects;
							}
						}
					}
				}
				
				if ($validObjects == null)
				{
					Objectify::Log("XquizIT warning - instance property defined with no valid objects", array
					(
						"Property Name" => $attPropertyName->Value,
						"Object Name" => $obj->Name,
						"XquizIT Source File Name" => $filename,
						"Data Type Name" => $elemPropertyValue->Name
					));
				}
				
				switch ($elemPropertyValue->Name)
				{
					case "MultipleInstancePropertyValue":
					{
						$instances = null;
						
						$elemInstances = $elemPropertyValue->GetElement("Instances");
						if ($elemInstances != null)
						{
							$instances = array();
			
							$elemInstancesItems = $elemInstances->GetElements();
							foreach ($elemInstancesItems as $elemInstance)
							{
								$attID = $elemInstance->GetAttribute("ID");
								$instances[] = TenantObjectInstance::GetByGlobalIdentifier($attID->Value);
							}
						}
			
						$value = new MultipleInstanceProperty($instances, $validObjects);
						break;
					}
					case "SingleInstancePropertyValue":
					{
						$instance = null;
						
						$elemInstance = $elemPropertyValue->GetElement("Instance");
						if ($elemInstance != null)
						{
							$attID = $elemInstance->GetAttribute("ID");
							$instance = TenantObjectInstance::GetByGlobalIdentifier($attID->Value);
						}
						
						$value = new SingleInstanceProperty($instance, $validObjects);
						break;
					}
					default:
					{
						Objectify::Log("XquizIT did not know how to parse this data type", array
						(
							"Property Name" => $attPropertyName->Value,
							"Object Name" => $obj->Name,
							"XquizIT Source File Name" => $filename,
							"Data Type Name" => $elemPropertyValue->Name
						));
						break;
					}
				}
			}
			return $value;
		}
		
		private function CreateDefaultUser($username, $passwordHash)
		{
			$objUser = TenantObject::GetByName("User");
			$objSecurityGroup = TenantObject::GetByName("SecurityGroup");
			
			$instSecurityGroup_SystemAdministrator = TenantObjectInstance::GetByGlobalIdentifier("{0E57B7A3-FE6D-4B40-843B-F20580441242}");
			
			$instUser = $objUser->CreateInstance(array
			(
				new TenantObjectInstancePropertyValue
				(
					"UserName",
					$Administrator_UserName
				),
				new TenantObjectInstancePropertyValue
				(
					"PasswordHash",
					$Administrator_PasswordHash
				),
				new TenantObjectInstancePropertyValue
				(
					"PasswordSalt",
					$Administrator_PasswordSalt
				),
				new TenantObjectInstancePropertyValue
				(
					"IsGlobal",
					true
				)
			));
			
			$instUser->SetPropertyValue("SecurityGroups", new MultipleInstanceProperty(
			array
			(
				$instSecurityGroup_SystemAdministrator
			),
			array
			(
				$objSecurityGroup
			)));
		}
		
		
		public function OnInitializing(CancelEventArgs $e)
		{
			if ($e->RenderingPage->IsPostback)
			{
				$Database_TablePrefix = $_POST["Database_TablePrefix"];
				
				$Administrator_UserName = $_POST["Administrator_UserName"];
				$Administrator_Password = $_POST["Administrator_Password"];
				
				// test the database connection first
				$oldConfiguration = System::$Configuration;
				
				System::SetConfigurationValue("Database.ServerName", $_POST["Database_ServerName"]);
				System::SetConfigurationValue("Database.DatabaseName", $_POST["Database_DatabaseName"]);
				System::SetConfigurationValue("Database.UserName", $_POST["Database_UserName"]);
				System::SetConfigurationValue("Database.Password", $_POST["Database_Password"]);
				System::SetConfigurationValue("Database.TablePrefix", $_POST["Database_TablePrefix"]);
				
				$pdo = null;
				try
				{
					$pdo = DataSystem::GetPDO();
				}
				catch (\Exception $ex)
				{
					
				}
				
				if ($pdo == null)
				{
					echo("{ \"Result\": \"Failure\", \"Message\": \"Could not connect to the database\" }");
					$e->Cancel = true;
					return;
				}
				
				if (!System::SaveConfigurationFile())
				{
					echo("{ \"Result\": \"Failure\", \"Message\": \"Could not save the configuration file\" }");
					$e->Cancel = true;
					return;
				}
				
				$tables = array();
				$tableFileNames = glob(dirname(__FILE__) . "/../Tables/*.inc.php");
				foreach ($tableFileNames as $tableFileName)
				{
					require($tableFileName);
				}
				
				$errorsFound = false;
				foreach ($tables as $table)
				{
					if (!$table->Create())
					{
						$errorsFound = true;
					}
				}
				
				if ($errorsFound)
				{
					echo("{");
					echo("\"Result\": \"Failure\",");
					echo("\"Errors\": [");
					
					$count = count(DataSystem::$Errors->Items);
					
					for ($i = 0; $i < $count; $i++)
					{
						echo("{");
						echo("\"Message\": \"" . DataSystem::$Errors->Items[$i]->Query . "\"");
						echo("}");
						if ($i < $count - 1)
						{
							echo(",");
						}
					}
					echo("]");
					
					echo("}");
				}
				else
				{
					// create the initial user
					$Administrator_PasswordSalt = RandomStringGenerator::Generate(RandomStringGeneratorCharacterSets::AlphaNumericMixedCase, 32);
					$Administrator_PasswordHash = hash("sha512", $Administrator_Password . $Administrator_PasswordSalt);
					
					// create a new global (non-tenanted) instance of the User object
					// this can be set by User property IsGlobal - USE SPARINGLY!!!
					
					// Create the tenanted objects required before anything else takes place
					$tenantObjectFileNames = glob(dirname(__FILE__) . "/../TenantObjects/*.xqjs");
					foreach ($tenantObjectFileNames as $tenantObjectFileName)
					{
						$objs = $this->LoadXQJS($tenantObjectFileName);
					}
					$tenantObjectFileNames = glob(dirname(__FILE__) . "/../TenantObjects/*.xqml");
					foreach ($tenantObjectFileNames as $tenantObjectFileName)
					{
						$objs = $this->LoadXQML($tenantObjectFileName);
					}
					$tenantObjectFileNames = glob(dirname(__FILE__) . "/../TenantObjects/*.inc.php");
					foreach ($tenantObjectFileNames as $tenantObjectFileName)
					{
						require($tenantObjectFileName);
					}
					
					$this->CreateDefaultUser($Administrator_UserName, $Administrator_PasswordHash);
					
					// $this->CreateDefaultSecurityPrivilegesAndGroups();
					
					$statement = $pdo->prepare("INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "Users (user_LoginID, user_PasswordHash, user_PasswordSalt) VALUES (:user_LoginID, :user_PasswordHash, :user_PasswordSalt)");
					$result = $statement->execute(array
					(
						":user_LoginID" => $Administrator_UserName,
						":user_PasswordHash" => $Administrator_PasswordHash,
						":user_PasswordSalt" => $Administrator_PasswordSalt
					));
					
					if ($result === false)
					{
						
					}
					
					echo("{");
					echo("\"Result\": \"Success\"");
					echo("}");
				}
				
				$e->Cancel = true;
			}
		}
	}
?>