<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\System;
	use Phast\CancelEventArgs;
	use Phast\Parser\PhastPage;
	
	use Objectify\Objects\KnownAttributes;
	use Objectify\Objects\KnownObjects;
	use Objectify\Objects\User;
	
	class DefaultPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$objTenant = KnownObjects::get___Tenant();
			$instTenant = $objTenant->GetInstances()[0];
			
			$instUser = User::GetCurrent();
			if ($instUser != null)
			{
				$defaultRedirectURL = $instUser->GetAttributeValue("DefaultRedirectURL", null);
				if ($defaultRedirectURL != null)
				{
					System::Redirect($defaultRedirectURL);
					$e->Cancel = true;
					return;
				}
			}
			
			$propDefaultRedirectURL = $instTenant->GetAttributeValue(KnownAttributes::get___Text___Default_Redirect_URL());
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