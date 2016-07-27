<?php
	namespace Objectify\Manager\Modules\InstanceEditor\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	use Phast\System;
	
	use Objectify\Objects\Instance;
	use Objectify\Objects\KnownAttributes;
		
	class ExecuteInstancePage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$iid = $this->Page->GetPathVariableValue("instanceID");
			$iidParts = explode("$", $iid);
			$inst = Instance::GetByID($iidParts[1]);
			
			if ($inst->ParentObject->Name == "RedirectTask")
			{
				$targetURL = $inst->GetAttributeValue(KnownAttributes::get___Text___Target_URL());
				System::Redirect($targetURL);
				die();
			}
			
			print_r($inst);
			die();
		}
	}
?>