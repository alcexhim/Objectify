<?php
	namespace Objectify\Manager\Modules\InstanceEditor\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	use Phast\WebControls\FormViewItemText;
	
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Objectify\Objects\Relationship;
	use Objectify\Objects\Instance;
	
	use Objectify\WebControls\FormViewItemInstance;
	use Objectify\WebControls\InstanceDisplayWidget;
	
	class ModifyInstancePage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$iid = $this->Page->GetPathVariableValue("instanceID");
			$litInstanceID = $e->RenderingPage->GetControlByID("litInstanceID");
			
			$iidParts = explode("$", $iid);
			$inst = Instance::GetByID($iidParts[1]);
			$litInstanceID->Value = $inst->ToString();
			
			$litInstanceObjectID = $e->RenderingPage->GetControlByID("litInstanceObjectID");
			$litInstanceObjectID->Value = $inst->ParentObject->ToString();
			
			$tbsTabs = $e->RenderingPage->GetControlByID("tbsTabs");
			
			
			$tabInstanceProperties = $tbsTabs->GetTabByID("tabInstanceProperties");
			
			$fv = $tabInstanceProperties->GetControlByID("fvInstanceProperties");
			
			$instanceProperties = $inst->ParentObject->GetInstanceProperties();
			foreach ($instanceProperties as $prop)
			{
				$value = $inst->GetPropertyValue($prop);
				switch ($prop->DataType->Name)
				{
					case "SingleInstance":
					case "MultipleInstance":
					{
						$fvi = new FormViewItemInstance("prop" . $prop->ID, "prop" . $prop->ID, $prop->Name, $prop->DefaultValue);
						$fvi->MultiSelect = ($prop->DataType->Name == "MultipleInstance");
						
						if ($value == null) continue;
						
						if ($prop->DataType->Name == "MultipleInstance")
						{
							$insts = $value->GetInstances();
						}
						else if ($prop->DataType->Name == "SingleInstance")
						{
							$insts = array($value->GetInstance());
						}
						
						if (is_array($insts))
						{
							foreach ($insts as $instt)
							{
								$fvi->SelectedInstances[] = $instt;
							}
						}
						
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
			
			$tabRelationships = $tbsTabs->GetTabByID("tabRelationships");
			$lvRelationships = $tabRelationships->GetControlByID("lvRelationships");
			$rels = Relationship::GetBySourceInstance($inst);
			foreach ($rels as $rel)
			{
				$lvi = new ListViewItem(array
				(
					new ListViewItemColumn("lvcRelationship", function($sender)
					{
						$iv = new InstanceDisplayWidget($sender->ExtraData);
						$iv->Render();
					}, null, $rel->RelationshipInstance),
					new ListViewItemColumn("lvcDestinationInstances", function($sender)
					{
						foreach ($sender->ExtraData as $inst)
						{
							$iv = new InstanceDisplayWidget($inst);
							$iv->Render();
							echo ("<br />");
						}
					}, null, $rel->GetDestinationInstances())
				));
				$lvRelationships->Items[] = $lvi;
			}
		}
	}
?>