<?php
	namespace Objectify\WebControls;

	use Phast\WebControl;
	use Phast\WebControlAttribute;
	
	use Phast\System;
	
	use Phast\WebControls\AdditionalDetailWidget;
	use Phast\WebControls\MenuItemCommand;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\Relationship;
	use Objectify\Objects\KnownRelationships;
	use Objectify\Objects\Instance;
	use Objectify\Objects\KnownAttributes;
	
	class InstanceDisplayWidget extends WebControl
	{
		public $CurrentInstance;
		public $InstanceID;
		
		public $ShowURL;
		public $ShowText;
		
		public function __construct($instance)
		{
			parent::__construct();
			
			$this->CurrentInstance = $instance;
			$this->TagName = "div";
			$this->ClassList[] = "InstanceDisplayWidget";
			
			$this->ShowURL = true;
			$this->ShowText = true;
		}
		
		protected function RenderBeginTag()
		{
			if ($this->CurrentInstance == null)
			{
				$this->CurrentInstance = Instance::GetByID($this->InstanceID);
			}
			
			if ($this->CurrentInstance != null)
			{
				$this->Attributes[] = new WebControlAttribute("data-instance-id", $this->CurrentInstance->GetInstanceID());
				
				$adw = new AdditionalDetailWidget();
				$adw->ExtraData = $this->CurrentInstance;
				$adw->ShowText = $this->ShowText;
				$adw->ShowURL = $this->ShowURL;
				
				$iid = $this->CurrentInstance->GetInstanceID();
				$iid = str_replace('$', '\\$', $iid);
				$adw->TargetURL = "~/instances/modify/" . $iid;
				$adw->Text = $this->CurrentInstance->ToString();
				$adw->ClassTitle = $this->CurrentInstance->ParentObject->ToString();
				
				$rels = Relationship::GetBySourceInstance($this->CurrentInstance, KnownRelationships::get___Class__has__Task(), true);
				$rels = $rels[0];
				
				if ($rels != null)
				{
					$instTasks = $rels->GetDestinationInstances();
					foreach ($instTasks as $task)
					{
						$adw->MenuItems[] = new MenuItemCommand($task->ToString());
					}
				}
				
				$adw->Content = function($sender, $extraData)
				{
					$instParentObject = $extraData->ParentObject->GetThisInstance();
					$rels = Relationship::GetBySourceInstance($instParentObject, KnownRelationships::get___Class__has_summary__Report_Field());
					$rel = $rels[0];
					if ($rel != null)
					{
						$instsSummaryReportField = $rel->GetDestinationInstances();
						echo ("<div class=\"PropertyGrid\">");
						foreach ($instsSummaryReportField as $instSummaryReportField)
						{
							echo ("<div class=\"Property\" data-instance-id=\"" . $instSummaryReportField->GetInstanceID() . "\">");

							$instReportFieldTitle = null;
							$relRFTitle = $instSummaryReportField->GetRelationship(KnownRelationships::get___Report_Field__has_title__Translatable_Text_Constant());
							if ($relRFTitle != null)
							{
								$instReportFieldTitle = $relRFTitle->GetDestinationInstance();
							}
							
							echo("<div class=\"PropertyName\"");
							if ($instReportFieldTitle != null) {
								// Show Field Properties - Show Field EC
								echo(" data-instance-id=\"" . $instReportFieldTitle->GetInstanceID() . "\"");
							}
							echo(">");
							
							if ($instReportFieldTitle != null) {
								echo($instReportFieldTitle->ToString());
							}
							
							echo ("</div>");
							
							echo("<div class=\"PropertyValue\">");
							
							if ($instSummaryReportField->ParentObject->Name == "AttributeReportField")
							{
								$relTarget = $instSummaryReportField->GetRelationship(KnownRelationships::get___Attribute_Report_Field__has_target__Attribute());
								if ($relTarget == null)
								{
									echo("(empty)");
								}
								else
								{
									$instAttribute = $relTarget->GetDestinationInstance();
									echo($extraData->GetAttributeValue($instAttribute, "(empty)"));
								}
							}
							else if ($instSummaryReportField->ParentObject->Name == "RelationshipReportField")
							{
								$relTarget = $instSummaryReportField->GetRelationship(KnownRelationships::get___Relationship_Report_Field__has_target__Relationship());
								if ($relTarget == null)
								{
									echo("(empty)");
								}
								else
								{
									$instTarget = $relTarget->GetDestinationInstance();
									$rel = $extraData->GetRelationship($instTarget);
									if ($rel == null)
									{
										echo ("(empty)");
									}
									else
									{
										$relinsts = $rel->GetDestinationInstances();
										$renderAsText = $instSummaryReportField->GetAttributeValue(KnownAttributes::get___Boolean___Render_as_Text(), false);
										foreach ($relinsts as $relinst)
										{
											if ($renderAsText)
											{
												echo ($relinst->ToString());
											}
											else
											{
												$adw = new InstanceDisplayWidget($relinst);
												$adw->Render();
											}
											echo("<br />");
										}
									}
								}
							}
							else
							{
								echo ("Get report field Value for '" . $instSummaryReportField->ParentObject->Name . "'");
							}
							echo("</div>");
							
							echo("</div>");
						}
						echo("</div>");
					}
				};
				
				$this->Controls[] = $adw;
			}
			parent::RenderBeginTag();
		}
	}

?>