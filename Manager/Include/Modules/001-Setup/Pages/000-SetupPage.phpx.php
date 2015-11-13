<?php
	namespace Objectify\Manager\Pages;
	
	use Phast\CancelEventArgs;
	use Phast\Parser\PhastPage;
	use Phast\Data\DataSystem;
	use Phast\System;
	use Phast\RandomStringGenerator;
	use Phast\RandomStringGeneratorCharacterSets;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstancePropertyValue;
	
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
					// create the initial user
					$Administrator_PasswordSalt = RandomStringGenerator::Generate(RandomStringGeneratorCharacterSets::AlphaNumericMixedCase, 32);
					$Administrator_PasswordHash = hash("sha512", $Administrator_Password . $Administrator_PasswordSalt);
					
					// create a new global (non-tenanted) instance of the User object
					// this can be set by User property IsGlobal - USE SPARINGLY!!!
					
					// Create the tenanted objects required before anything else takes place
					$objUser = TenantObject::Create("User");
					// $objUser->CreateProperty("UserName");
					// $objUser->CreateProperty("PasswordHash");
					// $objUser->CreateProperty("PasswordSalt");
					// $objUser->CreateProperty("IsGlobal");
					
					$objTenant = TenantObject::Create("Tenant");
					
					// $objSecurityGroup = TenantObject::GetByName("SecurityGroup");
					// $instTenantManager = $objSecurityGroup->GetInstance(15); // Tenant Manager
					
					$instUser = $objUser->CreateInstance(array
					(
						new TenantObjectInstancePropertyValue
						(
							$objUser->GetProperty("UserName"),
							$Administrator_UserName
						),
						new TenantObjectInstancePropertyValue
						(
							$objUser->GetProperty("PasswordHash"),
							$Administrator_PasswordHash
						),
						new TenantObjectInstancePropertyValue
						(
							$objUser->GetProperty("PasswordSalt"),
							$Administrator_PasswordSalt
						),
						new TenantObjectInstancePropertyValue
						(
							$objUser->GetProperty("IsGlobal"),
							true
						)
					));
					
					// $instUser->SetPropertyValue($objUser->GetInstanceProperty("SecurityGroups"), array
					// (
					//		$instTenantManager
					//		... multiple instances go here
					// ));
					
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