<?php
	namespace Objectify\Manager\Pages;
	
	use Phast\CancelEventArgs;
	use Phast\Parser\PhastPage;
	use Phast\Data\DataSystem;
	use Phast\System;
		
	class SetupPage extends PhastPage
	{
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
					echo("{");
					echo("\"Result\": \"Success\"");
					echo("}");
				}
				
				$e->Cancel = true;
			}
		}
	}
?>