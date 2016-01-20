<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\System;
	use Phast\CancelEventArgs;
	
	use Phast\Parser\PhastPage;
	use Objectify\Objects\TenantObject;
use Objectify\Objects\User;
			
	class DefaultPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$objTenant = TenantObject::GetByName("Tenant");
			$instTenant = $objTenant->GetInstances()[0];
			
			$instUser = User::GetCurrent();
			$defaultRedirectURL = $instUser->GetPropertyValue("DefaultRedirectURL", null);
			if ($defaultRedirectURL != null)
			{
				System::Redirect($defaultRedirectURL);
				$e->Cancel = true;
				return;
			}
			
			$propDefaultRedirectURL = $instTenant->GetPropertyValue("DefaultRedirectURL");
			if ($propDefaultRedirectURL != null)
			{
				System::Redirect($propDefaultRedirectURL);
			}
			else
			{
				System::Redirect("~/dashboard");
			}
			$e->Cancel = true;
		}
	}
?>