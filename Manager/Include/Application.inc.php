<?php
	use Phast\System;

	use Phast\Data\DataSystem;
	
	use Phast\WebControls\ButtonGroup;
	use Phast\WebControls\ButtonGroupButton;
	
	use Objectify\Tenant\MasterPages\WebPage;
	
	use Objectify\Tenant\Pages\LoginPage;
	use Objectify\Tenant\Pages\MainPage;
	use Objectify\Tenant\Pages\ModuleMainPage;
	use Objectify\Tenant\Pages\ModuleManagementPage;
	
	use Objectify\Tenant\Pages\TenantPropertiesPage;
	use Objectify\Tenant\Pages\TenantManagementPage;
	use Objectify\Tenant\Pages\TenantModuleManagementPage;
	
	use Objectify\Tenant\Pages\TenantObjectManagementPage;
	
	use Objectify\Tenant\Pages\TenantObjectInstanceBrowsePage;
	
	use Objectify\Tenant\Pages\TenantObjectMethodManagementPage;
	
	use Objectify\Tenant\Pages\ConfirmOperationPage;
	
	use Objectify\Objects\DataType;
	use Objectify\Objects\Tenant;
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectMethod;
	use Objectify\Objects\TenantObjectInstanceMethod;
	use Objectify\Objects\TenantObjectInstanceProperty;
	use Objectify\Objects\TenantStatus;
	use Objectify\Objects\TenantType;
	use Objectify\Objects\TenantObjectMethodParameterValue;
	use Objectify\Objects\User;
	
	function IsConfigured()
	{
		if (!(System::HasConfigurationValue("Database.ServerName") &&
			System::HasConfigurationValue("Database.DatabaseName") &&
			System::HasConfigurationValue("Database.UserName") &&
			System::HasConfigurationValue("Database.Password") &&
			System::HasConfigurationValue("Database.TablePrefix")
		))
		{
			return false;
		}
		
		$pdo = DataSystem::GetPDO() or null;
		if ($pdo == null) return false;
		
		$query = "SHOW TABLES LIKE '" . System::GetConfigurationValue("Database.TablePrefix") . "TenantObjects'";
		$statement = $pdo->prepare($query);
		$result = $statement->execute(array());
		
		if ($result === false) return false;
		
		if ($statement->rowCount() < 1) return false;
		return true;
	}
	
	function IsAdministrator()
	{
		if (!isset($_SESSION["Authentication.LoginToken"])) return false;
		
		$user = User::GetCurrent();
		if ($user == null) return false;
		
		return true;
	}
	
	System::$BeforeLaunchEventHandler = function($path)
	{
		if (!IsConfigured() && (!($path[0] == "setup")) && (!($path[0] == "Images")))
		{
			System::Redirect("~/setup");
			return true;
		}
		
		if (!IsAdministrator() && (!($path[0] == "account" && $path[1] == "login")) && (!($path[0] == "setup")) && (!($path[0] == "favicon.ico")))
		{
			if ($path[0] == "API")
			{
				echo ("{ \"Result\": \"Failure\", \"Remedy\": \"Login\" }");
				return false;
			}
			$path1 = implode("/", $path);
			$_SESSION["LoginRedirectURL"] = "~/" . $path1;
			
			System::RedirectToLoginPage();
			return true;
		}
		return true;
	};
	
	/*
	System::$Modules[] = new Module("net.Objectify.TenantManager.Default", array
	(
		new ModulePage("tenant", array
		(
			new ModulePage("create", function($path)
			{
				if ($_SERVER["REQUEST_METHOD"] === "POST")
				{
					$tenant_URL = $_POST["tenant_URL"];
					$tenant_Description = $_POST["tenant_Description"];
					$tenant_Status = ($_POST["tenant_Status"] == 1 ? TenantStatus::Enabled : TenantStatus::Disabled);
					$tenant_Type = TenantType::GetByID($_POST["tenant_TypeID"]);
					$tenant_BeginTimestamp = ($_POST["tenant_BeginTimestampValid"] == "1" ? null : $_POST["tenant_BeginTimestamp"]);
					$tenant_EndTimestamp = ($_POST["tenant_EndTimestampValid"] == "1" ? null : $_POST["tenant_EndTimestamp"]);
					
					$retval = Tenant::Create($tenant_URL, $tenant_Description, $tenant_Status, $tenant_Type, $tenant_BeginTimestamp, $tenant_EndTimestamp);
					
					if ($retval == null)
					{
						global $MySQL;
						echo($MySQL->error . " (" . $MySQL->errno . ")");
					}
					else
					{
						System::Redirect("~/tenant");
					}
				}
				else
				{
					$page = new TenantPropertiesPage();
					$page->Render();
					return true;
				}
			}),
			new ModulePage("modify", function($path)
			{
				if ($_SERVER["REQUEST_METHOD"] === "POST")
				{
					$tenant_URL = $_POST["tenant_URL"];
					
					$tenant = Tenant::GetByURL($path[0]);
					
					$tenant->URL = $_POST["tenant_URL"];
					$tenant->Description = $_POST["tenant_Description"];
					$tenant->Status = ($_POST["tenant_Status"] == 1 ? TenantStatus::Enabled : TenantStatus::Disabled);
					$tenant->Type = TenantType::GetByID($_POST["tenant_TypeID"]);
					$tenant->BeginTimestamp = ($_POST["tenant_BeginTimestampValid"] == "1" ? null : $_POST["tenant_BeginTimestamp"]);
					$tenant->EndTimestamp = ($_POST["tenant_EndTimestampValid"] == "1" ? null : $_POST["tenant_EndTimestamp"]);
					
					$retval = $tenant->Update();
					
					if (!$retval)
					{
						global $MySQL;
						echo($MySQL->error . " (" . $MySQL->errno . ")");
					}
					else
					{
						System::Redirect("~/tenant");
					}
					return true;
				}
				else
				{
					$page = new TenantPropertiesPage();
					$page->Tenant = Tenant::GetByURL($path[0]);
					$page->Render();
					return true;
				}
			}),
			new ModulePage("clone", function($path)
			{
				if ($_SERVER["REQUEST_METHOD"] === "POST")
				{
					$tenant_URL = $_POST["tenant_URL"];
					$tenant_Description = $_POST["tenant_Description"];
					$tenant_Status = ($_POST["tenant_Status"] == 1 ? TenantStatus::Enabled : TenantStatus::Disabled);
					$tenant_Type = TenantType::GetByID($_POST["tenant_TypeID"]);
					$tenant_BeginTimestamp = ($_POST["tenant_BeginTimestampValid"] == "1" ? null : $_POST["tenant_BeginTimestamp"]);
					$tenant_EndTimestamp = ($_POST["tenant_EndTimestampValid"] == "1" ? null : $_POST["tenant_EndTimestamp"]);
					
					$retval = Tenant::Create($tenant_URL, $tenant_Description, $tenant_Status, $tenant_Type, $tenant_BeginTimestamp, $tenant_EndTimestamp);
					
					if ($retval == null)
					{
						global $MySQL;
						echo($MySQL->error . " (" . $MySQL->errno . ")");
					}
					else
					{
						System::Redirect("~/tenant");
					}
				}
				else
				{
					$page = new TenantPropertiesPage();
					$page->Tenant = Tenant::GetByURL($path[0]);
					$page->Render();
					return true;
				}
			}),
			new ModulePage("delete", function($path)
			{
				if ($_SERVER["REQUEST_METHOD"] === "POST")
				{
					if ($_POST["Confirm"] == "1")
					{
						$tenant = Tenant::GetByURL($path[0]);
						if ($tenant->Delete())
						{
							System::Redirect("~/tenant");
						}
						else
						{
							global $MySQL;
							echo($MySQL->error . " (" . $MySQL->errno . ")");
						}
					}
				}
				else
				{
					$page = new ConfirmOperationPage();
					$page->ReturnButtonURL = "~/tenant";
					$page->Message = "Are you sure you want to delete the tenant '" . $path[0] . "'? This action cannot be undone, and will destroy any and all data associated with that tenant.";
					$page->Render();
					return true;
				}
			}),
			new ModulePage("manage", function($path)
			{
				if ($path[1] == "")
				{
					$tenant = Tenant::GetByURL($path[0]);
					if ($_SERVER["REQUEST_METHOD"] == "POST")
					{
						$properties = $tenant->GetProperties();
						foreach ($properties as $property)
						{
							$tenant->SetPropertyValue($property, $_POST["Property_" . $property->ID]);
						}
						System::Redirect("~/tenant/manage/" . $path[0]);
						return true;
					}
					else
					{
						$page = new TenantManagementPage();
						$page->Tenant = $tenant;
						$page->Render();
						return true;
					}
				}
				else
				{
					switch ($path[1])
					{
						case "modules":
						{
							$page = new TenantModuleManagementPage();
							$page->Tenant = Tenant::GetByURL($path[0]);
							$page->Module = \Objectify\Objects\Module::GetByID($path[2]);
							$page->Render();
							break;
						}
						case "objects":
						{
							if ($path[2] == "")
							{
								// $page = new TenantObjectBrowsePage();
								// $page->CurrentTenant = Tenant::GetByURL($path[0]);
								// $page->Render();
							}
							else
							{
								switch ($path[3])
								{
									case "instances":
									{
										switch ($path[4])
										{
											case "":
											{
												$tenant = Tenant::GetByURL($path[0]);
												$object = TenantObject::GetByID($path[2]);
												
												$page = new TenantObjectInstanceBrowsePage();
												$page->CurrentTenant = $tenant;
												$page->CurrentObject = $object;
												$page->Render();
												break;
											}
										}
									}
									case "methods":
									{
										switch ($path[4])
										{
											case "static":
											{
												$tenant = Tenant::GetByURL($path[0]);
												$object = TenantObject::GetByID($path[2]);
												$method = TenantObjectMethod::GetByID($path[5]);
												
												if ($_SERVER["REQUEST_METHOD"] == "POST")
												{
													$method->CodeBlob = $_POST["method_CodeBlob"];
													$method->Update();
													
													System::Redirect("~/tenant/manage/" . $tenant->URL . "/objects/" . $object->ID);
													return true;
												}
												
												$page = new TenantObjectMethodManagementPage();
												$page->CurrentTenant = $tenant;
												$page->CurrentObject = $object;
												$page->CurrentMethod = $method;
												$page->Render();
												break;
											}
											case "instance":
											{
												$page = new TenantObjectMethodManagementPage();
												$page->CurrentTenant = Tenant::GetByURL($path[0]);
												$page->CurrentObject = TenantObject::GetByID($path[2]);
												$page->CurrentMethod = TenantObjectInstanceMethod::GetByID($path[5]);
												$page->Render();
												break;
											}
										}
										break;
									}
									case "":
									{
										$tenant = Tenant::GetByURL($path[0]);
										$object = TenantObject::GetByID($path[2]);
										
										if ($_SERVER["REQUEST_METHOD"] == "POST")
										{
											$count = $_POST["InstanceProperty_NewPropertyCount"];
											for ($i = $count; $i > 0; $i--)
											{
												$name = $_POST["InstanceProperty_" . $i . "_Name"];
												$dataType = DataType::GetByID($_POST["InstanceProperty_" . $i . "_DataTypeID"]);
												$defaultValue = $_POST["InstanceProperty_" . $i . "_DefaultValue"];
												
												$object->CreateInstanceProperty(new TenantObjectInstanceProperty($name, $dataType, $defaultValue));
											}
											
											System::Redirect("~/tenant/manage/" . $tenant->URL . "/objects/" . $object->ID);
											return true;
										}
										else
										{
											$page = new TenantObjectManagementPage();
											$page->CurrentTenant = $tenant;
											$page->CurrentObject = $object;
											$page->Render();
										}
										break;
									}
								}
							}
							break;
						}
					}
				}
				return true;
			}),
			new ModulePage("launch", function($path)
			{
				$tenant = Tenant::GetByURL($path[0]);
				header("Location: /" . $tenant->URL);
			})
		)),
		new ModulePage("module", array
		(
			new ModulePage("", function($path)
			{
				$page = new ModuleMainPage();
				$page->Render();
				return true;
			}),
			new ModulePage("modify", function($path)
			{
				$module = \Objectify\Objects\Module::GetByID($path[0], true);
				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					$module->Title = $_POST["module_Title"];
					$module->Description = $_POST["module_Description"];
					$module->Update();
					
					System::Redirect("~/module/modify/" . $path[0]);
				}
				else
				{
					$page = new ModuleManagementPage();
					$page->Module = $module;
					$page->Render();
				}
				return true;
			})
		))
	));
	*/
?>