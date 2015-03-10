<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\System;
	
	use Objectify\Tenant\MasterPages\WebPage;
	use Objectify\Objects\Tenant;
	use Phast\Parser\PhastPage;
	use Phast\CancelEventArgs;
	
	class LoginPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				if (isset($_POST["user_LoginID"]) && isset($_POST["user_Password"]))
				{
					$admun = $_POST["user_LoginID"];
					$admpw = $_POST["user_Password"];
			
					if (CheckCredentials($admun, $admpw))
					{
						$_SESSION["Authentication.UserName"] = $admun;
						$_SESSION["Authentication.Password"] = $admpw;
						
						if (isset($_SESSION["LoginRedirectURL"]))
						{
							System::Redirect($_SESSION["LoginRedirectURL"]);
						}
						else
						{
							System::Redirect("~/");
						}
						return true;
					}
					else
					{
						$this->Page->GetControlByID("alertInvalidCredentials")->EnableRender = true;
					}
				}
			}
		}
	}
	class LogoutPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$_SESSION["Authentication.UserName"] = null;
			$_SESSION["Authentication.Password"] = null;
			System::Redirect("~/");
		}
	}
?>