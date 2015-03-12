<?php
	namespace Objectify\Manager\Pages;
	
	use Phast\CancelEventArgs;
	
	use Objectify\Objects\Tenant;
	
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
				
	class TenantModifyPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$page = $e->RenderingPage;
			$tenant = Tenant::GetByID($page->GetPathVariableValue("tenantID"));
			
			$lv = $page->GetControlByID("lvCustomProperties");
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
			
			$lv = $page->GetControlByID("lvGlobalObjects");
			$objects = TenantObject::Get(null, $this->Tenant);
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