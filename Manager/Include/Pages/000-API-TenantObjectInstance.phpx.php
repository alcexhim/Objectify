<?php
	namespace Objectify\Tenant\API;

	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstance;
	use Objectify\Objects\TenantObjectInstancePropertyValue;
	
	class TenantObjectInstanceAPI extends PhastPage
	{
		
		public function OnInitializing(CancelEventArgs $e)
		{
			$e->Cancel = true;
			
			header("Content-Type: application/json");
			
			if ($e->RenderingPage->IsPostback)
			{
				switch ($_GET["Action"])
				{
					case "Create":
					{
						$obj = TenantObject::GetByID($_GET["ParentObjectID"]);
						$globalIdentifier = null;
						
						$props = array();
						$propCount = $_POST["ParamCount"];
						for ($i = 0; $i < $propCount; $i++)
						{
							$props[] = new TenantObjectInstancePropertyValue($_POST["Param" . $i . "Name"], $_POST["Param" . $i . "Value"]);
						}
						
						$obj->CreateInstance($props, $globalIdentifier);
						echo ("{ \"Result\": \"Success\" }");
						break;
					}
				}
			}
			else
			{
				switch ($_GET["Action"])
				{
					case "Retrieve":
					{
						$items = array();
						if (isset($_GET["ID"]))
						{
							$items[] = TenantObjectInstance::GetByID($_GET["ID"]);
						}
						else if (isset($_GET["GlobalIdentifier"]))
						{
							$items[] = TenantObjectInstance::GetByGlobalIdentifier($_GET["GlobalIdentifier"]);
						}
						else
						{
							$objects = array();
							if (isset($_GET["ParentObjectID"]))
							{
								$objects[] = TenantObject::GetByID($_GET["ParentObjectID"]);
							}
							else
							{
								$objects = TenantObject::Get();
							}
							$items = array();
							foreach ($objects as $obj)
							{
								$items1 = $obj->GetInstances();
								foreach ($items1 as $item)
								{
									$items[] = $item;
								}
							}
						}
						
						echo ("{ \"Result\": \"Success\", \"Items\": [ ");
						
						$count = count($items);
						for($i = 0; $i < $count; $i++)
						{
							echo(json_encode($items[$i]));
							if ($i < $count - 1) echo(",");
						}
						
						echo (" ] }");
						return;
					}
				}
				echo ("{ \"Result\": \"Failure\", \"Message\": \"Invalid request type\" }");
			}
		}
		
	}
?>