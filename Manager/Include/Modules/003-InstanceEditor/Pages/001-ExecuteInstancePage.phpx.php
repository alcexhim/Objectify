<?php
	namespace Objectify\Manager\Modules\InstanceEditor\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	use Phast\System;
	
	use Phast\WebControls\Disclosure;
	use Phast\WebControls\FormViewItemText;
	use Phast\WebControls\FormViewItemChoice;
	use Phast\WebControls\FormViewItemChoiceValue;
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColumn;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	use Phast\WebControls\Menu;
	use Phast\WebControls\MenuItemHeader;
	use Phast\WebControls\MenuItemCommand;
	use Phast\WebControls\TabPage;
	use Phast\WebControls\TabContainer;

	use Phast\HTMLControl;
	use Phast\HTMLControls\HTMLControlTable;
	
	use Phast\WebControlAttribute;
	use Phast\WebStyleSheetRule;
	
	use Objectify\Objects\Objectify;
	use Objectify\Objects\Instance;
	use Objectify\Objects\KnownAttributes;
	use Objectify\Objects\KnownRelationships;
	use Objectify\Objects\TenantObject;
	
	use Objectify\WebControls\InstanceDisplayWidget;
	use Objectify\WebControls\InstanceListView;
	use Objectify\WebControls\RelationshipListView;
	
	class ExecuteInstancePage extends PhastPage
	{
		private function CreateControlFromPageComponent($json, $instPageComponent)
		{
			$ctl = null;
			if ($instPageComponent->ParentObject->Name == "RelationshipEditorPageComponent")
			{
				$relHasTargetRelationship = $instPageComponent->GetRelationship(KnownRelationships::get___Relationship_Editor_Page_Component__has_target__Relationship());
				if ($relHasTargetRelationship != null)
				{
					$instTargetRelationship = $relHasTargetRelationship->GetDestinationInstance();
			
					$lv = new RelationshipListView();
					$lv->EnableAddRemoveRows = true;
					$lv->Instance = Instance::GetByGlobalIdentifier($json->Parameters[0]->Value[0]);
					$lv->Relationship = $instTargetRelationship;
			
					$rel_has_Report_Field = $instPageComponent->GetRelationship(KnownRelationships::get___Relationship_Editor_Page_Component__has__Report_Field());
					if ($rel_has_Report_Field != null)
					{
						$instReportFields = $rel_has_Report_Field->GetDestinationInstances();
						$lv->ReportFields = $instReportFields;
					}
			
					$ctl = $lv;
				}
			}
			else if ($instPageComponent->ParentObject->Name == "TabContainerPageComponent")
			{
				$relHasTabContainerTab = $instPageComponent->GetRelationship(KnownRelationships::get___Tab_Container_Page_Component__has__Tab_Container_Tab());
				if ($relHasTabContainerTab != null)
				{
					$tabContainer = new TabContainer();
					$tabContainer->ID = "TabContainer_" . $instPageComponent->GetInstanceID();
			
					$instTabContainerTabs = $relHasTabContainerTab->GetDestinationInstances();
					foreach ($instTabContainerTabs as $instTabContainerTab)
					{
						$tab = new TabPage();
						$tab->ID = $tabContainer->ID . "_Tab_" . $instTabContainerTab->GetInstanceID();
							
						$relTitle = $instTabContainerTab->GetRelationship(KnownRelationships::get___Tab_Container_Tab__has_title__Translatable_Text_Constant());
						if ($relTitle != null)
						{
							$instTitle = $relTitle->GetDestinationInstance();
							$tab->Title = $instTitle->ToString();
						}
						
						$relComponents = $instTabContainerTab->GetRelationship(KnownRelationships::get___Container_Page_Component__has__Page_Component());
						if ($relComponents != null)
						{
							$instComponents = $relComponents->GetDestinationInstances();
							foreach ($instComponents as $instComponent)
							{
								$ctl1 = $this->CreateControlFromPageComponent($json, $instComponent);
								if ($ctl1 != null) $tab->Controls[] = $ctl1;
							}
						}
						$tabContainer->TabPages[] = $tab;
					}
					
					if (count($tabContainer->TabPages) > 0)
					{
						$tabContainer->SelectedTab = $tabContainer->TabPages[0];
					}
					$ctl = $tabContainer;
				}
			}
			else if ($instPageComponent->ParentObject->Name == "AccordionPageComponent")
			{
				$accordion = new Disclosure();
				$accordion->ID = "Accordion_" . $instPageComponent->GetInstanceID();
					
				$relTitle = $instPageComponent->GetRelationship(KnownRelationships::get___Accordion_Page_Component__has_title__Translatable_Text_Constant());
				if ($relTitle != null)
				{
					$instTitle = $relTitle->GetDestinationInstance();
					$accordion->Title = $instTitle->ToString();
				}
	
				$relComponents = $instPageComponent->GetRelationship(KnownRelationships::get___Container_Page_Component__has__Page_Component());
				if ($relComponents != null)
				{
					$instComponents = $relComponents->GetDestinationInstances();
					foreach ($instComponents as $instComponent)
					{
						$ctl1 = $this->CreateControlFromPageComponent($json, $instComponent);
						if ($ctl1 != null) $accordion->Controls[] = $ctl1;
					}
				}
				$ctl = $accordion;
			}
			if ($ctl != null)
			{
				$ctl->ClassList[] = "Mocha-Instance";
				$ctl->Attributes[] = new WebControlAttribute("data-instance-id", $instPageComponent->GetInstanceID());
			}
			return $ctl;
		}
		
		public function OnInitializing(CancelEventArgs $e)
		{
			$iid = $this->Page->GetPathVariableValue("instanceID");
			$paramstr = $this->Page->GetPathVariableValue("paramstr");
			
			$iidParts = explode("$", $iid);
			$inst = Instance::GetByID($iidParts[1]);
			
			$idwObjectTitleHeading = $e->RenderingPage->GetControlByID("idwObjectTitleHeading");
			$idwObjectTitleHeading->CurrentInstance = $inst;
			// $idwObjectTitleHeading->ShowURL = false;
			
			$fvPrompts = $e->RenderingPage->GetControlByID("fvPrompts");
			if ($paramstr != null)
			{
				$paramstr = base64_decode($paramstr);
				$json = json_decode($paramstr);
				
				$fvPrompts->EnableRender = false;
				
				$layerContent = $e->RenderingPage->GetControlByID("layerContent");
				
				$relPageComponent = $inst->GetRelationship(KnownRelationships::get___UI_Task__has__Page_Component());
				if ($relPageComponent != null)
				{
					$instsPageComponent = $relPageComponent->GetDestinationInstances();
					foreach ($instsPageComponent as $instPageComponent)
					{
						$ctl = $this->CreateControlFromPageComponent($json, $instPageComponent);
						if ($ctl != null) $layerContent->Controls[] = $ctl;
					}
				}
			}
			else
			{
				$relPrompts = $inst->GetRelationship(KnownRelationships::get___Task__has__Prompt());
				if ($relPrompts != null)
				{
					$instPrompts = $relPrompts->GetDestinationInstances();
					
					foreach ($instPrompts as $instPrompt)
					{
						$fvi = null;
						
						switch ($instPrompt->ParentObject->Name)
						{
							case "ChoicePrompt":
							{
								$fvi = new FormViewItemChoice();
								$fvi->RequireSelectionFromChoices = true;
								$relChoices = $instPrompt->GetRelationship(KnownRelationships::get___Choice_Prompt__has_valid__Prompt_Value());
								$instChoices = $relChoices->GetDestinationInstances();
								
								foreach ($instChoices as $instChoice)
								{
									$item = new FormViewItemChoiceValue();
									
									$relPromptValueTitle = $instChoice->GetRelationship(KnownRelationships::get___Prompt_Value__has_title__Translatable_Text_Constant());
									if ($relPromptValueTitle != null)
									{
										$instPromptValueTitle = $relPromptValueTitle->GetDestinationInstance();
										$item->Title = $instPromptValueTitle->ToString();
									}
									// $item->Value = $instChoice->GetInstanceID();
									$item->Value = $instChoice->GlobalIdentifier;
									$fvi->Items[] = $item;
								}
								break;
							}
							case "InstancePrompt":
							{
								$fvi = new FormViewItemChoice();
								$fvi->RequireSelectionFromChoices = true;
								$relChoices = $instPrompt->GetRelationship(KnownRelationships::get___Instance_Prompt__has_valid__Class());
								$instChoices = $relChoices->GetDestinationInstances();
								
								foreach ($instChoices as $instChoice)
								{
									$instObj = TenantObject::GetByGlobalIdentifier($instChoice->GlobalIdentifier);
									$instObjInsts = $instObj->GetInstances();
									
									foreach ($instObjInsts as $instObjInst)
									{
										$item = new FormViewItemChoiceValue();
										
										$item->Title = $instObjInst->ToString();
										// $item->Value = $instObjInsts->GetInstanceID();
										$item->Value = $instObjInst->GlobalIdentifier;
										$fvi->Items[] = $item;
									}
								}
								break;
							}
							default:
							{
								$fvi = new FormViewItemText();
								break;
							}
						}
						
						if ($fvi != null)
						{
							$fvi->ID = $instPrompt->GetInstanceID();
							
							$rel = $instPrompt->GetRelationship(KnownRelationships::get___Prompt__has_title__Translatable_Text_Constant());
							if ($rel != null)
							{
								$instTitle = $rel->GetDestinationInstance();
								$fvi->Title = $instTitle->ToString();
							}
							
							$fvPrompts->Items[] = $fvi;
						}
					}
				}				
			}
			
			if ($inst->ParentObject->Name == "UITask")
			{
				// execute teh report
			}
			else if ($inst->ParentObject->Name == "RedirectTask")
			{
				$targetURL = $inst->GetAttributeValue(KnownAttributes::get___Text___Target_URL());
				System::Redirect($targetURL);
				die();
			}
			else if ($inst->ParentObject->Name == "StandardReport")
			{
				// execute teh report
				$layerContent = $e->RenderingPage->GetControlByID("layerContent");
				
				$table = new HTMLControlTable();
				$table->Width = "100%";
				
				$tr = new HTMLControl("tr");

				// load in faceted filters if we have them. Report Facets give us one-click access to commonly-used
				// filters.
				$relFacets = $inst->GetRelationship(KnownRelationships::get___Report__has__Report_Facet());
				if ($relFacets != null)
				{
					$td = new HTMLControl("td");
					$td->Width = "200px";
					$td->StyleRules[] = new WebStyleSheetRule("vertical-align", "top");
					
					$mnuFacets = new Menu();
					$instsFacets = $relFacets->GetDestinationInstances();
					foreach ($instsFacets as $instFacet)
					{
						$mnuFacets->Items[] = new MenuItemHeader($instFacet->ToString());
						
						$relFacetOptions = $instFacet->GetRelationship(KnownRelationships::get___Report_Facet__has__Report_Facet_Option());
						if ($relFacetOptions != null)
						{
							$instsFacetOptions = $relFacetOptions->GetDestinationInstances();
							foreach ($instsFacetOptions as $instFacetOption)
							{
								$mnuFacets->Items[] = new MenuItemCommand($instFacetOption->ToString());
							}
						}
					}
					
					$td->Controls[] = $mnuFacets;
					$tr->Controls[] = $td;
				}
				
				$relDataSource = $inst->GetRelationship(KnownRelationships::get___Report__has__Report_Data_Source());
				if ($relDataSource == null) return;
				
				$instDataSource = $relDataSource->GetDestinationInstance();
				$relSourceMethod = $instDataSource->GetRelationship(KnownRelationships::get___Report_Data_Source__has_source__Method());
				if ($relSourceMethod == null) return;
				
				$instSourceMethod = $relSourceMethod->GetDestinationInstance();
				$instsRow = Objectify::ExecuteMethod($instSourceMethod);
				
				$td = new HTMLControl("td");
				$td->StyleRules[] = new WebStyleSheetRule("vertical-align", "top");
				
				$lvReport = new ListView();
				$rel = $inst->GetRelationship(KnownRelationships::get___Report__has__Report_Column());
				if ($rel != null)
				{
					$instsReportColumn = $rel->GetDestinationInstances();
					foreach ($instsReportColumn as $instReportColumn)
					{
						$relReportField = $instReportColumn->GetRelationship(KnownRelationships::get___Report_Column__has__Report_Field());
						if ($relReportField != null)
						{
							$instReportField = $relReportField->GetDestinationInstance();
							$title = $instReportField->ToString();
							if ($instReportField->ParentObject->Name == "PrimaryObjectReportField")
							{
								$title = $instsRow[0]->ParentObject->Name;
							}
							$lvReport->Columns[] = new ListViewColumn("ch" . $instReportField->ID, $title);
						}
					}

					foreach ($instsRow as $instRow)
					{
						$lvi = new ListViewItem();
						$countReportColumn= count($instsReportColumn);
						for ($i = 0; $i < $countReportColumn; $i++)
						{
							$instReportColumn = $instsReportColumn[$i];
							
							$relReportField = $instReportColumn->GetRelationship(KnownRelationships::get___Report_Column__has__Report_Field());
							$instReportField = $relReportField->GetDestinationInstance();
							
							$lvi->Columns[] = new ListViewItemColumn($lvReport->Columns[$i]->ID, function($sender)
							{
								$instRow = $sender->ExtraData[0];
								$instReportField = $sender->ExtraData[1];
								$instReportColumn = $sender->ExtraData[2];
								
								$displayAsCount = false;
								$relHas_Report_Column_Option = $instReportColumn->GetRelationship(KnownRelationships::get___Report_Column__has__Report_Column_Option());
								if ($relHas_Report_Column_Option != null)
								{
									$instsHas_Report_Column_Option = $relHas_Report_Column_Option->GetDestinationInstances();
									foreach ($instsHas_Report_Column_Option as $instRCO)
									{
										if ($instRCO->GlobalIdentifier == "5C9B4C79995B4E6A81C039C174BF9F6D")
										{
											$displayAsCount = true;
										}
									}
								}
								
								$value = Objectify::GetReportFieldValue($instReportField, $instRow);
								if (is_object($value))
								{
									if (get_class($value) == "Objectify\\Objects\\Instance")
									{
										/*
										if ($value->HasParentObject(TenantObject::GetByName("Attribute")))
										{
											echo($instRow->GetAttributeValue($instAttribute, "(empty)"));
										}
										else
										{
										*/
										
										if ($displayAsCount)
										{
											echo ("1");
										}
										else
										{
											$idw = new InstanceDisplayWidget($value);
											$idw->Render();
										}
									}
								}
								else if (is_array($value))
								{
									if ($displayAsCount)
									{
										echo("<a href=\"#\" class=\"InstanceListDropDown\" data-row-instance-id=\"" . $instRow->GetInstanceID() . "\" data-field-instance-id=\"" . $instReportField->GetInstanceID() . "\">");
										echo (count($value));
										echo (" <i class=\"fa fa-caret-down\"></i></a>");
									}
									else
									{
										foreach ($value as $val)
										{
											if (get_class($val) == "Objectify\\Objects\\Instance")
											{
												$idw = new InstanceDisplayWidget($val);
												$idw->Render();
												echo ("<br />");
											}
											else
											{
												echo ("<!-- GetReportFieldValue not defined for class `" . get_class($val) . "` -->");
											}
										}
									}
								}
								else
								{
									echo ($value);
								}
							}, $instRow->ToString(), array($instRow, $instReportField, $instReportColumn));
						}
						$lvReport->Items[] = $lvi;
					}
				}
				$td->Controls[] = $lvReport;
				
				$tr->Controls[] = $td;
				
				$table->Controls[] = $tr;
				
				$layerContent->Controls[] = $table;
			}
			else
			{
				print_r($inst);
				die();
			}
		}
	}
?>