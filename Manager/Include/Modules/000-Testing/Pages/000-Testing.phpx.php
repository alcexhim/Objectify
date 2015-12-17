<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	
	use Objectify\Objects\TenantObject;
	
	class TestingPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$objESS = TenantObject::GetByName("ExecuteServerSideScriptMethodAction");
			$instESS = $objESS->GetInstances();
			
			$val = $instESS[0]->GetPropertyValue("CodeBlob");
			
			eval($val);
			
			die();
		}
	}
?>