<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\Objectify;
	
	class TestingPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$objESS = TenantObject::GetByName("ServerSideScriptMethod");
			$instESS = $objESS->GetInstances();
			
			Objectify::ExecuteMethod("GetCurrentUser", array
			(
				"MethodName" => "cast_01102"
			));
			
			die();
		}
	}
?>