<?php
	namespace Objectify\Objects;
	use WebFX\System;
	use Phast\Data\DataSystem;
		
	\Enum::Create("Objectify\\Objects\\LogMessageSeverity", "Notice", "Warning", "Error");
	
	class Objectify
	{
		public static function Log($message, $params = null, $severity = LogMessageSeverity::Error)
		{
			$bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			
			if ($params == null) $params = array();
			
			$pdo = DataSystem::GetPDO();
			
			$tenant = Tenant::GetCurrent();
			
			$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "DebugMessages "
				. "(message_TenantID, message_Content, message_SeverityID, message_Timestamp, message_IPAddress)"
				. " VALUES "
				. "(:message_TenantID, :message_Content, :message_SeverityID, NOW(), :message_IPAddress)";
			
			$statement = $pdo->prepare($query);
			$result = $statement->execute(array
			(
				":message_TenantID" => ($tenant == null ? null : $tenant->ID),
				":message_Content" => $message,
				":message_SeverityID" => $severity,
				":message_IPAddress" => $_SERVER["REMOTE_ADDR"]
			));
			
			$msgid = $pdo->lastInsertId();
			
			foreach ($bt as $bti)
			{
				$query = "INSERT INTO " . System::GetConfigurationValue("Database.TablePrefix") . "DebugMessageBacktraces "
				. "(bt_MessageID, bt_FileName, bt_LineNumber)"
				. " VALUES "
				. "(:bt_MessageID, :bt_FileName, :bt_LineNumber)";
				
				$statement = $pdo->prepare($query);
				$result = $statement->execute(array
				(
					":bt_MessageID" => $msgid,
					":bt_FileName" => $bti["file"],
					":bt_LineNumber" => $bti["line"]
				));
			}
			
			foreach ($params as $key => $value)
			{
				$query = "INSERT INTO " . System::$Configuration["Database.TablePrefix"] . "DebugMessageParameters (mp_MessageID, mp_Name, mp_Value) VALUES (";
				$query .= $msgid . ", ";
				$query .= "'" . $MySQL->real_escape_string($key) . "', ";
				$query .= "'" . $MySQL->real_escape_string($value) . "'";
				$query .= ")";
				$MySQL->query($query);
			}
		}
	}
?>