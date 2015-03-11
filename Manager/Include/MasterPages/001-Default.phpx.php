<?php
	namespace Objectify\Tenant\MasterPages;
	
	use Phast\Parser\PhastPage;
	use Phast\System;
	
	use Objectify\Objects\User;
	
	class DefaultPage extends PhastPage
	{
		public function OnInitializing($e)
		{
			if (User::GetCurrent() == null)
			{
				if ($e->RenderingPage->GetServerVariableValue("RequireLogin") !== "false")
				{
					System::RedirectToLoginPage();
					$e->Cancel = true;
					return;
				}
			}
		}
	}
?>