<?php
	
	namespace Objectify\Tenant\Pages;

	use Phast\System;
	use Phast\CancelEventArgs;
	use Phast\Parser\PhastPage;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColumn;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;

	use Objectify\Objects\Instance;
	use Objectify\Objects\KnownInstances;
	use Objectify\Objects\Objectify;
	use Objectify\Objects\Relationship;
	use Objectify\Objects\TenantObject;
	
	use Objectify\WebControls\InstanceDisplayWidget;
	use Objectify\WebControls\ObjectDisplayWidget;
		
	class ObjectBrowsePage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$inst = KnownInstances::get___Standard_Report___All_Objects();
			System::Redirect("~/instances/execute/" . $inst->GetInstanceID());
			die();
		}
	}

	class ObjectModifyPage extends PhastPage
	{
		/**
		 * The object that is being modified by this ObjectModifyPage.
		 * @var TenantObject
		 */
		public $CurrentObject;
		
		public function OnInitializing(CancelEventArgs $e)
		{
			$this->CurrentObject = TenantObject::GetByID($e->RenderingPage->GetPathVariableValue("objectID"));
			
			$idwObjectTitleHeading = $e->RenderingPage->GetControlByID("idwObjectTitleHeading");
			$idwObjectTitleHeading->CurrentInstance = $this->CurrentObject->GetThisInstance();
			$idwObjectTitleHeading->ShowURL = false;
			
			$tbsTabs = $e->RenderingPage->GetControlByID("tbsTabs");
			$tabGeneralInformation = $tbsTabs->GetTabByID("tabGeneralInformation");
			$fvGeneralInformation = $tabGeneralInformation->GetControlByID("fvGeneralInformation");
			
			if ($this->CurrentObject != null)
			{
				$fvGeneralInformation->GetItemByID("txtObjectName")->Value = $this->CurrentObject->Name;
				$fvGeneralInformation->GetItemByID("txtGlobalIdentifier")->Value = $this->CurrentObject->GlobalIdentifier;
			}
			
			$instThisObject = Instance::GetByGlobalIdentifier($this->CurrentObject->GlobalIdentifier);
			
			$tabAttributes = $tbsTabs->GetTabByID("tabAttributes");
			$lvAttributes = $tabAttributes->GetControlByID("lvAttributes");

			$instRelAtt = Instance::GetByGlobalIdentifier("{DECBB61A-2C6C-4BC8-9042-0B5B701E08DE}");
			$insts = $instThisObject->GetRelatedInstances($instRelAtt);
			foreach ($insts as $inst)
			{
				$lvi = new ListViewItem(array
				(
					new ListViewItemColumn("lvcAttribute", function($sender)
					{
						$iv = new InstanceDisplayWidget($sender->ExtraData);
						$iv->Render();
					}, null, $inst),
					new ListViewItemColumn("lvcValue", function($sender)
					{
						$val = $sender->ExtraData["ThisObject"]->GetAttributeValue($sender->ExtraData["ThisAttribute"]);
						if ($val == null)
						{
							echo("<!-- (empty) -->");
						}
						else
						{
							echo (Objectify::HTML_FormatValue($val));
						}
					}, null, array("ThisObject" => $instThisObject, "ThisAttribute" => $inst))
				));
				$lvAttributes->Items[] = $lvi;
			}
			
			$tabRelationships = $tbsTabs->GetTabByID("tabRelationships");
			$lvRelationships = $tabRelationships->GetControlByID("lvRelationships");
			
			$rels = Relationship::GetBySourceInstance($instThisObject);
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