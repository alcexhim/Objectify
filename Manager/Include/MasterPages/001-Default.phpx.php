<?php
	namespace Objectify\Tenant\MasterPages;
	
	use Phast\Parser\PhastPage;
	use Phast\System;
	
	use Objectify\Objects\User;
	use Objectify\Objects\Tenant;
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstancePropertyValue;
	use Objectify\Objects\Relationship;
	use Objectify\Objects\Instance;
	use Objectify\Objects\KnownRelationships;
	
	use Phast\WebControls\MenuItemCommand;
	use Phast\WebControls\MenuItemSeparator;
	use Phast\WebControls\MenuItemHeader;
	use Phast\WebControlAttribute;
	
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
			
			$sidebarMenu->Items = array();
			$tenantName = System::GetTenantName();
			
			$objTenant = TenantObject::GetByName("Tenant");
			$instTenant = $objTenant->GetInstanceUsingAttributes(array
			(
				new TenantObjectInstancePropertyValue("Name", $tenantName)
			));
			$instTenant = $instTenant[0];
			
			/*
			
			if ($instTenant == null) {
				echo ("Tenant not found: " . $tenantName); die();
			}
			*/
			
			$instRel__Tenant__has__Sidebar_Menu_Items = KnownRelationships::get___Tenant__has_sidebar__Menu_Item();
			$rels = Relationship::GetBySourceInstance($instTenant, $instRel__Tenant__has__Sidebar_Menu_Items);
			$rel = $rels[0];
			
			$instSidebarMenuItems = $rel->GetDestinationInstances();
			
			foreach ($instSidebarMenuItems as $instSidebarMenuItem)
			{
				switch ($instSidebarMenuItem->ParentObject->Name)
				{
					case "MenuItemCommand":
					{
						$mi = new MenuItemCommand();
						
						$instsTitle = $instSidebarMenuItem->GetPropertyValue("Title")->GetInstances();
						$mi->Title = $instsTitle[0]->ToString();
						
						$instIcon = $instSidebarMenuItem->GetPropertyValue("Icon")->GetInstance();
						if ($instIcon != null)
						{
							switch ($instIcon->ParentObject->Name)
							{
								case "IconFontAwesome":
								{
									$mi->IconName = $instIcon->GetPropertyValue("IconName");
									break;
								}
							}
						}
						$mi->TargetURL = $instSidebarMenuItem->GetAttributeValue("TargetURL");
						break;
					}
					case "MenuItemSeparator":
					{
						$mi = new MenuItemSeparator();
						break;
					}
					case "MenuItemHeader":
					{
						$mi = new MenuItemHeader();
						$instsTitle = $instSidebarMenuItem->GetPropertyValue("Title")->GetInstances();
						$mi->Title = $instsTitle[0]->ToString();
						break;
					}
				}
				if (isset($mi))
				{
					$mi->Attributes[] = new WebControlAttribute("data-instance-id", $instSidebarMenuItem->GetInstanceID());
					$sidebarMenu->Items[] = $mi;
				}
			}
		}
	}
?>