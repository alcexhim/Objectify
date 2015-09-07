<?php
	namespace Objectify\Tenant\API;

	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	
	use Objectify\Objects\Tenant;
	
	class TenantAPI extends PhastPage
	{
		
		public function OnInitializing(CancelEventArgs $e)
		{
			$e->Cancel = true;
			
			header("Content-Type: application/json");
			
			if ($e->RenderingPage->IsPostback)
			{
				if ($_POST["tenant_ID"] != null)
				{
					// update an existing tenant
					$tenant = Tenant::GetByID($_POST["tenant_ID"]);
					
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
					
					if (Tenant::ExistsByURL($_POST["tenant_URL"]))
					{
						echo ("{ \"Result\": \"Failure\", \"Message\": \"The tenant already exists\" }");
						return;
					}
					
					$retval = Tenant::Create($_POST["tenant_URL"]);
					if ($retval === false)
					{
						echo ("{ \"Result\": \"Failure\", \"Message\": \"Unknown error occurred\" }");
						return;
					}
					echo ("{ \"Result\": \"Success\" }");
				}
			}
			else
			{
				switch ($_GET["action"])
				{
					case "retrieve":
					{
						$tenant_ID = $_GET["tenant_ID"];
						$tenant_URL = $_GET["tenant_URL"];
						
						if (is_numeric($tenant_ID))
						{
							$tenant = Tenant::GetByID($tenant_ID);
						}
						else
						{
							$tenant = Tenant::GetByURL($tenant_URL);
						}
						
						echo ("{ \"Result\": \"Success\", \"Items\": [ ");
						
						echo(json_encode($tenant));
						
						echo (" ] }");
						return;
					}
				}
				echo ("{ \"Result\": \"Failure\", \"Message\": \"Invalid request type\" }");
			}
		}
		
	}
?>