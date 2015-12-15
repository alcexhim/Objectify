<?php
	namespace Objectify\Tenant\API;

	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstancePropertyValue;
	
	class TenantObjectAPI extends PhastPage
	{
		
		public function OnInitializing(CancelEventArgs $e)
		{
			$e->Cancel = true;
			
			header("Content-Type: application/json");
			
			if ($e->RenderingPage->IsPostback)
			{
				if ($_POST["ID"] != null)
				{
					// update an existing tenant
					// $tenant = Tenant::GetByID($_POST["tenant_ID"]);
					
					$tenant->URL = $_POST["tenant_Name"];
					$tenant->Description = $_POST["tenant_Description"];
					if ($tenant->Update())
					{
						echo ("{ \"Result\": \"Success\" }");
					}
					else
					{
						echo ("{ \"Result\": \"Failure\" }");
					}
				}
				else
				{
					// create a new tenant
					$count = 1;
					if (isset($_POST["tenant_Count"]))
					{
						$count = $_POST["tenant_Count"];
					}
					
					for ($i = 0; $i < $count; $i++)
					{
						$tenantName = $_POST["tenant_URL"];
						if ($count > 1)
						{
							$tenantName .= str_pad(($i + 1), strlen($count), "0", STR_PAD_LEFT);
						}
						
						/*
						if (Tenant::ExistsByURL($tenantName))
						{
							echo ("{ \"Result\": \"Failure\", \"Message\": \"The tenant already exists\" }");
							return;
						}
						*/
						
						$objTenant = TenantObject::GetByName("Tenant");
						$retval = $objTenant->CreateInstance(array
						(
							new TenantObjectInstancePropertyValue("TenantURL", $tenantName)
						));
						
						if ($retval === false)
						{
							echo ("{ \"Result\": \"Failure\", \"Message\": \"Unknown error occurred\" }");
							return;
						}
					}
					echo ("{ \"Result\": \"Success\" }");
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
							$item = TenantObject::GetByID($_GET["ID"]);
							if ($item != null) $items[] = $item;
						}
						else if (isset($_GET["Name"]))
						{
							$item = TenantObject::GetByName($_GET["Name"]);
							if ($item != null) $items[] = $item;
						}
						else
						{
							$items = TenantObject::Get();
						}
						
						echo ("{ \"Result\": \"Success\", \"Items\": [ ");
						
						$count = count($items);
						for($i = 0; $i < $count; $i++)
						{
							$str = $items[$i]->ToString();
							$items[$i]->DisplayTitle = $str;
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