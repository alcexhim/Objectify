<?php
	namespace Objectify\WebControls;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColumn;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Phast\WebControls\AdditionalDetailWidget;
	use Phast\WebControls\TextBox;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\KnownRelationships;
	
	class RelationshipListView extends ListView
	{
		public $Instance;
		public $Relationship;
		
		protected function BeforeContent()
		{
			$relDestClass = $this->Relationship->GetRelationship(KnownRelationships::get___Relationship__has_destination__Class());
			if ($relDestClass != null)
			{
				$instDestClass = $relDestClass->GetDestinationInstance();
				
				$relAttributes = $instDestClass->GetRelationship(KnownRelationships::get___Class__has__Attribute());
				$instAttributes = $relAttributes->GetDestinationInstances();
				
				$this->Columns[] = new ListViewColumn("lvcLanguage", "Language");
				foreach ($instAttributes as $att)
				{
					$this->Columns[] = new ListViewColumn("lvc" . $att->GlobalIdentifier, $att->ToString());
				}
				
				$relThis = $this->Instance->GetRelationship($this->Relationship);
				$instsThis = $relThis->GetDestinationInstances();

				foreach ($instsThis as $instThis)
				{
					$lvi = new ListViewItem();
					$lvi->Columns[] = new ListViewItemColumn("lvcLanguage", function($sender)
					{
						$rel = $sender->ExtraData->GetRelationship(KnownRelationships::get___Translatable_Text_Constant_Value__has__Language());
						$inst = $rel->GetDestinationInstance();
						$adw = new InstanceDisplayWidget($inst);
						$adw->Render();
					}, null, $instThis);
					
					foreach ($instAttributes as $att)
					{
						$lvi->Columns[] = new ListViewItemColumn("lvc" . $att->GlobalIdentifier, function($sender)
						{
							$txt = new TextBox();
							$txt->Width = "100%";
							$txt->Text = $sender->ExtraData[0]->GetAttributeValue($sender->ExtraData[1]);
							$txt->Render();
						}, $instThis->GetAttributeValue($att), array($instThis, $att));
					}
					$this->Items[] = $lvi;
				}
			}
			
			parent::BeforeContent();
		}
	}
?>