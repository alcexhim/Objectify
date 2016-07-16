<?php
	namespace Objectify\Objects;
	
	use Phast\Enumeration;
	use Phast\System;
	
	use Phast\Data\DataSystem;
	
	class LogMessageSeverity extends Enumeration
	{
		const Notice = 1;
		const Warning = 2;
		const Error = 3;
	}
	
	class Objectify
	{
		/**
		 * Executes the specified XquizIT method.
		 * @param TenantObjectInstance $method
		 */
		public static function ExecuteMethod($method, $parameters = null)
		{
			if (is_string($method))
			{
				$methodName = $method;
				
				$objMethod = TenantObject::GetByName("Method");
				$instMethod = $objMethod->GetInstance(array
				(
					new TenantObjectInstancePropertyValue("Name", $method)
				));
				$method = $instMethod;
			}
			
			if ($method == null)
			{
				trigger_error("XquizIT: method not found" . (isset($methodName) ? (" " . $methodName) : ""));
				return null;
			}
			
			if ($parameters == null) $parameters = array();
			$codeblob = $method->GetPropertyValue("CodeBlob");
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