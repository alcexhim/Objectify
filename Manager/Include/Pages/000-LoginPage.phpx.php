<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\CancelEventArgs;
	use Phast\System;
	
	use Phast\Parser\PhastPage;
	
	use Objectify\Tenant\MasterPages\WebPage;
	use Objectify\Objects\Tenant;
	use Objectify\Objects\User;
	
	class LoginPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				if (isset($_POST["user_LoginID"]) && isset($_POST["user_Password"]))
				{
					$username = $_POST["user_LoginID"];
					$password = $_POST["user_Password"];
					
					$user = User::GetByCredentials($username, $password);
					
					if ($user != null)
					{
						if (!$user->RequestLoginToken())
						{
							$e->RenderingPage->GetControlByID("fv")->GetItemByID("txtUserName")->Value = $_POST["user_LoginID"];
							$e->RenderingPage->GetControlByID("alertInvalidCredentials")->EnableRender = true;
							return true;
						}
						
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
						$e->RenderingPage->GetControlByID("fv")->GetItemByID("txtUserName")->Value = $_POST["user_LoginID"];
						$e->RenderingPage->GetControlByID("alertInvalidCredentials")->EnableRender = true;
					}
				}
			}
		}
	}
	class LogoutPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			User::ReleaseLoginToken();
			System::Redirect("~/");
		}
	}
?>