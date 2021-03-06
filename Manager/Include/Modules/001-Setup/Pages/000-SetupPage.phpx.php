<?php
	namespace Objectify\Manager\Pages;
	
	use Phast\CancelEventArgs;
	use Phast\Parser\PhastPage;
	use Phast\Data\DataSystem;
	use Phast\System;
	use Phast\RandomStringGenerator;
	use Phast\RandomStringGeneratorCharacterSets;
	
	use Objectify\Objects\KnownAttributes;
	use Objectify\Objects\KnownObjects;
	use Objectify\Objects\Objectify;
	use Objectify\Objects\Tenant;
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\Instance;
	use Objectify\Objects\Relationship;
	use Objectify\Objects\KnownRelationships;
	
	class SetupPage extends PhastPage
	{
		private $startTimestamp;
		private $endTimestamp;
		
		private function ProcessRelationshipsXQJS($instRelationship, $destinationInstances, $instObj, $instInverseRelationship = null)
		{
			if (is_array($destinationInstances))
			{
				$array = array();
				foreach ($destinationInstances as $destinstID)
				{
					$destinst = Instance::GetByGlobalIdentifier($destinstID);
					$array[] = $destinst;
					
					if ($instInverseRelationship != null)
					{
						Relationship::Create($instInverseRelationship, $destinst, array($instObj));
					}
				}
				Relationship::Create($instRelationship, $instObj, $array);
			}
		}
		
		private function LoadXQJSPost($filedata)
		{
			$retval = array();
			
			$inst_Class_has_sub_Class = Instance::GetByGlobalIdentifier("{C14BC80D-879C-4E6F-9123-E8DFB13F4666}");
			$inst_Class_has_super_Class = Instance::GetByGlobalIdentifier("{100F0308-855D-4EC5-99FA-D8976CA20053}");
			
			$objLanguageString = KnownObjects::get___Translatable_Text_Constant_Value();
			$objTranslatableTextConstant = KnownObjects::get___Translatable_Text_Constant();
			
			// Set up Relationships first
			if (isset($filedata->Relationships)) {
				if (is_array($filedata->Relationships))
				{
					foreach ($filedata->Relationships as $rel)
					{
						$instRelationship = Instance::GetByGlobalIdentifier($rel->RelationshipInstance);
						$instSource = Instance::GetByGlobalIdentifier($rel->SourceInstance);
			
						$instDests = array();
						foreach ($rel->DestinationInstances as $iid)
						{
							$instDest = Instance::GetByGlobalIdentifier($iid);
							$instDests[] = $instDest;
						}
						/*
						 Objectify::Log("Creating a new Relationship", array
						 (
						 "Relationship Instance GID" => $rel->RelationshipInstance,
						 "Source Instance GID" => $rel->SourceInstance,
						 "Relationship Instance DBID" => $instRelationship->ID,
						 "Source Instance DBID" => $instSource->ID
						 ));
						 */
						Relationship::Create($instRelationship, $instSource, $instDests);
			
						if (isset($rel->InverseRelationshipInstance))
						{
							$instInverseRelationship = Instance::GetByGlobalIdentifier($rel->InverseRelationshipInstance);
								
							foreach ($instDests as $instDest)
							{
								/*
								 Objectify::Log("Creating a new Relationship", array
								 (
								 "Relationship Instance GID" => $rel->RelationshipInstance,
								 "Source Instance GID" => $instDest->GlobalIdentifier,
								 "Relationship Instance DBID" => $instRelationship->ID,
								 "Source Instance DBID" => $instDest->ID
								 ));
								 */
								Relationship::Create($instInverseRelationship, $instDest, $instSource);
							}
						}
					}
				}
			}
			
			// Now check Objects for parent-child relationships
			if (isset($filedata->Objects)) {
				if ($filedata->Objects != null)
				{
					foreach ($filedata->Objects as $obj_data)
					{
						if (isset($obj_data->Name))
						{
							$obj = TenantObject::GetByName($obj_data->Name);
							$instObj = Instance::GetByGlobalIdentifier($obj->GlobalIdentifier);
						}
						else if (isset($obj_data->ID))
						{
							$instObj = Instance::GetByGlobalIdentifier($obj_data->ID);
						}
						
						if (isset($obj_data->ParentObjects))
						{
							foreach ($obj_data->ParentObjects as $pobj_data)
							{
								$pobj = TenantObject::GetByName($pobj_data->Name);
								$instPObj = Instance::GetByGlobalIdentifier($pobj->GlobalIdentifier);
								
								Relationship::Create($inst_Class_has_sub_Class, $instPObj, $instObj);
								Relationship::Create($inst_Class_has_super_Class, $instObj, $instPObj);
							}
						}
						

						if (isset($obj_data->Relationships))
						{
							if (is_array($obj_data->Relationships))
							{
								foreach ($obj_data->Relationships as $rel)
								{
									$instRelationship = Instance::GetByGlobalIdentifier($rel->RelationshipID);
									if ($instRelationship == null)
									{
										trigger_error("XquizIT: relationship with gid '" . $rel->RelationshipID . "' not found");
										continue;
									}
									
									$instInverseRelationship = null;
									if (isset($rel->InverseRelationshipID))
									{
										$instInverseRelationship = Instance::GetByGlobalIdentifier($rel->InverseRelationshipID);
									}
									if ($instInverseRelationship == null)
									{
										$instInverseRelationship = $instRelationship->GetRelatedInstance(KnownRelationships::get___Relationship__has_sibling__Relationship());
									}
									
									if (isset($rel->DestinationInstances))
									{
										$this->ProcessRelationshipsXQJS($instRelationship, $rel->DestinationInstances, $instObj, $instInverseRelationship);
									}
								}
							}
						}
						
						// Then check Object Instances for attribute values
						if (isset($obj_data->Instances))
						{
							if (is_array($obj_data->Instances))
							{
								$count = count($obj_data->Instances);
								for ($i = 0; $i < $count; $i++)
								{
									$pobj_data = $obj_data->Instances[$i];
									$instPObj = Instance::GetByGlobalIdentifier($pobj_data->ID);
									
									if (isset($pobj_data->Relationships))
									{
										if (is_array($pobj_data->Relationships))
										{
											foreach ($pobj_data->Relationships as $rel)
											{
												trigger_error("REL creating " . $rel->RelationshipID);
												$instRelationship = Instance::GetByGlobalIdentifier($rel->RelationshipID);
												$instInverseRelationship = null;
												if (isset($rel->InverseRelationshipID))
												{
													$instInverseRelationship = Instance::GetByGlobalIdentifier($rel->InverseRelationshipID);
												}
												if ($instInverseRelationship == null)
												{
													if ($instRelationship != null)
													{
														$instInverseRelationship = $instRelationship->GetRelatedInstance(KnownRelationships::get___Relationship__has_sibling__Relationship());
													}
												}
												
												if (isset($rel->DestinationInstances))
												{
													$this->ProcessRelationshipsXQJS($instRelationship, $rel->DestinationInstances, $instPObj, $instInverseRelationship);
												}
											}
										}
									}
									
									// Then check for translatable values (Instance)
									if (isset($pobj_data->TranslatableValues))
									{
										if (is_array($pobj_data->TranslatableValues))
										{
											foreach ($pobj_data->TranslatableValues as $transval)
											{
												$instRelationship = Instance::GetByGlobalIdentifier($transval->RelationshipID);
												$instTTC_Value = $objTranslatableTextConstant->CreateInstance();
												
												foreach ($transval->Values as $val)
												{
													$instLanguage = Instance::GetByGlobalIdentifier($val->LanguageInstanceID);
													
													$instLanguage_Value = $objLanguageString->CreateInstance();
													$instLanguage_Value->SetAttributeValue(KnownAttributes::get___Text___Value(), $val->Value);
													
													Relationship::Create(KnownRelationships::get___Translatable_Text_Constant_Value__has__Language(), $instLanguage_Value, array($instLanguage));
													Relationship::Create(KnownRelationships::get___Language__for__Translatable_Text_Constant_Value(), $instLanguage, array($instLanguage_Value));
													
													Relationship::Create(KnownRelationships::get___Translatable_Text_Constant__has__Translatable_Text_Constant_Value(), $instTTC_Value, array($instLanguage_Value));
												}
												Relationship::Create($instRelationship, $instPObj, $instTTC_Value);
											}
										}
									}
									
									// Set up attribute values (Instance)
									if (isset($pobj_data->AttributeValues))
									{
										if (is_array($pobj_data->AttributeValues))
										{
											foreach ($pobj_data->AttributeValues as $attval)
											{
												if (isset($attval->ID))
												{
													$instatt = Instance::GetByGlobalIdentifier($attval->ID);
													if ($instatt == null)
													{
														trigger_error("[FAIL] setting attribute with id '" . $attval->ID . "' on inst '" . $pobj_data->ID . "' to '" . $attval->Value . "'");
													}
													else
													{
														$value = $attval->Value;
														$instPObj->SetAttributeValue($instatt, $value);
													}
												}
											}
										}
									}
								}
							}
						}
						
						// Then check for translatable values (Object)
						if (isset($obj_data->TranslatableValues))
						{
							if (is_array($obj_data->TranslatableValues))
							{
								foreach ($obj_data->TranslatableValues as $transval)
								{
									$instRelationship = Instance::GetByGlobalIdentifier($transval->RelationshipID);
									$instTTC_Value = $objTranslatableTextConstant->CreateInstance();
									
									foreach ($transval->Values as $val)
									{
										$instLanguage = Instance::GetByGlobalIdentifier($val->LanguageInstanceID);
										
										$instLanguage_Value = $objLanguageString->CreateInstance();
										$instLanguage_Value->SetAttributeValue(KnownAttributes::get___Text___Value(), $val->Value);

										Relationship::Create(KnownRelationships::get___Translatable_Text_Constant_Value__has__Language(), $instLanguage_Value, array($instLanguage));
										Relationship::Create(KnownRelationships::get___Language__for__Translatable_Text_Constant_Value(), $instLanguage, array($instLanguage_Value));
										
										Relationship::Create(KnownRelationships::get___Translatable_Text_Constant__has__Translatable_Text_Constant_Value(), $instTTC_Value, array($instLanguage_Value));
									}
									Relationship::Create($instRelationship, $instObj, $instTTC_Value);
								}
							}
						}
						
						// Set up attribute values (Object)
						if (isset($obj_data->AttributeValues))
						{
							if (is_array($obj_data->AttributeValues))
							{
								foreach ($obj_data->AttributeValues as $attval)
								{
									if (isset($attval->ID))
									{
										$instatt = Instance::GetByGlobalIdentifier($attval->ID);
										if ($instatt == null)
										{
											trigger_error("[FAIL] setting attribute with id '" . $attval->ID . "' on inst '" . $instObj->GlobalIdentifier . "' to '" . $attval->Value . "'");
										}
										else
										{
											$value = $attval->Value;
											$instObj->SetAttributeValue($instatt, $value);
										}
									}
								}
							}
						}
					}
				}
			}
		}
		
		private function LoadXQJSFile($filename)
		{
			$filedatastr = file_get_contents($filename);
			$filedata = json_decode($filedatastr);
			return $filedata;
		}
		
		/**
		 * Creates TenantObject(s) from an XquiIT JavaScript Object Notation (JSON) file and returns the array of all TenantObjects that were created.
		 * @param string $filename The file name to parse as an XquizIT object definition.
		 * @return TenantObject[]|false
		 */
		private function LoadXQJS($filedata)
		{
			$retval = array();
			
			if ($filedata->Objects != null)
			{
				foreach ($filedata->Objects as $objdef)
				{
					$id = null;
					if (isset($objdef->ID)) $id = $objdef->ID;
					
					$id = Objectify::SanitizeGlobalIdentifier($id);
					
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
					
					if (isset($objdef->Instances))
					{
						foreach ($objdef->Instances as $instDef)
						{
							if (!isset($instDef->ID)) continue;
							
							$id = Objectify::SanitizeGlobalIdentifier($instDef->ID);
							$inst = $obj->GetInstanceByGlobalIdentifier($id, true);
							if ($inst == null)
							{
								$inst = $obj->CreateInstance(null, $id);
							}
							
							if (isset($instDef->PropertyValues))
							{
								foreach ($instDef->PropertyValues as $propValDef)
								{
									if (!isset($propValDef->Name)) continue;
									
									trigger_error("XquizIT: deprecated property value definition for '" . $propValDef->Name . "' on '" . $id . "'");
								}
							}
						}
					}
				}
			}
			
			return $retval;
		}
		
		/**
		 * Creates a default tenant with the specified tenant name.
		 * @return Instance the instance of the created Tenant
		 */
		private function CreateDefaultTenant($Administrator_UserName, $Administrator_Password)
		{
			$pdo = DataSystem::GetPDO();
			
			// First load in XQJS configuration files into memory
			$xqjsData = array();
			
			$tenantObjectFileNames = glob(dirname(__FILE__) . "/../TenantObjects/*/*.xqjs");
			foreach ($tenantObjectFileNames as $tenantObjectFileName)
			{
				$xqjsData[] = $this->LoadXQJSFile($tenantObjectFileName);
			}
			$tenantObjectFileNames = glob(dirname(__FILE__) . "/../TenantObjects/*.xqjs");
			foreach ($tenantObjectFileNames as $tenantObjectFileName)
			{
				$xqjsData[] = $this->LoadXQJSFile($tenantObjectFileName);
			}
			
			foreach ($xqjsData as $xqjsDataBlock)
			{
				if (isset($xqjsDataBlock->Tenants))
				{
					if (is_array($xqjsDataBlock->Tenants))
					{
						foreach ($xqjsDataBlock->Tenants as $tenantInfo)
						{
							// Now create the initial tenant
							$tenant = Tenant::GetByGlobalIdentifier($tenantInfo->ID);
							$parentTenant = null;
							if (isset($tenantInfo->ParentTenantID))
							{
								$parentTenant = Tenant::GetByGlobalIdentifier($tenantInfo->ParentTenantID);
							}
							if ($tenant == null) $tenant = Tenant::Create($tenantInfo->Name, $tenantInfo->ID, $parentTenant);
							$_SESSION["CurrentTenantID"] = $tenant->ID;
							
							$this->LoadXQJS($tenantInfo);
						}
					}
				}
			}
			
			/*
			$tenants = Tenant::Get();
			foreach ($tenants as $tenant)
			{
				$_SESSION["CurrentTenantID"] = $tenant->ID;
					
				$objs = TenantObject::Get();
				$instrel_Class__has_owner__User = KnownRelationships::get___Class__has_owner__User();
				$instrel_User__owner_for__Class = KnownRelationships::get___User__owner_for__Class();
				
				$instRel_Class__has__Object_Source = KnownRelationships::get___Class__has__Object_Source();
				$instRel_Object_Source__for__Class = KnownRelationships::get___Object_Source__for__Class();
				
				$instObjectSource_System = Instance::GetByGlobalIdentifier("{9547EB35-07FB-4B64-B82C-6DA1989B9165}");
				$inst_xq_environments = Instance::GetByGlobalIdentifier("{B066A54B-B160-4510-A805-436D3F90C2E6}");
					
				foreach ($objs as $obj)
				{
					$instobj = $obj->GetThisInstance();
						
					Relationship::Create($instrel_Class__has_owner__User, $instobj, array($inst_xq_environments));
					Relationship::Create($instrel_User__owner_for__Class, $inst_xq_environments, array($instobj));
						
					Relationship::Create($instRel_Class__has__Object_Source, $instobj, array($instObjectSource_System));
					Relationship::Create($instRel_Object_Source__for__Class, $instObjectSource_System, array($instobj));
						
					$attCreationDate = $obj->GetAttribute("CreationDate");
					if ($attCreationDate != null) {
						$instobj->SetAttributeValue($attCreationDate, date());
					}
						
					$dtNow = new \DateTime();
					$insts = $obj->GetInstances();
					foreach ($insts as $inst)
					{
						$inst->SetAttributeValue(KnownAttributes::get___Date___CreationDate(), $dtNow);
					}
				}
			}
			*/
			
			foreach ($xqjsData as $xqjsDataBlock)
			{
				if (isset($xqjsDataBlock->Tenants))
				{
					if (is_array($xqjsDataBlock->Tenants))
					{
						foreach ($xqjsDataBlock->Tenants as $tenantInfo)
						{
							// Now create the initial tenant
							$tenant = Tenant::GetByGlobalIdentifier($tenantInfo->ID);
							$parentTenant = null;
							if (isset($tenantInfo->ParentTenantID))
							{
								$parentTenant = Tenant::GetByGlobalIdentifier($tenantInfo->ParentTenantID);
							}
							if ($tenant == null) $tenant = Tenant::Create($tenantInfo->Name, $tenantInfo->ID, $parentTenant);
							$_SESSION["CurrentTenantID"] = $tenant->ID;
							
							$this->LoadXQJSPost($tenantInfo);
						}
					}
				}
			}
			
			$tenants = Tenant::Get();
			foreach ($tenants as $tenant)
			{
				$_SESSION["CurrentTenantID"] = $tenant->ID;
				
				// update the administrative user
				$Administrator_PasswordSalt = RandomStringGenerator::Generate(RandomStringGeneratorCharacterSets::AlphaNumericMixedCase, 32);
				$Administrator_PasswordHash = hash("sha512", $Administrator_Password . $Administrator_PasswordSalt);
				
				$instDefaultUser = Instance::GetByGlobalIdentifier("{739C26BC-740F-4CB0-BCB1-2A28FA570E7D}");
				if ($instDefaultUser != null)
				{
					$this->UpdateDefaultUser($instDefaultUser, $Administrator_UserName, $Administrator_PasswordHash, $Administrator_PasswordSalt);
				}
				
				// Set the tenant name for the newly-created tenant
				$instTenant = Instance::GetByGlobalIdentifier("{F2C9D4A9-9EFB-4263-84DB-66A9DA65AD00}");
				$instAttributeName = Instance::GetByGlobalIdentifier("{9153A637-992E-4712-ADF2-B03F0D9EDEA6}");
				$instTenant->SetAttributeValue($instAttributeName, $tenant->Name);
				
				// finally do some post-processing, such as adding attributes, etc.
				$objClasses = TenantObject::Get();
				
				$instAttribute_Name = Instance::GetByGlobalIdentifier("{9153A637-992E-4712-ADF2-B03F0D9EDEA6}");
				foreach ($objClasses as $obj)
				{
					$instThisClass = Instance::GetByGlobalIdentifier($obj->GlobalIdentifier);
					if ($instThisClass == null)
					{
						trigger_error("inst with gid " . $obj->GlobalIdentifier . " not found");
						continue;
					}
					$instThisClass->SetAttributeValue($instAttribute_Name, $obj->Name);
				}
				
				// once all attribute value updates are completed, notify that xq-environments was the
				// user that did the updating
				$query = "UPDATE " . System::GetConfigurationValue("Database.TablePrefix") . "Attributes SET "
					. "attval_UserTenantID = :attval_UserTenantID, "
					. "attval_UserObjectID = :attval_UserObjectID, "
					. "attval_UserInstanceID = :attval_UserInstanceID";
				
				$statement = $pdo->prepare($query);
				$statement->execute(array
				(
					":attval_UserTenantID" => 1,
					":attval_UserObjectID" => $inst_xq_environments->ParentObject->ID,
					":attval_UserInstanceID" => $inst_xq_environments->ID
				));
			}
			return $instTenant;
		}
		
		/**
		 * Updates the information for the specified user.
		 * @param Instance $instUser
		 * @param string $username
		 * @param string $passwordHash
		 * @param string $passwordSalt
		 */
		private function UpdateDefaultUser($instUser, $username, $passwordHash, $passwordSalt)
		{
			$instAttribute_UserName = Instance::GetByGlobalIdentifier("{960FAF02-5C59-40F7-91A7-20012A99D9ED}");
			$instAttribute_PasswordHash = Instance::GetByGlobalIdentifier("{F377FC29-4DF1-4AFB-9643-4191F37A00A9}");
			$instAttribute_PasswordSalt = Instance::GetByGlobalIdentifier("{8C5A99BC-40ED-4FA2-B23F-F373C1F3F4BE}");
			
			$instUser->SetAttributeValue($instAttribute_UserName, $username);
			$instUser->SetAttributeValue($instAttribute_PasswordHash, $passwordHash);
			$instUser->SetAttributeValue($instAttribute_PasswordSalt, $passwordSalt);
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
				
				// Create the Get_Next_Instance_ID stored procedure
				$query = "DROP FUNCTION IF EXISTS Get_Next_Instance_ID";
				$statement = $pdo->prepare($query);
				$statement->execute();
				$ei = $statement->errorInfo();
				
				$query = "CREATE FUNCTION `Get_Next_Instance_ID`(`TenantID` INT, `ObjectID` INT) RETURNS int(11) NO SQL "
					. "RETURN (SELECT CASE "
					. "WHEN MAX(instance_ID) IS NULL THEN 1 "
					. "ELSE MAX(instance_ID) + 1 "
					. "END "
					. "FROM ofx_Instances WHERE instance_TenantID = TenantID AND instance_ObjectID = ObjectID)";
				$statement = $pdo->prepare($query);
				$statement->execute();
				$ei = $statement->errorInfo();
				
				// Create the Create_Instance stored procedure
				$query = "DROP FUNCTION IF EXISTS Create_Instance";
				$statement = $pdo->prepare($query);
				$statement->execute();
				$ei = $statement->errorInfo();
				
				$query = "CREATE FUNCTION `Create_Instance`(`instance_TenantID` INT, `instance_ObjectID` INT, `instance_GlobalIdentifier` CHAR(32)) RETURNS int(11) MODIFIES SQL DATA "
					. "BEGIN "
					. "INSERT INTO ofx_Instances (instance_TenantID, instance_ObjectID, instance_ID, instance_GlobalIdentifier) VALUES (instance_TenantID, instance_ObjectID, `Get_Next_Instance_ID`(instance_TenantID, instance_ObjectID), instance_GlobalIdentifier); "
					. "RETURN `Get_Next_Instance_ID`(instance_TenantID, instance_ObjectID) - 1; "
					. "END";
				$statement = $pdo->prepare($query);
				$statement->execute();
				
				// Create the Update_Attribute stored procedure
				$query = "DROP PROCEDURE IF EXISTS `Update_Attribute`";
				$statement = $pdo->prepare($query);
				$statement->execute();
				
				$query = "CREATE PROCEDURE `Update_Attribute`(`attval_TenantID` INT, `attval_ObjectID` INT, `attval_InstanceID` INT, `attval_AttributeTenantID` INT, `attval_AttributeObjectID` INT, `attval_AttributeInstanceID` INT, `attval_UserInstanceID` INT, `attval_Value` LONGTEXT) MODIFIES SQL DATA "
					. "BEGIN "
					. "INSERT INTO ofx_Attributes ("
					. "attval_TenantID, attval_ObjectID, attval_InstanceID, attval_AttributeTenantID, attval_AttributeObjectID, attval_AttributeInstanceID, attval_EffectiveDateTime, attval_UserInstanceID, attval_Value"
					. ") VALUES ("
					. "attval_TenantID, attval_ObjectID, attval_InstanceID, attval_AttributeTenantID, attval_AttributeObjectID, attval_AttributeInstanceID, NOW(), attval_UserInstanceID, attval_Value"
					. "); END";
				$statement = $pdo->prepare($query);
				$statement->execute();
				
				// Create the Retrieve_Instance stored procedure
				$query = "DROP PROCEDURE IF EXISTS `Retrieve_Instance`";
				$statement = $pdo->prepare($query);
				$statement->execute();
				
				$query = "CREATE PROCEDURE `Retrieve_Instance`(`TenantID` INT, `ObjectID` INT, `InstanceID` INT) NO SQL "
					. "BEGIN "
					. "SELECT instance_TenantID, instance_ObjectID, instance_ID, instance_GlobalIdentifier FROM ofx_Instances WHERE (TenantID IS NULL OR instance_TenantID = TenantID) AND (ObjectID IS NULL OR instance_ObjectID = ObjectID) AND ((ObjectID IS NULL AND InstanceID IS NULL) OR (instance_ObjectID = ObjectID AND instance_ID = InstanceID)); "
					. "END";
				$statement = $pdo->prepare($query);
				$statement->execute();
				$ei = $statement->errorInfo();
				
				// Create the Retrieve_Attribute stored procedure
				$query = "DROP PROCEDURE IF EXISTS `Retrieve_Attribute`";
				$statement = $pdo->prepare($query);
				$statement->execute();
				
				// FIXME: for some reason, we can't handle Effective Date parameter in stored procedure
				// so the sproc always returns the value as of the most recent effective date
				$query = "CREATE PROCEDURE `Retrieve_Attribute`(`TenantID` INT, `ObjectID` INT, `InstanceID` INT, `AttributeTenantID` INT, `AttributeObjectID` INT, `AttributeInstanceID` INT, `EffectiveDateTime` DATETIME) NO SQL "
					. "BEGIN "
					. "SELECT attval_Value FROM ofx_Attributes WHERE attval_TenantID = TenantID AND attval_ObjectID = ObjectID AND attval_InstanceID = InstanceID AND attval_AttributeTenantID = AttributeTenantID AND attval_AttributeObjectID = AttributeObjectID AND attval_AttributeInstanceID = AttributeInstanceID ORDER BY attval_EffectiveDateTime DESC LIMIT 1; "
					. "END";
				$statement = $pdo->prepare($query);
				$statement->execute();
				$ei = $statement->errorInfo();
				trigger_error("xq-proc-create: " . $ei[2]);
				
				if ($errorsFound)
				{
					echo("{");
					echo("\"Result\": \"Failure\",");
					echo("\"Errors\": [");
					
					$count = count(DataSystem::$Errors->Items);
					
					for ($i = 0; $i < $count; $i++)
					{
						echo(json_encode(DataSystem::$Errors->Items[$i]));
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
					// create a new global (non-tenanted) instance of the User object
					// this can be set by User property IsGlobal - USE SPARINGLY!!!
					$instDefaultTenant = $this->CreateDefaultTenant($Administrator_UserName, $Administrator_Password);
					
					echo("{");
					echo("\"Result\": \"Success\"");
					echo("}");
				}
				
				$e->Cancel = true;
			}
		}
	}
?>