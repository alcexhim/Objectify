<?php
	namespace Objectify\Manager\Modules\InstanceEditor\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	use Phast\System;

	use Phast\WebControls\ListViewColumn;
	use Phast\WebControls\FormViewItemText;
	use Phast\WebControls\FormViewItemChoice;
	use Phast\WebControls\FormViewItemChoiceValue;
	
	use Objectify\Objects\Instance;
	use Objectify\Objects\KnownAttributes;
	use Objectify\Objects\KnownRelationships;
	use Objectify\Objects\TenantObject;
	
	use Objectify\WebControls\InstanceListView;
	use Objectify\WebControls\RelationshipListView;
use Phast\WebControls\ListView;
use Phast\HTMLControls\HTMLControlTable;
use Phast\HTMLControl;
use Phast\WebStyleSheetRule;
use Phast\WebControls\Menu;
use Phast\WebControls\MenuItemHeader;
use Phast\WebControls\MenuItemCommand;
use Objectify\Objects\Objectify;
use Phast\WebControls\ListViewItem;
use Phast\WebControls\ListViewItemColumn;
use Objectify\WebControls\InstanceDisplayWidget;
												
	class ExecuteInstancePage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$iid = $this->Page->GetPathVariableValue("instanceID");
			$paramstr = $this->Page->GetPathVariableValue("paramstr");
			
			$iidParts = explode("$", $iid);
			$inst = Instance::GetByID($iidParts[1]);
			
			$idwObjectTitleHeading = $e->RenderingPage->GetControlByID("idwObjectTitleHeading");
			$idwObjectTitleHeading->CurrentInstance = $inst;
			$idwObjectTitleHeading->ShowURL = false;
			
			$fvPrompts = $e->RenderingPage->GetControlByID("fvPrompts");
			if ($paramstr != null)
			{
				$paramstr = base64_decode($paramstr);
				$json = json_decode($paramstr);
				
				$fvPrompts->EnableRender = false;
				
				$layerContent = $e->RenderingPage->GetControlByID("layerContent");
				
				$lv = new RelationshipListView();
				$lv->EnableAddRemoveRows = true;
				$lv->Instance = Instance::GetByGlobalIdentifier($json->Parameters[0]->Value[0]);
				$lv->Relationship = Instance::GetByGlobalIdentifier("F9B60C00FF1D438FAC746EDFA8DD7324");
				$layerContent->Controls[] = $lv;
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
				$rel = $inst->GetRelationship(KnownRelationships::get___Report__has__Report_Field());
				if ($rel != null)
				{
					$instsReportField = $rel->GetDestinationInstances();
					foreach ($instsReportField as $instReportField)
					{
						$title = $instReportField->ToString();
						if ($instReportField->ParentObject->Name == "PrimaryObjectReportField")
						{
							$title = $instsRow[0]->ParentObject->Name;
						}
						$lvReport->Columns[] = new ListViewColumn("ch" . $instReportField->ID, $title);
					}

					foreach ($instsRow as $instRow)
					{
						$lvi = new ListViewItem();
						$countReportField = count($instsReportField);
						for ($i = 0; $i < $countReportField; $i++)
						{
							$instReportField = $instsReportField[$i];
							$lvi->Columns[] = new ListViewItemColumn($lvReport->Columns[$i]->ID, function($sender)
							{
								$instRow = $sender->ExtraData[0];
								$instReportField = $sender->ExtraData[1];
								
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
										$idw = new InstanceDisplayWidget($value);
										$idw->Render();
									}
								}
								else
								{
									echo ($value);
								}
							}, $instRow->ToString(), array($instRow, $instReportField));
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