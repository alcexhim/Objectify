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
use Objectify\Objects\KnownAttributes;
use Objectify\Objects\Objectify;
			
	class DefaultPage extends PhastPage
	{
		public function OnInitializing($e)
		{
			$e->RenderingPage->ClassList[] = "EnableHeader";
			$e->RenderingPage->ClassList[] = "EnableSidebar";
			
			$CurrentUser = User::GetCurrent();
			$DisableLoginPage = System::GetConfigurationValue("Application.DisableLoginPage", false);
			if (!$DisableLoginPage)
			{
				if ($CurrentUser == null)
				{
					if ($e->RenderingPage->GetServerVariableValue("RequireLogin") !== "false")
					{
						System::RedirectToLoginPage();
						$e->Cancel = true;
						return;
					}
				}
			}
			
			$sidebar = $this->Page->GetControlByID("sidebar");
			$sidebarMenu = $sidebar->GetControlByID("sidebarMenu");
			
			$cmdUserMenu = $this->Page->GetControlByID("cmdUserMenu");
			
			if ($CurrentUser != null)
			{
				$cmdUserMenu->Text = $CurrentUser->ToString();
			}
			else
			{
				$cmdUserMenu->Visible = false;
			}
			
			// TODO: Add your items here (load from Tenant.Sidebar Menu Items property?)
			
			$sidebarMenu->Items = array();
			$tenantName = System::GetTenantName();
			
			$objTenant = TenantObject::GetByName("Tenant");
			$instTenant = $objTenant->GetInstanceUsingAttributes(array
			(
				new TenantObjectInstancePropertyValue("Name", $tenantName)
			));
			$instTenant = $instTenant[0];

			$litTenantType = $this->Page->GetControlByID("litTenantType");
			$litTenantType->Value = Objectify::GenerateTenantBadgeHTML($instTenant);
			
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
						
						$rels = Relationship::GetBySourceInstance($instSidebarMenuItem, KnownRelationships::get___Menu_Item_Command__has_title__Translatable_Text_Constant());
						$insts = $rels[0]->GetDestinationInstances();
						$inst = $insts[0];
						
						$mi->Title = $inst->ToString();
						
						$relsIcon = Relationship::GetBySourceInstance($instSidebarMenuItem, KnownRelationships::get___Command_Menu_Item__has__Icon());
						$relIcon = $relsIcon[0];
						if ($relIcon != null)
						{
							$instIcons = $relIcon->GetDestinationInstances();
							$instIcon = $instIcons[0];
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
						
						$rels = Relationship::GetBySourceInstance($instSidebarMenuItem, KnownRelationships::get___Menu_Item_Command__has_title__Translatable_Text_Constant());
						$insts = $rels[0]->GetDestinationInstances();
						$inst = $insts[0];
						
						$mi->Title = $inst->ToString();
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