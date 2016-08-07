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
	use Objectify\Objects\Tenant;
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\Instance;
	use Objectify\Objects\TenantObjectInstancePropertyValue;
	use Objectify\Objects\MultipleInstanceProperty;
	use Objectify\Objects\SingleInstanceProperty;
	use Objectify\Objects\Relationship;
	use Objectify\Objects\KnownRelationships;
	
	use UniversalEditor\ObjectModels\Markup\MarkupTagElement;
use Objectify\Objects\KnownAttributes;
		
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
		
		private function LoadPostXQJS($filename)
		{
			$retval = array();
			$filedatastr = file_get_contents($filename);
			$filedata = json_decode($filedatastr);
			
			$inst_Class_has_sub_Class = Instance::GetByGlobalIdentifier("{C14BC80D-879C-4E6F-9123-E8DFB13F4666}");
			$inst_Class_has_super_Class = Instance::GetByGlobalIdentifier("{100F0308-855D-4EC5-99FA-D8976CA20053}");
			
			$objLanguageString = TenantObject::GetByName("TranslatableTextConstantValue");
			$objTranslatableTextConstant = TenantObject::GetByName("TranslatableTextConstant");
			
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
									$instInverseRelationship = null;
									if (isset($rel->InverseRelationshipID))
									{
										$instInverseRelationship = Instance::GetByGlobalIdentifier($rel->InverseRelationshipID);
									}
									else
									{
										$relSibling = $instRelationship->GetRelationship(KnownRelationships::get___Relationship__has_sibling__Relationship());
										if ($relSibling != null)
										{
											$instInverseRelationship = $relSibling->GetDestinationInstance();
										}
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
					}
				}
			}
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
					
					// TODO: don't add parent objects before the "has sub Class" and "has super Class" relationships
					//       are created to handle parent objects
					// parent objects can be handled simply by inheritance
					
					if (isset($objdef->ParentObjects))
					{
						foreach ($objdef->ParentObjects as $parentObjDef)
						{
							$parentObject = null;
							if (isset($parentObjDef->ID))
							{
								$id = Objectify::SanitizeGlobalIdentifier($parentObjDef->ID);
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
					
					$id = Objectify::SanitizeGlobalIdentifier($id);
					
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
							
							$globalIdentifier = Objectify::SanitizeGlobalIdentifier($attInstanceGlobalID->Value);
							
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
								$id = Objectify::SanitizeGlobalIdentifier($validObject->ID);
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
						$id = Objectify::SanitizeGlobalIdentifier($propDef->Value->Instance);
						$instance = Instance::GetByGlobalIdentifier($id);
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
							$id = Objectify::SanitizeGlobalIdentifier($instId);
							$instances[] = Instance::GetByGlobalIdentifier($id);
						}
					}
					
					$value = new MultipleInstanceProperty($instances, $validObjects);
					break;
				}
				case "ObjectReference":
				{
					$value = TenantObject::GetByName($propDef->Value->Name);
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
								$instances[] = Instance::GetByGlobalIdentifier($attID->Value);
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
							$instance = Instance::GetByGlobalIdentifier($attID->Value);
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
		
		/**
		 * Creates a default tenant with the specified tenant name.
		 * @param string $tenantName The name (URL) of the tenant to create.
		 * @return Instance the instance of the created Tenant
		 */
		private function CreateDefaultTenant($tenantName)
		{
			$objTenant = TenantObject::GetByName("Tenant");
			
			$instTenant = $objTenant->GetInstanceByGlobalIdentifier("{F2C9D4A9-9EFB-4263-84DB-66A9DA65AD00}");
			$instAttributeName = Instance::GetByGlobalIdentifier("{9153A637-992E-4712-ADF2-B03F0D9EDEA6}");
			$instTenant->SetAttributeValue($instAttributeName, $tenantName);
			
			return $instTenant;
		}
		
		private function CreateDefaultUser($username, $passwordHash, $passwordSalt, $displayName)
		{
			$objUser = TenantObject::GetByName("User");
			$objSecurityGroup = TenantObject::GetByName("SecurityGroup");
			
			$instSecurityGroup_SystemAdministrator = Instance::GetByGlobalIdentifier("{0E57B7A3-FE6D-4B40-843B-F20580441242}");
			
			$objLanguage = TenantObject::GetByName("Language");
			$objLanguageString = TenantObject::GetByName("TranslatableTextConstantValue");
			$objTranslatableTextConstant = TenantObject::GetByName("TranslatableTextConstant");
			
			$instLangs = $objLanguage->GetInstances();
			$lang = $instLangs[0];
			
			$instEnglish_SystemAdministrator = $objLanguageString->CreateInstance();
			$instEnglish_SystemAdministrator->SetAttributeValue(KnownAttributes::get___Text___Value(), $displayName);
			
			Relationship::Create(KnownRelationships::get___Translatable_Text_Constant_Value__has__Language(), $instEnglish_SystemAdministrator, array($lang));
			Relationship::Create(KnownRelationships::get___Language__for__Translatable_Text_Constant_Value(), $lang, array($instEnglish_SystemAdministrator));
			
			$instTTC_SystemAdministrator = $objTranslatableTextConstant->CreateInstance();
			Relationship::Create(KnownRelationships::get___Translatable_Text_Constant__has__Translatable_Text_Constant_Value(), $instTTC_SystemAdministrator, array($instEnglish_SystemAdministrator));
			
			$instUser = $objUser->CreateInstance();
			
			Relationship::Create(KnownRelationships::get___User__has_display_name__Translatable_Text_Constant(), $instUser, array($instTTC_SystemAdministrator));
			Relationship::Create(KnownRelationships::get___Translatable_Text_Constant__display_name_for__User(), $instTTC_SystemAdministrator, array($instUser));
			
			$instAttribute_UserName = Instance::GetByGlobalIdentifier("{960FAF02-5C59-40F7-91A7-20012A99D9ED}");
			$instAttribute_PasswordHash = Instance::GetByGlobalIdentifier("{F377FC29-4DF1-4AFB-9643-4191F37A00A9}");
			$instAttribute_PasswordSalt = Instance::GetByGlobalIdentifier("{8C5A99BC-40ED-4FA2-B23F-F373C1F3F4BE}");
			$instAttribute_Global = Instance::GetByGlobalIdentifier("{40A05D59-4F7B-46BF-9352-67FC3E5FB2C1}");
			
			$instUser->SetAttributeValue($instAttribute_UserName, $username);
			$instUser->SetAttributeValue($instAttribute_PasswordHash, $passwordHash);
			$instUser->SetAttributeValue($instAttribute_PasswordSalt, $passwordSalt);
			$instUser->SetAttributeValue($instAttribute_Global, true);
			
			Relationship::Create(KnownRelationships::get___User__has__Security_Group(), $instUser, array($instSecurityGroup_SystemAdministrator));
			Relationship::Create(KnownRelationships::get___Security_Group__for__User(), $instSecurityGroup_SystemAdministrator, array($instUser));
			
			return $instUser;
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
					
					// First create the initial tenant
					$tenant = Tenant::Create("default");
					
					$_SESSION["CurrentTenantID"] = $tenant->ID;
					
					// Create the tenanted objects in directories required before anything else takes place
					$tenantObjectFileNames = glob(dirname(__FILE__) . "/../TenantObjects/*/*.xqjs");
					foreach ($tenantObjectFileNames as $tenantObjectFileName)
					{
						$objs = $this->LoadXQJS($tenantObjectFileName);
					}
					$tenantObjectFileNames = glob(dirname(__FILE__) . "/../TenantObjects/*/*.xqml");
					foreach ($tenantObjectFileNames as $tenantObjectFileName)
					{
						$objs = $this->LoadXQML($tenantObjectFileName);
					}
					$tenantObjectFileNames = glob(dirname(__FILE__) . "/../TenantObjects/*/*.inc.php");
					foreach ($tenantObjectFileNames as $tenantObjectFileName)
					{
						require($tenantObjectFileName);
					}
					
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
					
					$instDefaultUser = $this->CreateDefaultUser($Administrator_UserName, $Administrator_PasswordHash, $Administrator_PasswordSalt, "System Administrator");
					$instDefaultTenant = $this->CreateDefaultTenant("default");
					
					// $this->CreateDefaultSecurityPrivilegesAndGroups();
					
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
					}
					
					$tenantObjectFileNames = glob(dirname(__FILE__) . "/../TenantObjects/*/*.xqjs");
					foreach ($tenantObjectFileNames as $tenantObjectFileName)
					{
						$objs = $this->LoadPostXQJS($tenantObjectFileName);
					}
					
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
					
					echo("{");
					echo("\"Result\": \"Success\"");
					echo("}");
				}
				
				$e->Cancel = true;
			}
		}
	}
?>