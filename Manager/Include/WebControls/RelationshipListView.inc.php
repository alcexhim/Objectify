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
	use Objectify\Objects\Objectify;
	
	class RelationshipListView extends ListView
	{
		public $Instance;
		public $Relationship;
		public $ReportColumns;
		
		protected function BeforeContent()
		{
			if ($this->Relationship == null)
			{
				parent::BeforeContent();
				return;
			}
			
			$relDestClass = $this->Relationship->GetRelationship(KnownRelationships::get___Relationship__has_destination__Class());
			if ($relDestClass != null)
			{
				$instDestClass = $relDestClass->GetDestinationInstance();
				
				if (isset($this->ReportColumns))
				{
					if (is_array($this->ReportColumns))
					{
						foreach ($this->ReportColumns as $instReportColumn)
						{
							$relReportField = $instReportColumn->GetRelationship(KnownRelationships::get___Report_Column__has__Report_Field());
							if ($relReportField != null)
							{
								$instReportField = $relReportField->GetDestinationInstance();
								if ($instReportField != null)
								{
									$title = $instReportField->ToString();
								}
								else
								{
									$title = "(no Report Field for inst " . $instReportColumn->GetInstanceID() . ")";
								}
							}
							else
							{
								$title = $instReportColumn->ToString();
							}
							
							$this->Columns[] = new ListViewColumn("lvc" . $instReportColumn->GetInstanceID(), $title);
						}
					}
				}
				
				$relThis = $this->Instance->GetRelationship($this->Relationship);
				if ($relThis != null)
				{
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
						
						if (isset($this->ReportColumns))
						{
							if (is_array($this->ReportColumns))
							{
								foreach ($this->ReportColumns as $instReportColumn)
								{
									$relReportField = $instReportColumn->GetRelationship(KnownRelationships::get___Report_Column__has__Report_Field());
									if ($relReportField == null) continue;
									
									$instReportField = $relReportField->GetDestinationInstance();
									$val = Objectify::GetReportFieldValue($instReportField, $instThis);
									
									$lvi->Columns[] = new ListViewItemColumn("lvc" . $instReportField->GetInstanceID(), function($sender)
									{
										if (is_array($sender->ExtraData))
										{
											foreach ($sender->ExtraData as $instTarg)
											{
												$adw = new InstanceDisplayWidget($instTarg);
												$adw->Render();
												echo ("<br />");
											}
										}
										else if (is_object($sender->ExtraData))
										{
											$adw = new InstanceDisplayWidget($sender->ExtraData);
											$adw->Render();
										}
									}, $val, $val);
								}
							}
						}
						$this->Items[] = $lvi;
					}
				}
			}
			
			parent::BeforeContent();
		}
	}
?>