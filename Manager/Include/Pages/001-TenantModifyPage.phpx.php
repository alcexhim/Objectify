<?php
	namespace Objectify\Manager\Pages;
	
	use Phast\CancelEventArgs;
	
	use Objectify\Objects\Tenant;
	use Objectify\Objects\TenantObject;
	
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Phast\WebControls\TextBox;
	
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
				
				$properties = $tenant->GetProperties();
				foreach ($properties as $property)
				{
					$lvi = new ListViewItem(array
					(
						new ListViewItemColumn("lvcPropertyName", $property->Name),
						new ListViewItemColumn("lvcPropertyDescription", $property->Description),
						new ListViewItemColumn("lvcPropertyValue", null, function()
						{
							$property->RenderEditor($tenant->GetPropertyValue($property));
						})
					));
					$lv->Items[] = $lvi;
				}
				
				/*
				$lv = $page->GetControlByID("lvEnabledModules");
				$modules = Module::Get(null, $this->Tenant);
				foreach ($modules as $module)
				{
					$lvi = new ListViewItem(array
					(
						new ListViewItemColumn("lvcModule", $module->Name, "<a href=\"" . System::ExpandRelativePath("~/tenant/modify/" . $tenant->URL . "/modules/" . $module->ID) . "\">" . $module->Title . "</a>"),
						new ListViewItemColumn("lvcDescription", $module->Description)
					));
					$lvModules->Items[] = $lvi;
				}
				*/
				
				$tabGlobalObjects = $tbsTabs->GetTabByID("tabGlobalObjects");
				$lv = $tabGlobalObjects->GetControlByID("lvGlobalObjects");
				$objects = TenantObject::Get(null, $tenant);
				foreach ($objects as $object)
				{
					$lvi = new ListViewItem(array
					(
						new ListViewItemColumn("lvcObject", $object->Name, "<a href=\"" . System::ExpandRelativePath("~/tenant/manage/" . $tenant->URL . "/objects/" . $object->ID) . "\">" . $object->Name . "</a>"),
						new ListViewItemColumn("lvcDescription", $object->Description),
						new ListViewItemColumn("lvcInstances", $object->CountInstances(), "<a href=\"" . System::ExpandRelativePath("~/tenant/manage/" . $tenant->URL . "/objects/" . $object->ID . "/instances") . "\">" . $object->CountInstances() . "</a>")
					));
				}
			}
		}
	}
?>