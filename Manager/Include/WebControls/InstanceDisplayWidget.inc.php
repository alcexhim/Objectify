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
				
				$instTasks = $this->CurrentInstance->GetRelatedInstances(KnownRelationships::get___Class__has__Task());
				foreach ($instTasks as $task)
				{
					$adw->MenuItems[] = new MenuItemCommand($task->ToString());
				}
				
				$adw->Content = function($sender, $extraData)
				{
					$instParentObject = $extraData->ParentObject->GetThisInstance();
					
					echo ("<div class=\"PropertyGrid\">");
					
					echo ("<div class=\"Property\">");
					echo ("<div class=\"PropertyName\">Global Identifier</div>");
					echo ("<div class=\"PropertyValue\">" . $extraData->GlobalIdentifier . "</div>");
					echo ("</div>");

					// 10 spins with preloading fields, 6 spins without
					// so forcing this to be an AJAX popup is actually faster
					
					if (true)
					{
						$instsSummaryReportField = $instParentObject->GetRelatedInstances(KnownRelationships::get___Class__has_summary__Report_Field());
						foreach ($instsSummaryReportField as $instSummaryReportField)
						{
							echo ("<div class=\"Property\" data-instance-id=\"" . $instSummaryReportField->GetInstanceID() . "\">");

							$instReportFieldTitle = $instSummaryReportField->GetRelatedInstance(KnownRelationships::get___Report_Field__has_title__Translatable_Text_Constant());
							
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
								$instAttribute = $instSummaryReportField->GetRelatedInstance(KnownRelationships::get___Attribute_Report_Field__has_target__Attribute());
								if ($instAttribute == null)
								{
									echo("(empty)");
								}
								else
								{
									echo($extraData->GetAttributeValue($instAttribute, "(empty)"));
								}
							}
							else if ($instSummaryReportField->ParentObject->Name == "RelationshipReportField")
							{
								$instTarget = $instSummaryReportField->GetRelatedInstance(KnownRelationships::get___Relationship_Report_Field__has_target__Relationship());
								if ($instTarget == null)
								{
									echo("(empty)");
								}
								else
								{
									$relinsts = $extraData->GetRelatedInstances($instTarget);
									if (count($relinsts) == 0)
									{
										echo ("(empty)");
									}
									else
									{
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
					}
					
					echo("</div>");
				};
				$this->Controls[] = $adw;
			}
			parent::RenderBeginTag();
		}
	}

?>