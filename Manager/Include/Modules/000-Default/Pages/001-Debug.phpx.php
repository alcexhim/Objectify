<?php
	namespace Objectify\Manager\Modules\DefaultModule\Pages;
	
	use Phast\CancelEventArgs;
	use Phast\Parser\PhastPage;
	use Phast\Data\DataSystem;
	use Phast\System;
	
	use PDO;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	class DebugPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$messageID = $this->Page->GetPathVariableValue("messageID");
			$pdo = DataSystem::GetPDO();
			
			if (is_numeric($messageID))
			{
				if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] == "delete")
				{
					$query = "DELETE FROM :tablename WHERE message_ID = :message_ID";
					$statement = $pdo->prepare($query);
					$result = $statement->execute(array
					(
						"tablename" => System::GetConfigurationValue("Database.TablePrefix") . "DebugMessages",
						"message_ID" => $messageID
					));
					if ($result !== false)
					{
						System::Redirect("~/debug");
						$e->Cancel = true;
					}
				}
				else
				{
					$query = "SELECT * FROM :tablename WHERE message_ID = :message_ID";
					$statement = $pdo->prepare($query);
					$statement->execute(array
					(
						"tablename" => System::GetConfigurationValue("Database.TablePrefix") . "DebugMessages",
						"message_ID" => $messageID
					));
					$values = $statement->fetch(PDO::FETCH_ASSOC);
					
					$this->Page->GetControlByID("divErrorDetails")->EnableRender = true;
					$this->Page->GetControlByID("lblMessageContent")->Value = $values["message_Content"];
					
					$query1 = "SELECT * FROM :tablename WHERE mp_MessageID = :mp_MessageID";
					$statement1 = $pdo->prepare($query1);
					$statement1->execute(array
					(
						"tablename" => System::GetConfigurationValue("Database.TablePrefix") . "DebugMessageParameters",
						"mp_MessageID" => $values["message_ID"]
					));
					$count1 = $statement1->rowCount();
					
					$lvParameters = $this->Page->GetControlByID("lvParameters");
					for ($j = 0; $j < $count1; $j++)
					{
						$values1 = $statement1->fetch(PDO::FETCH_ASSOC);
						$lvParameters->Items[] = new ListViewItem(array
						(
							new ListViewItemColumn("lvcName", $values1["mp_Name"]),
							new ListViewItemColumn("lvcValue", $values1["mp_Value"])
						));
					}
					
					$query1 = "SELECT * FROM :tablename WHERE bt_MessageID = :bt_MessageID";
					$statement1 = $pdo->prepare($query1);
					$statement1->execute(array
					(
						":tablename" => System::$Configuration["Database.TablePrefix"] . "DebugMessageBacktraces",
						":bt_MessageID" => $values["message_ID"]
					));
					$count1 = $statement1->rowCount();

					$lvBacktrace = $this->Page->GetControlByID("lvBacktrace");
					for ($j = 0; $j < $count1; $j++)
					{
						$values1 = $statement1->fetch(PDO::FETCH_ASSOC);
						$lvBacktrace->Items[] = new ListViewItem(array
						(
							new ListViewItemColumn("lvcFileName", "bt_FileName"),
							new ListViewItemColumn("lvcLineNumber", "bt_LineNumber")
						));
					}
				}
			}
			else
			{
				if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] == "delete")
				{
					$query = "DELETE FROM :tablename";
					$statement = $pdo->prepare($query);
					$result = $statement->execute(array
					(
						"tablename" => System::GetConfigurationValue("Database.TablePrefix") . "DebugMessages"
					));
					if ($result !== false)
					{
						System::Redirect("~/debug");
						$e->Cancel = true;
					}
				}
				else
				{
					$this->Page->GetControlByID("divErrorList")->EnableRender = true;
					
					$lvMessages = $this->Page->GetControlByID("lvMessages");
					
					$query = "SELECT * FROM :tablename";
					$statement = $pdo->prepare($query);
					$statement->execute(array
					(
						":tablename" => System::$Configuration["Database.TablePrefix"] . "DebugMessages"
					));
					$count = $statement->rowCount();
					for ($i = 0; $i < $count; $i++)
					{
						$values = $statement->fetch(PDO::FETCH_ASSOC);
						
						$lvi = new ListViewItem();
						
						$tenant = Tenant::GetByID($values["message_TenantID"]);
						if ($tenant != null)
						{
							$lvi->Columns[] = new ListViewItemColumn("lvcTenant", "<a href=\"" . System::ExpandRelativePath("~/Tenants/Manage/" . $tenant->URL) . "\">" . $tenant->URL . "</a>", $tenant->URL);
						}
						else
						{
							$lvi->Columns[] = new ListViewItemColumn("lvcTenant", "");
						}
						
						switch ($values["message_SeverityID"])
						{
						}
						$lvi->Columns[] = new ListViewItemColumn("lvcSeverity", "");
						
						$lvi->Columns[] = new ListViewItemColumn("lvcMessage", "<a href=\"" . System::ExpandRelativePath("~/debug/" . $values["message_ID"]) . "\">" . $values["message_Content"] . "</a>", $values["message_Content"]);
						
						$lvi->Columns[] = new ListViewItemColumn("lvcTimestamp", $values["message_Timestamp"]);
						$lvi->Columns[] = new ListViewItemColumn("lvcTimestamp", $values["message_IPAddress"]);
						
						$lvMessages->Items[] = $lvi;
					}
				}
			}
		}
	}
?>