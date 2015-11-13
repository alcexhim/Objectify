<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\System;
	use Phast\Parser\PhastPage;
	
	use Objectify\Tenant\MasterPages\WebPage;
	use Objectify\Objects\Tenant;
	
	use Phast\WebControls\AdditionalDetailWidget;
	use Phast\WebControls\AdditionalDetailWidgetDisplayStyle;
	
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Phast\WebControls\MenuItemCommand;
	
	class DashboardPage extends PhastPage
	{
		public function OnInitializing($e)
		{
			$tenants = Tenant::Get();

			$lvTenantsActive = $this->Page->GetControlByID("lvTenantsActive");
			$lvTenantsInactive = $this->Page->GetControlByID("lvTenantsInactive");
			
			// $lblTenantCountTotal = $this->Page->GetControlByID("lblTenantCountTotal");
			// $lblTenantCountActive = $this->Page->GetControlByID("lblTenantCountActive");
			// $lblTenantCountInactive = $this->Page->GetControlByID("lblTenantCountInactive");
			$dscActiveTenants = $this->Page->GetControlByID("dscActiveTenants");
			$dscInactiveTenants = $this->Page->GetControlByID("dscInactiveTenants");
			
			$lblTenantCountTotal = count($tenants);
			
			$countActive = 0;
			$countInactive = 0;
			foreach ($tenants as $tenant)
			{
				$lvi = new ListViewItem();
				$lvi->Columns[] = new ListViewItemColumn("lvcTenantName", function($sender)
				{
					$tenant = $sender->ExtraData;
					$adw = new AdditionalDetailWidget();
					$adw->Text = $tenant->URL;
					$adw->ClassTitle = "Tenant";
					$adw->TargetURL = "~/tenants/launch/" . $tenant->URL;
					$adw->TargetFrame = "_blank";
					$adw->MenuItems = array
					(
						new MenuItemCommand("Tenant", null, null, null, array
						(
							// Modify Tenant (101) : for Instance (1332) : of Tenant (15724)
							// ~/o/15724/i/1332/t/101
							new MenuItemCommand("Modify", "~/tenants/modify/" . $tenant->URL),
							new MenuItemCommand("Clone", "~/tenants/clone/" . $tenant->URL),
							new MenuItemCommand("Delete", "~/tenants/delete/" . $tenant->URL)
						)),
						new MenuItemCommand("Migration", null, null, null, array
						(
							new MenuItemCommand("Create", "~/migration/create/" . $tenant->URL)
						)),
						new MenuItemCommand("Reporting", null, null, null, array
						(
							new MenuItemCommand("Create Custom Report from Here", "~/tenants/modify/" . $tenant->URL),
							new MenuItemCommand("Related Reports", "~/tenants/delete/" . $tenant->URL),
							new MenuItemCommand("Report Fields and Values", "~/tenants/delete/" . $tenant->URL)
						))
					);
					$adw->Render();
				}, $tenant->URL, $tenant);
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
				
				if ($tenant->IsExpired())
				{
					$countInactive++;
					$lvTenantsInactive->Items[] = $lvi;
				}
				else
				{
					$countActive++;
					$lvTenantsActive->Items[] = $lvi;
				}
			}
			
			$dscActiveTenants->Title = "Active Tenants (" . $countActive . ")";
			$dscInactiveTenants->Title = "Inactive Tenants (" . $countInactive . ")";
		}
	}
?>