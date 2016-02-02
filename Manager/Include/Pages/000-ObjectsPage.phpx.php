<?php
	
	namespace Objectify\Tenant\Pages;

	use Phast\CancelEventArgs;
	use Phast\Parser\PhastPage;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColumn;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstance;
	
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
			
			$props = $this->CurrentObject->GetProperties();
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
					new ListViewItemColumn("lvcDataType", $prop->DataType->Name),
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
					}, null, $this->CurrentObject->GetPropertyValue($prop)),
					new ListViewItemColumn("lvcValue", function($sender)
					{
						if (is_object($sender->ExtraData))
						{
							if (get_class($sender->ExtraData) == "Objectify\\Objects\\MultipleInstanceProperty")
							{
								$iv = new InstanceDisplayWidget();
								$insts = $sender->ExtraData->GetInstances();
								$iv->InstanceID = $insts[0]->ID;
								$iv->Render();
							}
							else if (get_class($sender->ExtraData) == "Objectify\\Objects\\SingleInstanceProperty")
							{
								$iv = new InstanceDisplayWidget();
								$iv->InstanceID = $sender->ExtraData->GetInstance()->ID;
								$iv->Render();
							}
							else
							{
								// print_r($sender->ExtraData);
								echo($sender->ExtraData);
							}
						}
						else
						{
							echo($sender->ExtraData);
						}
					}, null, $this->CurrentObject->GetPropertyValue($prop))
				));
				
				$lvStaticProperties->Items[] = $lvi;
			}
			
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
			
			$instThisObject = TenantObjectInstance::GetByGlobalIdentifier($this->CurrentObject->GlobalIdentifier);
			
			$tabAttributes = $tbsTabs->GetTabByID("tabAttributes");
			$lvAttributes = $tabAttributes->GetControlByID("lvAttributes");

			$instRelAtt = TenantObjectInstance::GetByID(4);
			$relsAtts = Relationship::GetBySourceInstance($instThisObject, $instRelAtt);
			
			foreach ($relsAtts as $rel)
			{
				$lvi = new ListViewItem(array
				(
					new ListViewItemColumn("lvcAttribute", function($sender)
					{
						$iv = new InstanceDisplayWidget($sender->ExtraData[0]);
						$iv->Render();
					}, null, $rel->GetDestinationInstances()),
					new ListViewItemColumn("lvcValue", function($sender)
					{
						echo("(empty)");
					}, null, $rel->GetDestinationInstances())
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