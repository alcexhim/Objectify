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
use Objectify\Objects\TenantObject;
		
	class ModifyInstancePage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$iid = $this->Page->GetPathVariableValue("instanceID");
			$iidParts = explode("$", $iid);
			$inst = Instance::GetByID($iidParts[1]);
			
			$idwMain = $e->RenderingPage->GetControlByID("idwMain");
			$idwMain->ShowURL = false;
			$idwMain->CurrentInstance = $inst;
			
			$litGlobalIdentifier = $e->RenderingPage->GetControlByID("litGlobalIdentifier");
			$litGlobalIdentifier->Value = $inst->GlobalIdentifier;
			
			$litInstanceObjectID = $e->RenderingPage->GetControlByID("litInstanceObjectID");
			$litInstanceObjectID->Value = $inst->ParentObject->ToString();
			
			$tbsTabs = $e->RenderingPage->GetControlByID("tbsTabs");
			
			$tabAttributes = $tbsTabs->GetTabByID("tabAttributes");
			$lvAttributes = $tabAttributes->GetControlByID("lvAttributes");
			
			$atts = $inst->ParentObject->GetAttributes();
			foreach ($atts as $att)
			{
				$lvi = new ListViewItem(array
				(
					new ListViewItemColumn("lvcAttribute", function($sender)
					{
						if ($sender->ExtraData != null) {
							$iv = new InstanceDisplayWidget($sender->ExtraData);
							$iv->Render();
						}
					}, null, $att),
					
					// HACK HACK HACK: Figure out why the string "System" gets wiped out when in a ListViewItemColumn
					new ListViewItemColumn("lvcValue", " " . $inst->GetAttributeValue($att), null)
				));
				$lvAttributes->Items[] = $lvi;
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
			
			$tabInstances = $tbsTabs->GetTabByID("tabInstances");
			$instObj = TenantObject::GetByGlobalIdentifier($inst->GlobalIdentifier);
			if ($instObj != null)
			{
				$ilvInstances = $tabInstances->GetControlByID("ilvInstances");
				$ilvInstances->Object = $instObj;
				$tabInstances->Visible = true;
			}
		}
	}
?>