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
			
			Tenant::Create($_POST["tenant_URL"]);
			
			echo ("{ \"Result\": \"Success\" }");
		}
		
	}
?>