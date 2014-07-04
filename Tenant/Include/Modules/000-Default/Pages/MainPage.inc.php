<?php
	namespace Objectify\Pages;
	
	use WebFX\Controls\ButtonGroup;
	use WebFX\Controls\ButtonGroupButton;
	use WebFX\Controls\ButtonGroupButtonAlignment;
	
	use WebFX\Controls\ActionList;
	use WebFX\Controls\ActionListItem;
	use WebFX\Controls\ActionListCommand;
	use WebFX\Controls\ActionListSeparator;
	
	use WebFX\Controls\Panel;
	
	use Objectify\MasterPages\WebPage;
	
	use Objectify\Objects\Tenant;
	use Objectify\Objects\TenantObjectMethodParameterValue;
	
	use Objectify\Objects\User;
	use Objectify\Objects\UserProfileVisibility;
	
	use WebFX\System;
	use WebFX\Module;
	use WebFX\ModulePage;

	class MainPage extends WebPage
	{
		protected function RenderContent()
		{
			$actionList = new ActionList("alMain");
			$actionList->Items[] = new ActionListCommand("actNew", "<strong>Create</strong> a new document");
			$actionList->Items[] = new ActionListCommand("actOpen", "<strong>Open</strong> an existing document");
			$actionList->Items[] = new ActionListCommand("actConvert", "<strong>Convert</strong> a document from one data format to another");
			$actionList->Items[] = new ActionListSeparator();
			$actionList->Items[] = new ActionListCommand("actAbout", "<strong>About</strong> Universal Editor in the Cloud");
			$actionList->Render();
		}
	}

	System::$Modules[] = new Module("net.Objectify.Default.MainPage", array
	(
		new ModulePage("", function($path)
		{
			if (IsAuthenticated())
			{
				$tenant = Tenant::GetCurrent();
				$tobjUser = $tenant->GetObject("User");
				$instUser = $tobjUser->GetMethod("GetCurrentUser")->Execute();
				
				$propStartPage = $tobjUser->GetInstanceProperty("StartPage");
				
				$startPageSet = $instUser->HasPropertyValue($propStartPage);
				$startPage = $instUser->GetPropertyValue($propStartPage);
				
				if ($startPageSet)
				{
					/*
					$spi = $startPage->Instance;
					$spio = $startPage->Instance->ParentObject;
					$startPage = $spi->GetPropertyValue($spio->GetProperty("Value"));
					*/
					System::Redirect($startPage);
				}
				else
				{
					System::Redirect("~/dashboard");
				}
				return true;
			}
			
			$page = new MainPage();
			$page->Render();
			return true;
		})
	));
?>