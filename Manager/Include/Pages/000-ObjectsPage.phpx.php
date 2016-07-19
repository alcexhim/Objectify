<?php
	
	namespace Objectify\Tenant\Pages;

	use Phast\CancelEventArgs;
	use Phast\Parser\PhastPage;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColumn;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\Instance;
	
	use Objectify\WebControls\InstanceDisplayWidget;
	use Objectify\WebControls\ObjectDisplayWidget;
	use Objectify\Objects\Relationship;
		
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
			$idwObjectTitleHeading->CurrentObject = $this->CurrentObject;
			
			$tbsTabs = $e->RenderingPage->GetControlByID("tbsTabs");
			$tabGeneralInformation = $tbsTabs->GetTabByID("tabGeneralInformation");
			$fvGeneralInformation = $tabGeneralInformation->GetControlByID("fvGeneralInformation");
			
			if ($this->CurrentObject != null)
			{
				$fvGeneralInformation->GetItemByID("txtObjectName")->Value = $this->CurrentObject->Name;
				$fvGeneralInformation->GetItemByID("txtGlobalIdentifier")->Value = $this->CurrentObject->GlobalIdentifier;
			}
			
			$tabProperties = $tbsTabs->GetTabByID("tabProperties");
			$lvStaticProperties = $tabProperties->GetControlByID("lvStaticProperties");
			$lvInstanceProperties = $tabProperties->GetControlByID("lvInstanceProperties");
			
			$props = $this->CurrentObject->GetInstanceProperties();
			foreach ($props as $prop)
			{
				$lvi = new ListViewItem(array
				(
					new ListViewItemColumn("lvcProperty", function($sender)
					{
						switch ($sender->ExtraData->DataType->Name)
						{
							case "SingleInstance":
							{
								echo ("<i class=\"fa fa-cube fa-fw\"></i>");
								break;
							}
							case "MultipleInstance":
							{
								echo ("<i class=\"fa fa-cubes fa-fw\"></i>");
								break;
							}
							case "Text":
							{
								echo ("<i class=\"fa fa-file-text-o fa-fw\"></i>");
								break;
							}
						}
						echo ($sender->ExtraData->Name);
					}, null, $prop),
					new ListViewItemColumn("lvcDataType", function($sender)
					{
						echo ($sender->ExtraData->DataType->Name);
					}, null, $prop),
					new ListViewItemColumn("lvcValidObjects", function($sender)
					{
						if (is_object($sender->ExtraData))
						{
							if (get_class($sender->ExtraData) == "Objectify\\Objects\\MultipleInstanceProperty"
								|| get_class($sender->ExtraData) == "Objectify\\Objects\\SingleInstanceProperty")
							{
								foreach ($sender->ExtraData->ValidObjects as $validObject)
								{
									$iv = new ObjectDisplayWidget();
									$iv->CurrentObject = $validObject;
									$iv->Render();
									echo ("<br />");
								}
							}
						}
					}, null, $prop->DefaultValue),
					new ListViewItemColumn("lvcDefaultValue", $prop->DefaultValue)
				));
				$lvInstanceProperties->Items[] = $lvi;
			}
			
			$instThisObject = Instance::GetByGlobalIdentifier($this->CurrentObject->GlobalIdentifier);
			
			$tabAttributes = $tbsTabs->GetTabByID("tabAttributes");
			$lvAttributes = $tabAttributes->GetControlByID("lvAttributes");

			$instRelAtt = Instance::GetByGlobalIdentifier("{DECBB61A-2C6C-4BC8-9042-0B5B701E08DE}");
			$relsAtts = Relationship::GetBySourceInstance($instThisObject, $instRelAtt);
			$rel = $relsAtts[0];
			
			if ($rel != null)
			{
				$insts = $rel->GetDestinationInstances();
				
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
								echo ($val);
							}
						}, null, array("ThisObject" => $instThisObject, "ThisAttribute" => $inst))
					));
					$lvAttributes->Items[] = $lvi;
				}
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