<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\Parser\PhastPage;
	use Phast\System;
	
	use Objectify\Objects\Instance;
	use Objectify\Objects\User;
	
	class SettingsPage extends PhastPage
	{
		public function OnInitializing($e)
		{
			$inst = Instance::GetByGlobalIdentifier("{96044B18-86E4-4D84-90E6-CB16361C4BE8}");
			
			$instUser = User::GetCurrent();
			
			$instParm = Instance::GetByGlobalIdentifier("{95305300-A200-4773-9CB3-7FA985039B76}");
			
			$params = "{\"Parameters\":[{\"IID\":\"" . $instParm->GetInstanceID() . "\",\"Value\":[\"" . $instUser->GetInstanceID() . "\"]}]}";
			$encodedParams = base64_encode($params);
			
			System::Redirect("~/instances/execute/" . $inst->GetInstanceID() . "/" . $encodedParams);
			die();
		}
	}
?>