<?php
	namespace Objectify\Manager\Pages;
	
	use Phast\CancelEventArgs;
	use Phast\System;
	
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Phast\WebControls\TextBox;

	use Objectify\Objects\Tenant;
	use Objectify\Objects\TenantObject;
	
	class TenantModifyPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$page = $e->RenderingPage;
			
			$tenant = null;
			if ($page->HasPathVariable("tenantName"))
			{
				$tenantName = $page->GetPathVariableValue("tenantName");
				if (is_numeric($tenantName))
				{
					$tenant = Tenant::GetByID($tenantName);
				}
				else
				{
					$tenant = Tenant::GetByURL($tenantName);
				}
			}
			
			if ($tenant != null)
			{
				$tbsTabs = $page->GetControlByID("tbsTabs");
				
				$tabGeneralInformation = $tbsTabs->GetTabByID("tabGeneralInformation");
				$fv = $tabGeneralInformation->GetControlByID("fvGeneralInformation");
				
				$txtTenantName = $fv->GetItemByID("txtTenantName");
				$txtTenantDescription = $fv->GetItemByID("txtTenantDescription");
				
				$txtTenantName->Value = $tenant->URL;
				$txtTenantDescription->Value = $tenant->Description;

				$tabCustomProperties = $tbsTabs->GetTabByID("tabCustomProperties");
				$lv = $tabCustomProperties->GetControlByID("lvCustomProperties");
				
				$lvcPropertyName = $lv->GetColumnByID("lvcPropertyName");
				$lvcPropertyName->Template = function()
				{
					$txt = new TextBox();
					$txt->Render();
				};
				
				$lvcPropertyDescription = $lv->GetColumnByID("lvcPropertyDescription");
				$lvcPropertyDescription->Template = function()
				{
					$txt = new TextBox();
					$txt->Render();
				};
				
				$lvcPropertyValue = $lv->GetColumnByID("lvcPropertyValue");
				$lvcPropertyValue->Template = function()
				{
					$txt = new TextBox();
					$txt->Render();
				};
				
				$tabGlobalObjects = $tbsTabs->GetTabByID("tabGlobalObjects");
				
				$lv = $tabGlobalObjects->GetControlByID("lvInheritedObjects");
				$objects = TenantObject::Get(null, null);
				foreach ($objects as $object)
				{
					$lvi = new ListViewItem(array
					(
						new ListViewItemColumn("lvcObject", $object->Name, "<a href=\"" . System::ExpandRelativePath("~/tenant/manage/" . $tenant->URL . "/objects/" . $object->ID) . "\">" . $object->Name . "</a>"),
						// new ListViewItemColumn("lvcDescription", $object->GetDescription()),
						new ListViewItemColumn("lvcInstances", $object->CountInstances(), "<a href=\"" . System::ExpandRelativePath("~/tenant/manage/" . $tenant->URL . "/objects/" . $object->ID . "/instances") . "\">" . $object->CountInstances() . "</a>")
					));
					$lv->Items[] = $lvi;
				}
				
				$lv = $tabGlobalObjects->GetControlByID("lvGlobalObjects");
				$objects = TenantObject::Get(null, $tenant);
				foreach ($objects as $object)
				{
					$lvi = new ListViewItem(array
					(
						new ListViewItemColumn("lvcObject", $object->Name, "<a href=\"" . System::ExpandRelativePath("~/tenant/manage/" . $tenant->URL . "/objects/" . $object->ID) . "\">" . $object->Name . "</a>"),
						// new ListViewItemColumn("lvcDescription", $object->GetDescription()),
						new ListViewItemColumn("lvcInstances", $object->CountInstances(), "<a href=\"" . System::ExpandRelativePath("~/tenant/manage/" . $tenant->URL . "/objects/" . $object->ID . "/instances") . "\">" . $object->CountInstances() . "</a>")
					));
				}
			}
		}
	}
?>