<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\System;
	use Phast\Parser\PhastPage;
	
	use Objectify\Tenant\MasterPages\WebPage;
	use Objectify\Objects\Tenant;
	
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
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
				$lvi->Columns[] = new ListViewItemColumn("lvcTenantName", "<a href=\"" . System::ExpandRelativePath("~/tenant/launch/" . $tenant->URL) . "\" target=\"_blank\">" . $tenant->URL . "</a>", $tenant->URL);
				
				$str = "";
				$strPlain = "";
				foreach ($tenant->DataCenters->Items as $item)
				{
					$str .= "<a href=\"http://" . $item->HostName . "/" . $tenant->URL . "\">" . $item->Title . " (" . $item->HostName . ")</a><br />";
					$strPlain .= $item->Title . " ";
				}
				$lvi->Columns[] = new ListViewItemColumn("lvcDataCenters", $str, $strPlain);
				$lvi->Columns[] = new ListViewItemColumn("lvcPaymentPlan", $tenant->PaymentPlan->Title);
				$lvi->Columns[] = new ListViewItemColumn("lvcActivationDate", $tenant->BeginTimestamp == null ? "(indefinite)" : $tenant->BeginTimestamp);
				$lvi->Columns[] = new ListViewItemColumn("lvcTerminationDate", $tenant->EndTimestamp == null ? "(indefinite)" : $tenant->EndTimestamp);
				$lvi->Columns[] = new ListViewItemColumn("lvcDescription", $tenant->Description);
				$lvi->Columns[] = new ListViewItemColumn("lvcActions",
					"<a href=\"" . System::ExpandRelativePath("~/Tenants/Manage/" . $tenant->URL) . "\">Manage</a> | " .
					"<a href=\"" . System::ExpandRelativePath("~/Tenants/Modify/" . $tenant->URL) . "\">Edit</a> | " .
					"<a href=\"" . System::ExpandRelativePath("~/Tenants/Clone/" . $tenant->URL) . "\">Clone</a> | " .
					"<a href=\"" . System::ExpandRelativePath("~/Tenants/Delete/" . $tenant->URL) . "\">Delete</a>");

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