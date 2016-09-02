<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\CancelEventArgs;
	
	use Phast\HTMLControl;
	use Phast\HTMLControls\Image;
	use Phast\HTMLControls\Heading;
	use Phast\HTMLControls\Paragraph;
	
	use Phast\WebControl;
	use Phast\WebControls\Panel;
	
	use Phast\WebControlAttribute;
	use Phast\WebStyleSheetRule;
	
	use Phast\Parser\PhastPage;
	
	use Objectify\Objects\Instance;
	use Objectify\Objects\KnownAttributes;
	use Objectify\Objects\KnownObjects;
	use Objectify\Objects\KnownRelationships;
	use Objectify\Objects\Relationship;
	
	class PagePage extends PhastPage
	{
		/**
		 * Renders the specified "Page Component" instance.
		 * @param Instance $instComponent
		 * @return WebControl
		 */
		private function CreatePageComponent($instComponent)
		{
			$ctl = null;
			
			switch ($instComponent->ParentObject->Name)
			{
				case "ParagraphPageComponent":
				{
					$para = new Paragraph();
					
					$rels = Relationship::GetBySourceInstance($instComponent, KnownRelationships::get___Paragraph_Page_Component__has_text__Translatable_Text_Constant());
					$rel = $rels[0];
					if ($rel != null)
					{
						$insts = $rel->GetDestinationInstances();
						$inst = $insts[0];
						
						$para->Content = $inst->ToString();
					}
					$ctl = $para;
					break;
				}
				case "HeadingPageComponent":
				{
					$hdr = new Heading();
					$hdr->Level = $instComponent->GetAttributeValue("Level");

					$rels = Relationship::GetBySourceInstance($instComponent, KnownRelationships::get___Heading_Page_Component__has_text__Translatable_Text_Constant());
					$rel = $rels[0];
					if ($rel != null)
					{
						$insts = $rel->GetDestinationInstances();
						$inst = $insts[0];
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
					$propTitle = $instComponent->GetPropertyValue("Title");
					if ($propTitle != null)
					{
						$pnl->Title = $propTitle->GetInstances()[0]->ToString();
					}
					$ctl = $pnl;
					break;
				}
				case "ImagePageComponent":
				{
					$img = new Image();
					$img->ImageUrl = $instComponent->GetAttributeValue("ImageURL");
					$ctl = $img;
					break;
				}
				case "SequentialContainerPageComponent":
				{
					$orientation = "Vertical";
					$rels = Relationship::GetBySourceInstance($instComponent, KnownRelationships::get___Sequential_Container_Page_Component__has__Sequential_Container_Orientation());
					$rel = $rels[0];
					if ($rel != null)
					{
						$insts = $rel->GetDestinationInstances();
						$inst = $insts[0];
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
					$comment->Content = print_r($instComponent, true);
					$ctl = $comment;
					break;
				}
			}

			switch ($instComponent->ParentObject->Name)
			{
				case "PanelPageComponent":
				{
					$relsHeaderComponents = Relationship::GetBySourceInstance($instComponent, KnownRelationships::get___Panel_Page_Component__has_header__Page_Component());
					$relHeaderComponents = $relsHeaderComponents[0];
					if ($relHeaderComponents != null)
					{
						$instHeaderComponents = $relHeaderComponents->GetDestinationInstances();
						foreach ($instHeaderComponents as $instComponent1)
						{
							$ctl1 = $this->CreatePageComponent($instComponent1);
							$ctl->HeaderControls[] = $ctl1;
						}
					}

					$relsContentComponents = Relationship::GetBySourceInstance($instComponent, KnownRelationships::get___Panel_Page_Component__has_content__Page_Component());
					$relContentComponents = $relsContentComponents[0];
					if ($relContentComponents != null)
					{
						$instContentComponents = $relContentComponents->GetDestinationInstances();
						foreach ($instContentComponents as $instComponent1)
						{
							$ctl1 = $this->CreatePageComponent($instComponent1);
							$ctl->ContentControls[] = $ctl1;
						}
					}

					$relsFooterComponents = Relationship::GetBySourceInstance($instComponent, KnownRelationships::get___Panel_Page_Component__has_footer__Page_Component());
					$relFooterComponents = $relsFooterComponents[0];
					if ($relFooterComponents != null)
					{
						$instFooterComponents = $relFooterComponents->GetDestinationInstances();
						foreach ($instFooterComponents as $instComponent1)
						{
							$ctl1 = $this->CreatePageComponent($instComponent1);
							$ctl->FooterControls[] = $ctl1;
						}
					}
					break;
				}
				case "SequentialContainerPageComponent":
				{
					$relsComponents = Relationship::GetBySourceInstance($instComponent, KnownRelationships::get___Sequential_Container_Page_Component__has__Page_Component());
					$relComponents = $relsComponents[0];
					if ($relComponents != null)
					{
						$instComponents = $relComponents->GetDestinationInstances();
						foreach ($instComponents as $instComponent1)
						{
							$ctl1 = $this->CreatePageComponent($instComponent1);
							$ctl->Controls[] = $ctl1;
						}
					}
					break;
				}
			}
			
			switch (get_class($ctl))
			{
				default:
				{
					$instComponents = $instComponent->GetPropertyValue("Components");
					if ($instComponents !== null)
					{
						$instComponents = $instComponents->GetInstances();
						foreach ($instComponents as $instComponent1)
						{
							$ctl1 = $this->CreatePageComponent($instComponent1);
							$ctl->Controls[] = $ctl1;
						}
					}
					break;
				}
			}
			
			if ($ctl != null)
			{
				$ctl->Attributes[] = new WebControlAttribute("data-instance-id", $instComponent->GetInstanceID());
				
				$relsStyles = Relationship::GetBySourceInstance($instComponent, KnownRelationships::get___Page_Component__has__Style());
				$relStyles = $relsStyles[0];
				if ($relStyles != null)
				{
					$instStyles = $relStyles->GetDestinationInstances();
					foreach ($instStyles as $instStyle)
					{
						$ctl->ClassList[] = $instStyle->GetPropertyValue("ClassName");
						
						$rels = Relationship::GetBySourceInstance($instStyle, KnownRelationships::get___Style__has__Style_Rule());
						$rel = $rels[0];
						if ($rel != null)
						{
							$instRules = $rel->GetDestinationInstances();
							if ($instRules != null)
							{
								foreach ($instRules as $instRule)
								{
									$relsStyleProperty = Relationship::GetBySourceInstance($instRule, KnownRelationships::get___Style_Rule__has__Style_Property());
									$relStyleProperty = $relsStyleProperty[0];
									if ($relStyleProperty != null)
									{
										$instStyleProperty = $relStyleProperty->GetDestinationInstances();
										$instStyleProperty = $instStyleProperty[0];
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
			}
			
			return $ctl;
		}
		
		public function OnInitializing(CancelEventArgs $e)
		{
			$objPage = KnownObjects::get___Page();
			$pageID = $this->Page->GetPathVariableValue("pageID");
			$instPage = $objPage->GetInstanceByInstanceID($pageID);
			
			if ($instPage == null)
			{
				echo("Resource not found - 404");
				return;
			}
			if ($instPage->ParentObject->Name != "Page")
			{
				echo ($instPage->ParentObject->Name . " is not a Page");
				return;
			}
			
			$relsStyles = Relationship::GetBySourceInstance($instPage, KnownRelationships::get___Page__has__Style());
			$relStyles = $relsStyles[0];
			if ($relStyles != null)
			{
				$instStyles = $relStyles->GetDestinationInstances();
				foreach ($instStyles as $instStyle)
				{
					$propClassName = $instStyle->GetPropertyValue("ClassName");
					if ($propClassName !== null) $this->Page->ClassList[] = $propClassName;
				}
			}
			
			$relsComponents = Relationship::GetBySourceInstance($instPage, KnownRelationships::get___Page__has__Page_Component());
			$relComponents = $relsComponents[0];
			if ($relComponents != null)
			{
				$instComponents = $relComponents->GetDestinationInstances();
				// cycle through page components and render them
				foreach ($instComponents as $instComponent)
				{
					$ctl = $this->CreatePageComponent($instComponent);
					$this->Page->Controls[] = $ctl;
				}
			}
		}
	}
?>