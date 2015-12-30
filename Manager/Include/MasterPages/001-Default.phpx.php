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
			
			$sidebar = $this->Page->GetControlByID("sidebar");
			$sidebarMenu = $sidebar->GetControlByID("sidebarMenu");
			
			// TODO: Add your items here (load from Tenant.Sidebar Menu Items property?)
			
			/*
			$sidebarMenu->Items = array();
			$instTenant = TenantObject::GetByName("Tenant")->GetInstanceByIndex(0);
			$instSidebarMenuItems = $instTenant->GetPropertyValue("SidebarMenuItems")->GetInstances();
			foreach ...
			{
				$sidebarMenu->Items[] = ... 
			}
			*/
			
			// $sidebarMenu->Items[] = new MenuItemCommand("TEST COMMAND");
		}
	}
?>