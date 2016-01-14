<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\System;
	use Phast\CancelEventArgs;
	
	use Phast\HTMLControl;
	use Phast\HTMLControls\Image;
	
	use Phast\WebControl;
	use Phast\WebControlAttribute;
	
	use Phast\Parser\PhastPage;
	
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstance;
use Phast\WebControls\Panel;
use Phast\HTMLControls\Header;
use Phast\HTMLControls\Heading;
					
	class PagePage extends PhastPage
	{
		/**
		 * Renders the specified "Page Component" instance.
		 * @param TenantObjectInstance $instComponent
		 * @return WebControl
		 */
		private function CreatePageComponent($instComponent)
		{
			$ctl = null;
			
			switch ($instComponent->ParentObject->Name)
			{
				case "HeadingPageComponent":
				{
					$hdr = new Heading();
					$hdr->Level = $instComponent->GetPropertyValue("Level");
					$propTitle = $instComponent->GetPropertyValue("Title");
					if ($propTitle != null)
					{
						$hdr->Content = $propTitle->GetInstances()[0]->ToString();
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
					$img->ImageUrl = $instComponent->GetPropertyValue("ImageFileName");
					$ctl = $img;
					break;
				}
				case "SequentialContainerPageComponent":
				{
					$instOrientation = $instComponent->GetPropertyValue("Orientation")->GetInstance();
					$orientation = $instOrientation->GetPropertyValue("Value");
					
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

			switch (get_class($ctl))
			{
				case "Phast\\WebControls\\Panel":
				{
					$instHeaderComponents = $instComponent->GetPropertyValue("HeaderComponents");
					if ($instHeaderComponents !== null)
					{
						$instHeaderComponents = $instHeaderComponents->GetInstances();
						foreach ($instHeaderComponents as $instComponent1)
						{
							$ctl1 = $this->CreatePageComponent($instComponent1);
							$ctl->HeaderControls[] = $ctl1;
						}
					}
					
					$instContentComponents = $instComponent->GetPropertyValue("ContentComponents");
					if ($instContentComponents !== null)
					{
						$instContentComponents = $instContentComponents->GetInstances();
						foreach ($instContentComponents as $instComponent1)
						{
							$ctl1 = $this->CreatePageComponent($instComponent1);
							$ctl->ContentControls[] = $ctl1;
						}
					}
					
					$instFooterComponents = $instComponent->GetPropertyValue("FooterComponents");
					if ($instFooterComponents !== null)
					{
						$instFooterComponents = $instFooterComponents->GetInstances();
						foreach ($instFooterComponents as $instComponent1)
						{
							$ctl1 = $this->CreatePageComponent($instComponent1);
							$ctl->ContentControls[] = $ctl1;
						}
					}
					break;
				}
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
			
			return $ctl;
		}
		
		public function OnInitializing(CancelEventArgs $e)
		{
			$objPage = TenantObject::GetByName("Page");
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
			
			$instPageStyles = $instPage->GetPropertyValue("Styles");
			if ($instPageStyles != null)
			{
				$instPageStyles = $instPageStyles->GetInstances();
				foreach ($instPageStyles as $instPageStyle)
				{
					$propClassName = $instPageStyle->GetPropertyValue("ClassName");
					if ($propClassName !== null) $this->Page->ClassList[] = $propClassName;
				}
			}
			
			$instPageComponents = $instPage->GetPropertyValue("Components");
			if ($instPageComponents != null)
			{
				$instPageComponents = $instPageComponents->GetInstances();
				
				// cycle through page components and render them
				foreach ($instPageComponents as $instPageComponent)
				{
					$ctl = $this->CreatePageComponent($instPageComponent);
					$this->Page->Controls[] = $ctl;
				}
			}
		}
	}
?>