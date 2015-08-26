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
?>