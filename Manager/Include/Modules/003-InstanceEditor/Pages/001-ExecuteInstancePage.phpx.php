<?php
	namespace Objectify\Manager\Modules\InstanceEditor\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	use Phast\System;
	
	use Phast\WebControls\Disclosure;
	
	use Phast\WebControls\FormView;
	use Phast\WebControls\FormViewItemBoolean;
	use Phast\WebControls\FormViewItemChoice;
	use Phast\WebControls\FormViewItemChoiceValue;
	use Phast\WebControls\FormViewItemDateTime;
	use Phast\WebControls\FormViewItemLabel;
	use Phast\WebControls\FormViewItemText;
	
	use Phast\WebControls\Menu;
	use Phast\WebControls\MenuItemHeader;
	use Phast\WebControls\MenuItemCommand;
	
	use Phast\WebControls\Panel;
	use Phast\WebControls\TabPage;
	use Phast\WebControls\TabContainer;

	use Phast\HTMLControl;
	use Phast\HTMLControls\Image;
	use Phast\HTMLControls\Paragraph;
	use Phast\HTMLControls\HTMLControlTable;
	use Phast\HTMLControls\Heading;
	
	use Phast\WebControlAttribute;
	use Phast\WebStyleSheetRule;
	
	use Objectify\Objects\Objectify;
	use Objectify\Objects\Instance;
	use Objectify\Objects\KnownAttributes;
	use Objectify\Objects\KnownRelationships;
	use Objectify\Objects\TenantObject;

	use Objectify\WebControls\FormViewItemInstance;
	use Objectify\WebControls\ReportListView;
	use Objectify\WebControls\RelationshipListView;
	
	class ExecuteInstancePage extends PhastPage
	{
		private function CreateControlFromPageComponent($json, $instPageComponent)
		{
			$instPrimary = Instance::GetByInstanceID($json->Parameters[0]->Value[0]);
			$ctl = null;
			
			switch ($instPageComponent->ParentObject->Name)
			{
				case "RelationshipEditorPageComponent":
				{
					$relHasTargetRelationship = $instPageComponent->GetRelationship(KnownRelationships::get___Relationship_Editor_Page_Component__has_target__Relationship());
					if ($relHasTargetRelationship != null)
					{
						$instTargetRelationship = $relHasTargetRelationship->GetDestinationInstance();
						
						$lv = new RelationshipListView();
						$lv->EnableAddRemoveRows = true;
						$lv->Instance = $instPrimary;
						$lv->Relationship = $instTargetRelationship;
							
						$rel_has_Report_Column = $instPageComponent->GetRelationship(KnownRelationships::get___Relationship_Editor_Page_Component__has__Report_Column());
						if ($rel_has_Report_Column != null)
						{
							$instReportColumns = $rel_has_Report_Column->GetDestinationInstances();
							$lv->ReportColumns = $instReportColumns;
						}
							
						$ctl = $lv;
					}
					break;
				}
				case "AttributeEditorPageComponent":
				{
					$relHasTargetAttribute = $instPageComponent->GetRelationship(KnownRelationships::get___Attribute_Editor_Page_Component__has_target__Attribute());
					if ($relHasTargetAttribute != null)
					{
						$instsTargetAttribute = $relHasTargetAttribute->GetDestinationInstances();
							
						$fv = new FormView();
						foreach ($instsTargetAttribute as $instTargetAttribute)
						{
							switch ($instTargetAttribute->ParentObject->Name)
							{
								case "BooleanAttribute":
								{
									$fvi = new FormViewItemBoolean();
									break;
								}
								case "DateAttribute":
								{
									$fvi = new FormViewItemDateTime();
									break;
								}
								case "TextAttribute":
								default:
								{
									$fvi = new FormViewItemText();
									break;
								}
							}
				
							if ($fvi != null)
							{
								$fvi->ID = "fvi_" . $instTargetAttribute->GetInstanceID();
								$fvi->Title = $instTargetAttribute->ToString();
								$fvi->Value = $instPrimary->GetAttributeValue($instTargetAttribute);
								$fv->Items[] = $fvi;
							}
						}
							
						$ctl = $fv;
					}
					break;
				}
				case "TabContainerPageComponent":
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
					break;
				}
				case "AccordionPageComponent":
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
					break;
				}
				case "ParagraphPageComponent":
				{
					$para = new Paragraph();
					
					$rel = $instPageComponent->GetRelationship(KnownRelationships::get___Paragraph_Page_Component__has_text__Translatable_Text_Constant());
					if ($rel != null)
					{
						$inst = $rel->GetDestinationInstance();
						$para->Content = $inst->ToString();
					}
					$ctl = $para;
					break;
				}
				case "HeadingPageComponent":
				{
					$hdr = new Heading();
					$hdr->Level = $instPageComponent->GetAttributeValue("Level");
			
					$rel = $instPageComponent->GetRelationship(KnownRelationships::get___Heading_Page_Component__has_text__Translatable_Text_Constant());
					if ($rel != null)
					{
						$inst = $rel->GetDestinationInstance();
						if ($inst != null)
						{
							$hdr->Content = $inst->ToString();
						}
					}
					$ctl = $hdr;
					break;
				}
				case "PanelPageComponent":
				{
					$pnl = new Panel();
					$relTitle = $instPageComponent->GetRelationship(KnownRelationships::get___Panel_Page_Component__has_title__Translatable_Text_Constant());
					if ($relTitle != null)
					{
						$instTitle = $relTitle->GetDestinationInstance();
						if ($instTitle != null)
						{
							$pnl->Title = $instTitle->ToString();
						}
					}
					$ctl = $pnl;
					break;
				}
				case "ImagePageComponent":
				{
					$img = new Image();
					
					$relTargetFile = $instPageComponent->GetRelationship(KnownRelationships::get___Image_Page_Component__has_target__File());
					if ($relTargetFile != null)
					{
						$instTargetFile = $relTargetFile->GetDestinationInstance();
						if ($instTargetFile != null)
						{
							$img->ImageUrl = "data:image/png;base64," . $instTargetFile->GetAttributeValue(KnownAttributes::get___Text___Value());
						}
					}
					else
					{
						$img->ImageUrl = $instPageComponent->GetAttributeValue("ImageURL");
					}
					$ctl = $img;
					break;
				}
				case "SequentialContainerPageComponent":
				{
					$orientation = "Vertical";
					$rel = $instPageComponent->GetRelationship(KnownRelationships::get___Sequential_Container_Page_Component__has__Sequential_Container_Orientation());
					if ($rel != null)
					{
						$inst = $rel->GetDestinationInstance();
						if ($inst != null)
						{
								
						}
					}
						
					$divSequentialContainer = new HTMLControl("div");
					$divSequentialContainer->ClassList[] = "SequentialContainer";
					$divSequentialContainer->Attributes[] = new WebControlAttribute("data-orientation", $orientation);
						
					$ctl = $divSequentialContainer;
					break;
				}
				default:
				{
					$comment = new HTMLControl("div");
					$comment->Content = print_r($instPageComponent, true);
					$ctl = $comment;
					break;
				}
			}
			
			switch ($instPageComponent->ParentObject->Name)
			{
				case "PanelPageComponent":
				{
					$relHeaderComponents = $instPageComponent->GetRelationship(KnownRelationships::get___Panel_Page_Component__has_header__Page_Component());
					if ($relHeaderComponents != null)
					{
						$instHeaderComponents = $relHeaderComponents->GetDestinationInstances();
						foreach ($instHeaderComponents as $instComponent1)
						{
							$ctl1 = $this->CreateControlFromPageComponent($json, $instComponent1);
							$ctl->HeaderControls[] = $ctl1;
						}
					}

					$relContentComponents = $instPageComponent->GetRelationship(KnownRelationships::get___Panel_Page_Component__has_content__Page_Component());
					if ($relContentComponents != null)
					{
						$instContentComponents = $relContentComponents->GetDestinationInstances();
						foreach ($instContentComponents as $instComponent1)
						{
							$ctl1 = $this->CreateControlFromPageComponent($json, $instComponent1);
							$ctl->ContentControls[] = $ctl1;
						}
					}

					$relFooterComponents = $instPageComponent->GetRelationship(KnownRelationships::get___Panel_Page_Component__has_footer__Page_Component());
					if ($relFooterComponents != null)
					{
						$instFooterComponents = $relFooterComponents->GetDestinationInstances();
						foreach ($instFooterComponents as $instComponent1)
						{
							$ctl1 = $this->CreateControlFromPageComponent($json, $instComponent1);
							$ctl->FooterControls[] = $ctl1;
						}
					}
					break;
				}
				default:
				{
					$relComponents = $instPageComponent->GetRelationship(KnownRelationships::get___Container_Page_Component__has__Page_Component());
					if ($relComponents != null)
					{
						$instComponents = $relComponents->GetDestinationInstances();
						foreach ($instComponents as $instComponent1)
						{
							$ctl1 = $this->CreateControlFromPageComponent($json, $instComponent1);
							$ctl->Controls[] = $ctl1;
						}
					}
					break;
				}
			}
			
			if ($ctl != null)
			{
				$ctl->ClassList[] = "Mocha-Instance";
				$ctl->Attributes[] = new WebControlAttribute("data-instance-id", $instPageComponent->GetInstanceID());
				
				$relStyles = $instPageComponent->GetRelationship(KnownRelationships::get___Page_Component__has__Style());
				if ($relStyles != null)
				{
					$instStyles = $relStyles->GetDestinationInstances();
					foreach ($instStyles as $instStyle)
					{
						$ctl->ClassList[] = $instStyle->GetAttributeValue(KnownAttributes::get___Text___CSSClassName());
				
						$rel = $instStyle->GetRelationship(KnownRelationships::get___Style__has__Style_Rule());
						if ($rel != null)
						{
							$instRules = $rel->GetDestinationInstances();
							foreach ($instRules as $instRule)
							{
								$relStyleProperty = $instRule->GetRelationship(KnownRelationships::get___Style_Rule__has__Style_Property());
								if ($relStyleProperty != null)
								{
									$instStyleProperty = $relStyleProperty->GetDestinationInstance();
									if ($instStyleProperty != null)
									{
										$cssPropertyName = $instStyleProperty->GetAttributeValue(KnownAttributes::get___Text___CSSValue());
										$cssPropertyValue = $instRule->GetAttributeValue(KnownAttributes::get___Text___Value());
										$ctl->StyleRules[] = new WebStyleSheetRule($cssPropertyName, $cssPropertyValue);
									}
								}
							}
						}
					}
				}
			}
			return $ctl;
		}
		
		public function OnInitializing(CancelEventArgs $e)
		{
			$iid = $this->Page->GetPathVariableValue("instanceID");
			$paramstr = $this->Page->GetPathVariableValue("paramstr");
			
			$iidParts = explode("$", $iid);
			$inst = Instance::GetByID($iidParts[1], $iidParts[0]);
			$this->Page->Title = $inst->ToString();
			
			$idwObjectTitleHeading = $e->RenderingPage->GetControlByID("idwObjectTitleHeading");
			$idwObjectTitleHeading->CurrentInstance = $inst;
			// $idwObjectTitleHeading->ShowURL = false;
			
			$layerContent = $e->RenderingPage->GetControlByID("layerContent");
			$layerFooter = $e->RenderingPage->GetControlByID("layerFooter");
			
			$fvPrompts = $e->RenderingPage->GetControlByID("fvPrompts");
			$hdrInstructions = $e->RenderingPage->GetControlByID("hdrInstructions");
			$hdrInstructions->EnableRender = false;
			
			if ($paramstr != null)
			{
				$paramstr = base64_decode($paramstr);
				$json = json_decode($paramstr);
				
				foreach ($json->Parameters as $parmInfo)
				{
					if (isset($parmInfo->ID))
					{
						$instParm = Instance::GetByGlobalIdentifier($parmInfo->ID);
					}
					else if (isset($parmInfo->IID))
					{
						$instParm = Instance::GetByInstanceID($parmInfo->IID);
					}
					if ($instParm == null) continue;
					
					if (stripos($parmInfo->Value[0], "$") === false)
					{
						$parmValue = Instance::GetByGlobalIdentifier($parmInfo->Value[0]);
					}
					else
					{
						$parmValue = Instance::GetByInstanceID($parmInfo->Value[0]);
					}
					if ($parmValue != null)
					{
						$item = new FormViewItemInstance();
						$item->ReadOnly = true;
						$item->Value = $parmValue;
					}
					else
					{
						$item = new FormViewItemLabel();
					}
					$item->Title = $instParm->ToString();
					$fvPrompts->Items[] = $item;
				}
				
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
				
				// execute method calls for Tasks that have them
				$relExecutesMethodCall = $inst->GetRelationship(KnownRelationships::get___Task__executes__Method_Call());
				if ($relExecutesMethodCall != null)
				{
					$instsExecutesMethodCall = $relExecutesMethodCall->GetDestinationInstances();
					foreach ($instsExecutesMethodCall as $instMethodCall)
					{
						Objectify::ExecuteMethodCall($instMethodCall);
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
										$item->Value = $instObjInst->GetInstanceID();
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
			
			if (count($fvPrompts->Items) == null)
			{
				$fvPrompts->EnableRender = false;
			}
			
			if ($inst->ParentObject->Name == "UITask")
			{
				// execute teh report
				$relInstructions = $inst->GetRelationship(KnownRelationships::get___Task__has_instructions__Translatable_Text_Constant());
				if ($relInstructions != null)
				{
					$instInstructions = $relInstructions->GetDestinationInstance();
					$hdrInstructions->Content = $instInstructions->ToString();
					$hdrInstructions->EnableRender = true;
				}
			}
			else if ($inst->ParentObject->Name == "File")
			{
				// download the file
				$fileName = $inst->GetAttributeValue(KnownAttributes::get___Text___Name());
				$mimetype = $inst->GetAttributeValue(KnownAttributes::get___Text___Content_Type());
				$value = $inst->GetAttributeValue(KnownAttributes::get___Text___Value());
				
				header("Content-Disposition: attachment,filename=" . $fileName);
				header("Content-Type: " . $mimetype);
				echo (base64_decode($value));
				die();
			}
			else if ($inst->ParentObject->Name == "Page")
			{
				$relStyles = $inst->GetRelationship(KnownRelationships::get___Page__has__Style());
				if ($relStyles != null)
				{
					$instStyles = $relStyles->GetDestinationInstances();
					foreach ($instStyles as $instStyle)
					{
						$propClassName = $instStyle->GetAttributeValue(KnownAttributes::get___Text___CSSClassName());
						if ($propClassName !== null) $this->Page->ClassList[] = $propClassName;
					}
				}
				
				$relHasPageComponent = $inst->GetRelationship(KnownRelationships::get___Page__has__Page_Component());
				if ($relHasPageComponent != null)
				{
					$instPageComponents = $relHasPageComponent->GetDestinationInstances();
					foreach ($instPageComponents as $instPageComponent)
					{
						$ctl = $this->CreateControlFromPageComponent($json, $instPageComponent);
						if ($ctl != null) $layerContent->Controls[] = $ctl;
					}
				}
				$layerFooter->EnableRender = false;
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
				$layerFooter->EnableRender = false;
				
				$relInstructions = $inst->GetRelationship(KnownRelationships::get___Report__has_instructions__Translatable_Text_Constant());
				if ($relInstructions != null)
				{
					$instInstructions = $relInstructions->GetDestinationInstance();
					$hdrInstructions->Content = $instInstructions->ToString();
					$hdrInstructions->EnableRender = true;
				}
				
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
				
				$td = new HTMLControl("td");
				$td->StyleRules[] = new WebStyleSheetRule("vertical-align", "top");
				
				$lvReport = new ReportListView();
				$lvReport->Report = $inst;
				
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