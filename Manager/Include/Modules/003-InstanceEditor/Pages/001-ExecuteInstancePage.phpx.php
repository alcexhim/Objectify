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
	
	class ExecuteInstancePage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$iid = $this->Page->GetPathVariableValue("instanceID");
			$paramstr = $this->Page->GetPathVariableValue("paramstr");
			
			$iidParts = explode("$", $iid);
			$inst = Instance::GetByID($iidParts[1]);
			
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
				$headTitle = $e->RenderingPage->GetControlByID("headTitle");
				$rel = $inst->GetRelationship(KnownRelationships::get___Task__has_title__Translatable_Text_Constant());
				$instTitle = $rel->GetDestinationInstance();
				$headTitle->Content = $instTitle->ToString();
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
				$headTitle = $e->RenderingPage->GetControlByID("headTitle");
				$rel = $inst->GetRelationship(KnownRelationships::get___Report__has_title__Translatable_Text_Constant());
				$instTitle = $rel->GetDestinationInstance();
				$headTitle->Content = $instTitle->ToString();

				$lvReport = $e->RenderingPage->GetControlByID("lvReport");
				$lvReport->EnableRender = true;
				
				$rel = $inst->GetRelationship(KnownRelationships::get___Report__has__Report_Field());
				if ($rel != null)
				{
					$insts = $rel->GetDestinationInstances();
					foreach ($insts as $inst)
					{
						$lvReport->Columns[] = new ListViewColumn("ch" . $inst->ID, $inst->ToString());
					}
					// print_r($insts);
				}
			}
			else
			{
				print_r($inst);
				die();
			}
		}
	}
?>