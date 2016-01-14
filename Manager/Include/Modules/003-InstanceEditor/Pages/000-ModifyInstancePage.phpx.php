<?php
	namespace Objectify\Manager\Modules\InstanceEditor\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	use Phast\WebControls\FormViewItemText;
	
	use Objectify\Objects\TenantObjectInstance;
	use Objectify\WebControls\FormViewItemInstance;
				
	class ModifyInstancePage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$iid = $this->Page->GetPathVariableValue("instanceID");
			$litInstanceID = $e->RenderingPage->GetControlByID("litInstanceID");
			
			$iidParts = explode("$", $iid);
			$inst = TenantObjectInstance::GetByID($iidParts[1]);
			$litInstanceID->Value = $inst->ToString();
			
			$fv = $e->RenderingPage->GetControlByID("fvInstanceProperties");
			
			$instanceProperties = $inst->ParentObject->GetInstanceProperties();
			foreach ($instanceProperties as $prop)
			{
				$value = $inst->GetPropertyValue($prop);
				switch ($prop->DataType->Name)
				{
					case "MultipleInstance":
					{
						$fvi = new FormViewItemInstance("prop" . $prop->ID, "prop" . $prop->ID, $prop->Name, $prop->DefaultValue);
						$fvi->MultiSelect = true;
						
						foreach ($value->ValidObjects as $obj)
						{
							$fvi->ValidObjects[] = $obj;
						}
						$fv->Items[] = $fvi;
						break;
					}
					case "Text":
					{
						$fv->Items[] = new FormViewItemText("prop" . $prop->ID, "prop" . $prop->ID, $prop->Name, $value);
						break;
					}
				}
			}
		}
	}
?>