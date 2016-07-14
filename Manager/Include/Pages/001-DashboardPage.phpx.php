<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\System;
	use Phast\Parser\PhastPage;
	
	use Phast\WebControls\AdditionalDetailWidget;
	use Phast\WebControls\AdditionalDetailWidgetDisplayStyle;
	
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Phast\WebControls\MenuItemCommand;
	
	use Objectify\Tenant\MasterPages\WebPage;
	
	use Objectify\Objects\TenantObject;
	use Objectify\WebControls\InstanceDisplayWidget;
		
	class DashboardPage extends PhastPage
	{
		public function OnInitializing($e)
		{
			$lvTenantsActive = $this->Page->GetControlByID("lvTenantsActive");
			$lvTenantsInactive = $this->Page->GetControlByID("lvTenantsInactive");
			
			$objTenant = TenantObject::GetByName("Tenant");
			$tenants = $objTenant->GetInstances();
			
			$dscActiveTenants = $this->Page->GetControlByID("dscActiveTenants");
			$dscInactiveTenants = $this->Page->GetControlByID("dscInactiveTenants");
			
			$lblTenantCountTotal = count($tenants);
			
			$countActive = 0;
			$countInactive = 0;
			
			foreach ($tenants as $tenant)
			{
				$tenantURL = $tenant->GetAttributeValue("TenantURL");
				$lvi = new ListViewItem();
				$lvi->Columns[] = new ListViewItemColumn("lvcTenantName", function($sender)
				{
					$tenant = $sender->ExtraData;
					
					$adw = new InstanceDisplayWidget($tenant);
					/*
					$adw = new AdditionalDetailWidget();
					$adw->Text = $tenantURL;
					$adw->ClassTitle = "Tenant";
					$adw->TargetURL = "~/tenants/launch/" . $tenantURL;
					$adw->TargetFrame = "_blank";
					$adw->MenuItems = array
					(
						new MenuItemCommand("Tenant", null, null, null, array
						(
							// Modify Tenant (101) : for Instance (1332) : of Tenant (15724)
							// ~/o/15724/i/1332/t/101
							new MenuItemCommand("Modify", "~/tenants/modify/" . $tenantURL),
							new MenuItemCommand("Clone", "~/tenants/clone/" . $tenantURL),
							new MenuItemCommand("Delete", "~/tenants/delete/" . $tenantURL)
						)),
						new MenuItemCommand("Migration", null, null, null, array
						(
							new MenuItemCommand("Create", "~/migration/create/" . $tenantURL)
						)),
						new MenuItemCommand("Reporting", null, null, null, array
						(
							new MenuItemCommand("Create Custom Report from Here", "~/tenants/modify/" . $tenantURL),
							new MenuItemCommand("Related Reports", "~/tenants/delete/" . $tenantURL),
							new MenuItemCommand("Report Fields and Values", "~/tenants/delete/" . $tenantURL)
						))
					);
					*/
					$adw->Render();
				}, $tenantURL, $tenant);
				$lvi->Columns[] = new ListViewItemColumn("lvcTenantType", "");
				
				$str = "";
				$strPlain = "";
				$lvi->Columns[] = new ListViewItemColumn("lvcActivationDate", $tenant->BeginTimestamp == null ? "(indefinite)" : $tenant->BeginTimestamp);
				$lvi->Columns[] = new ListViewItemColumn("lvcTerminationDate", $tenant->EndTimestamp == null ? "(indefinite)" : $tenant->EndTimestamp);
				
				/*
				$lvi->Columns[] = new ListViewItemColumn("lvcDescription", $tenant->Description);
				$lvi->Columns[] = new ListViewItemColumn("lvcActions",
					"<a href=\"" . System::ExpandRelativePath("~/Tenants/Manage/" . $tenant->URL) . "\">Manage</a> | " .
					"<a href=\"" . System::ExpandRelativePath("~/Tenants/Modify/" . $tenant->URL) . "\">Edit</a> | " .
					"<a href=\"" . System::ExpandRelativePath("~/Tenants/Clone/" . $tenant->URL) . "\">Clone</a> | " .
					"<a href=\"" . System::ExpandRelativePath("~/Tenants/Delete/" . $tenant->URL) . "\">Delete</a>");
				*/
				/*
				if ($tenant->IsExpired())
				{
					$countInactive++;
					$lvTenantsInactive->Items[] = $lvi;
				}
				else
				{
				*/
					$countActive++;
					$lvTenantsActive->Items[] = $lvi;
				//				}
			}
			
			$dscActiveTenants->Title = "Active Tenants (" . $countActive . ")";
			$dscInactiveTenants->Title = "Inactive Tenants (" . $countInactive . ")";
		}
	}
?>