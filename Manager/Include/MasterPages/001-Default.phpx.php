<?php
	namespace Objectify\Tenant\MasterPages;
	
	use Phast\Parser\PhastPage;
	use Phast\System;
	
	use Objectify\Objects\User;
	use Objectify\Objects\TenantObject;
	
	use Phast\WebControls\MenuItemCommand;
	
	class DefaultPage extends PhastPage
	{
		public function OnInitializing($e)
		{
			$e->RenderingPage->ClassList[] = "EnableHeader";
			$e->RenderingPage->ClassList[] = "EnableSidebar";
			
			$ibSearch = $e->RenderingPage->GetControlByID("ibSearch");
			$ibSearch->ValidObjects[] = TenantObject::GetByName("Task");
			
			if (User::GetCurrent() == null)
			{
				if ($e->RenderingPage->GetServerVariableValue("RequireLogin") !== "false")
				{
					System::RedirectToLoginPage();
					$e->Cancel = true;
					return;
				}
			}
			
			$sidebar = $e->RenderingPage->GetControlByID("sidebar");
			$sidebarMenu = $sidebar->GetControlByID("sidebarMenu");
			
			$sidebarMenu->Items[] = new MenuItemCommand("TEST COMMAND");
		}
	}
?>