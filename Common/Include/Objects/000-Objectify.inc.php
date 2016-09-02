<?php
	namespace Objectify\Objects;
	
	use Phast\Enumeration;
	use Phast\System;
	
	use Phast\Data\DataSystem;
	use Phast\UUID;
	
	class LogMessageSeverity extends Enumeration
	{
		const Notice = 1;
		const Warning = 2;
		const Error = 3;
	}
	
	class Objectify
	{
		public static function HTML_FormatValue($value)
		{
			if (is_bool($value))
			{
				if ($value)
				{
					return "Yes";
				}
				else
				{
					return "No";
				}
			}
			else if (is_object($value) && get_class($value) == "DateTime")
			{
				return Objectify::HTML_FormatDate($value);
			}
			return $value;
		}
		
		/**
		 * Formats the specified DateTime in the current user's preferred format, with the HTML5 date
		 * tag formatted in its required format. Preferred format is determined by the specified format
		 * first, then by (User.has preferred Date Time Format) relationship on the currently logged-in
		 * user, or if that is not set, (Tenant.has preferred Date Time Format) relationship on the
		 * current tenant.
		 * 
		 * This should eventually be replaced by an Objectify BEM - Build Element Method.
		 * @param \DateTime $datetime
		 */
		public static function HTML_FormatDate($datetime, $format = null)
		{
			$htmlDateTimeFormat = "Y-m-d\TH:i:s.uP";
			if ($format == null) $format = "l, F j, Y H:i:s";
			
			$preferredDateTimeFormat = $format;
			
			$str = "<date datetime=\"" . $datetime->format($htmlDateTimeFormat) . "\">"
				. $datetime->format($preferredDateTimeFormat)
				. "</date>";
			return $str;
		}
		
		public static function GenerateTenantBadgeHTML($instTenant)
		{
			$relTenantType = $instTenant->GetRelationship(KnownRelationships::get___Tenant__has__Tenant_Type());
			if ($relTenantType != null)
			{
				$instTenantType = $relTenantType->GetDestinationInstance();
				$attTenantTypeBackgroundColor = $instTenantType->GetAttributeValue(KnownAttributes::get___Text___BackgroundColor());
				$attTenantTypeForegroundColor = $instTenantType->GetAttributeValue(KnownAttributes::get___Text___ForegroundColor());
				
				$attDisplayVersionInBadge = Instance::GetByGlobalIdentifier("{BE5966A4-C4CA-49A6-B504-B6E8759F392D}");
				$attVersionString = Instance::GetByGlobalIdentifier("{5D8CAF97-1E4C-495C-8C2D-1DFA26C74C13}");
				$attMADIRevision = Instance::GetByGlobalIdentifier("{BD8523CA-C003-4F7F-93F6-F4ABECDD1BBE}");
				
				$versionString = $instTenant->GetAttributeValue($attVersionString, "1.0.17.263");
				$madiRevision = $instTenant->GetAttributeValue($attMADIRevision, "15365");
				
				$val = "<div style=\"border-radius: 8px; background-color: " . $attTenantTypeBackgroundColor . "; padding: 8px; color: " . $attTenantTypeForegroundColor . "; display: inline-block;\">";
				$val .= "<strong>" . $instTenantType->ToString() . "</strong>";
				if ($instTenantType->GetAttributeValue($attDisplayVersionInBadge, false))
				{
					$val .= " - " . $versionString . " (MADI revision: " . $madiRevision . ")";
				}
				$val .= "</div>";
			
				return $val;
			}
			return "";
		}
		
		/**
		 * Gets the value of the specified Report Field as either a Text/Numeric/Date Attribute or a Single/Multiple
		 * Instance.
		 * @param Instance $instReportField
		 * @param Instance $instRow
		 * @return Instance|string|boolean|number|array|null
		 */
		public static function GetReportFieldValue($instReportField, $instRow)
		{
			switch ($instReportField->ParentObject->Name)
			{
				case "PrimaryObjectReportField":
				{
					return $instRow;
				}
				case "AttributeReportField":
				{
					$relTarget = $instReportField->GetRelationship(KnownRelationships::get___Attribute_Report_Field__has_target__Attribute());
					if ($relTarget == null)
					{
						return null;
					}
					else
					{
						$instAttribute = $relTarget->GetDestinationInstance();
						return $instAttribute;
					}
					break;
				}
				case "RelationshipReportField":
				{
					$relTarget = $instReportField->GetRelationship(KnownRelationships::get___Relationship_Report_Field__has_target__Relationship());
					if ($relTarget == null)
					{
						return null;
					}
					else
					{
						$instTarget = $relTarget->GetDestinationInstance();
						$rel = $instRow->GetRelationship($instTarget);
						if ($rel == null)
						{
							return null;
						}
						else
						{
							$relinsts = $rel->GetDestinationInstances();
							$renderAsText = $instReportField->GetAttributeValue(KnownAttributes::get___Boolean___Render_as_Text(), false);
							if ($renderAsText)
							{
								$text = "";
								foreach ($relinsts as $relinst)
								{
									$text .= $relinst->ToString();
									$text .= "\n";
								}
								return $text;
							}
							else
							{
								return $relinsts;
							}
						}
					}
					break;
				}
			}
			return null;
		}
		
		/**
		 * Executes the specified Method Binding.
		 * @param Instance $methodBinding
		 */
		public static function ExecuteMethodBinding($methodBinding)
		{
			switch ($methodBinding->ParentObject->Name)
			{
				case "ReturnInstanceSetMethodBinding":
				{
					$relSourceClass = $methodBinding->GetRelationship(KnownRelationships::get___Return_Instance_Set_Method_Binding__has_source__Class());
					if ($relSourceClass != null)
					{
						$instRet = array();
						$instsSourceClass = $relSourceClass->GetDestinationInstances();
						foreach ($instsSourceClass as $instSourceClass)
						{
							$obj = TenantObject::GetByGlobalIdentifier($instSourceClass->GlobalIdentifier);
							$insts = $obj->GetInstances();
							$instRet = array_merge($instRet, $insts);
						}
						return $instRet;
					}
					break;
				}
			}
		}
		
		/**
		 * Executes a saved Method Call.
		 * @param Instance $instMethodCall
		 */
		public static function ExecuteMethodCall($instMethodCall)
		{
			$relMethod = $instMethodCall->GetRelationship(Instance::GetByGlobalIdentifier("3D3B601B4EF049F3AF0586CEA0F00619"));
			if ($relMethod != null)
			{
				$instMethod = $relMethod->GetDestinationInstance();
				
				$relPromptValue = $instMethodCall->GetRelationship(Instance::GetByGlobalIdentifier("765BD0C9117D4D0E88C92CEBD4898135"));
				if ($relPromptValue != null)
				{
					$instPromptValues = $relPromptValue->GetDestinationInstances();
					return Objectify::ExecuteMethod($instMethod, $instPromptValues);
				}
			}
			return null;
		}
		
		/**
		 * Executes the specified XquizIT method.
		 * @param Instance $method
		 */
		public static function ExecuteMethod($method, $parameters = null)
		{
			if (is_string($method))
			{
				$methodName = $method;
				
				$objMethod = KnownObjects::get___Method();
				if ($objMethod == null)
				{
					trigger_error("XquizIT FATAL: required class `Method` not found; drop and recreate system?");
					return null;
				}
				
				$instsMethod = $objMethod->GetInstanceUsingAttributes(array
				(
					new TenantObjectInstancePropertyValue("Name", $method)
				));
				$method = $instsMethod[0];
			}
			
			if ($method == null)
			{
				trigger_error("XquizIT: method not found" . (isset($methodName) ? (" " . $methodName) : ""));
				return null;
			}
			
			if ($method->ParentObject->Name == "ControlTransactionMethod")
			{
				if ($method->GlobalIdentifier == "4E92D64EAC914ABF805296366DF93996")
				{
					// create instance
					$guid = UUID::Generate();
					
					$instPromptValue = $parameters[0];
					$relHasInstance = $instPromptValue->GetRelationship(Instance::GetByGlobalIdentifier("512B518EA89244ABAC354E9DBCABFF0B"));
					if ($relHasInstance != null)
					{
						$instDest = $relHasInstance->GetDestinationInstance();
						
						$obj = TenantObject::GetByGlobalIdentifier($instDest->GlobalIdentifier);
						$inst = new Instance($obj);
						$inst->GlobalIdentifier = $guid;
						
						$inst->Update();
					}
					return;
				}
			}
			
			$relBinding = $method->GetRelationship(KnownRelationships::get___Method__has__Method_Binding());
			if ($relBinding != null)
			{
				$instsBinding = $relBinding->GetDestinationInstances();
				foreach ($instsBinding as $instBinding)
				{
					return Objectify::ExecuteMethodBinding($instBinding);
				}
				return null;
			}
			
			if ($parameters == null) $parameters = array();
			$codeblob = $method->GetAttributeValue("CodeBlob");
			return eval($codeblob);
		}
		
		public static function SanitizeGlobalIdentifier($id)
		{
			if ($id != null)
			{
				$id = str_replace("{", "", $id);
				$id = str_replace("}", "", $id);
				$id = str_replace("-", "", $id);
			}
			return $id;
		}
		
		private static $pdo_statement_log = null;
		private static $pdo_statement_log_backtrace = null;
		private static $pdo_statement_log_parameter = null;
		
		public static function Log($message, $params = null, $severity = LogMessageSeverity::Error)
		{
			$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			
			if ($params == null) $params = array();
			
			$pdo = DataSystem::GetPDO();
			
			if (self::$pdo_statement_log == null)
			{
				$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "DebugMessages "
					. "(message_Content, message_SeverityID, message_Timestamp, message_IPAddress)"
					. " VALUES "
					. "(:message_Content, :message_SeverityID, NOW(), :message_IPAddress)";
				
				$statement = $pdo->prepare($query);
				self::$pdo_statement_log = $statement;
			}
			
			$result = self::$pdo_statement_log->execute(array
			(
				":message_Content" => $message,
				":message_SeverityID" => $severity,
				":message_IPAddress" => $_SERVER["REMOTE_ADDR"]
			));
			
			$msgid = $pdo->lastInsertId();
			
			foreach ($bt as $bti)
			{
				if (self::$pdo_statement_log_backtrace == null)
				{
					$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "DebugMessageBacktraces "
					. "(bt_MessageID, bt_FileName, bt_LineNumber)"
					. " VALUES "
					. "(:bt_MessageID, :bt_FileName, :bt_LineNumber)";
					
					$statement = $pdo->prepare($query);
					self::$pdo_statement_log_backtrace = $statement;
				}
				
				$result = self::$pdo_statement_log_backtrace->execute(array
				(
					":bt_MessageID" => $msgid,
					":bt_FileName" => $bti["file"],
					":bt_LineNumber" => $bti["line"]
				));
			}
			
			foreach ($params as $key => $value)
			{
				if (self::$pdo_statement_log_parameter == null)
				{
					$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "DebugMessageParameters "
					. "(mp_MessageID, mp_Name, mp_Value)"
					. " VALUES "
					. "(:mp_MessageID, :mp_Name, :mp_Value)";
					
					$statement = $pdo->prepare($query);
					self::$pdo_statement_log_parameter = $statement;
				}
				
				$result = self::$pdo_statement_log_parameter->execute(array
				(
					":mp_MessageID" => $msgid,
					":mp_Name" => $key,
					":mp_Value" => $value
				));
			}
		}
	}
?>