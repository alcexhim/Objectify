<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\CancelEventArgs;
	use Phast\System;
	
	use Phast\Parser\PhastPage;
	
	use Objectify\Tenant\MasterPages\WebPage;
	use Objectify\Objects\Tenant;
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstancePropertyValue;
	use Objectify\Objects\User;
	use Objectify\Objects\MultipleInstanceProperty;
	use Objectify\Objects\SingleInstanceProperty;

	use Phast\RandomStringGenerator;
	use Phast\RandomStringGeneratorCharacterSets;
			
	class LoginPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$objTenant = TenantObject::GetByName("Tenant");
			if ($objTenant != null)
			{
				$instTenant = $objTenant->GetInstance(array
				(
					new TenantObjectInstancePropertyValue("TenantURL", System::GetTenantName())
				));
				
				$paraTopText = $e->RenderingPage->GetControlByID("paraTopText");
				
				$instsLoginHeaderText = $instTenant->GetPropertyValue("LoginHeaderText", "")->GetInstances();
				$paraTopText->Content = $instsLoginHeaderText[0]->ToString();
				
				$paraBottomText = $e->RenderingPage->GetControlByID("paraBottomText");
				$instsLoginFooterText = $instTenant->GetPropertyValue("LoginFooterText", "")->GetInstances();
				$paraBottomText->Content = $instsLoginFooterText[0]->ToString();
			}
			
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				if (isset($_POST["user_LoginID"]) && isset($_POST["user_Password"]))
				{
					$username = $_POST["user_LoginID"];
					$password = $_POST["user_Password"];
					
					$objUser = TenantObject::GetByName("User");
					$inst = null;
					if ($objUser != null)
					{
						$inst = $objUser->GetInstance(array
						(
							new TenantObjectInstancePropertyValue("UserName", $username)
						));
					}
					
					if ($inst != null)
					{
						// we have an instance, validate the password
						$passwordSalt = $inst->GetPropertyValue("PasswordSalt");
						$passwordHash = $inst->GetPropertyValue("PasswordHash");
						
						$expectedPasswordHash = hash("sha512", $password . $passwordSalt);
						
						if ($passwordHash != $expectedPasswordHash)
						{
							$inst = null;
						}
					}
					
					if ($inst != null)
					{
						$user_LoginToken = RandomStringGenerator::Generate(RandomStringGeneratorCharacterSets::AlphaNumericMixedCase, 32);
						$_SESSION["Authentication.LoginToken"] = $user_LoginToken;
						
						$objUserLogin = TenantObject::GetByName("UserLogin");
						$objUserLogin->CreateInstance(array
						(
							new TenantObjectInstancePropertyValue("Token", $user_LoginToken),
							new TenantObjectInstancePropertyValue("SignonTime", (new \DateTime())),
							new TenantObjectInstancePropertyValue("User", new SingleInstanceProperty($inst, array($objUser))),
							new TenantObjectInstancePropertyValue("SignoffTime", null),
							new TenantObjectInstancePropertyValue("DeviceType", new SingleInstanceProperty(null, array($objDeviceType))),
							new TenantObjectInstancePropertyValue("IPAddress", $_SERVER["REMOTE_ADDR"])
						));
					}
					
					$user = User::GetCurrent(); // User::GetByCredentials($username, $password);
					
					if ($user != null)
					{
						/*
						if (!$user->RequestLoginToken())
						{
							$e->RenderingPage->GetControlByID("fv")->GetItemByID("txtUserName")->Value = $_POST["user_LoginID"];
							$e->RenderingPage->GetControlByID("alertInvalidCredentials")->EnableRender = true;
							return true;
						}
						*/
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
			
			unset($_SESSION["LoginRedirectURL"]);
			System::Redirect("~/");
		}
	}
?>