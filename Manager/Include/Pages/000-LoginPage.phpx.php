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
	use Objectify\Objects\Relationship;
	use Objectify\Objects\Instance;
	
	use Phast\RandomStringGenerator;
	use Phast\RandomStringGeneratorCharacterSets;
					
	class LoginPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$objTenant = TenantObject::GetByName("Tenant");
			if ($objTenant != null)
			{
				$instTenants = $objTenant->GetInstanceUsingAttributes(array
				(
					new TenantObjectInstancePropertyValue("Name", System::GetTenantName())
				));
				$instTenant = $instTenants[0];

				$instRel_Tenant__has_login_header_TTC = Instance::GetByGlobalIdentifier("{41D66ACB-AFDE-4B6F-892D-E66255F10DEB}");
				$instRel_Tenant__has_login_footer_TTC = Instance::GetByGlobalIdentifier("{A6203B6B-5BEB-4008-AE49-DB5E7DDBA45B}");
				
				$paraTopText = $e->RenderingPage->GetControlByID("paraTopText");

				$relsHeader = Relationship::GetBySourceInstance($instTenant, $instRel_Tenant__has_login_header_TTC);
				if ($relsHeader != null && count($relsHeader) > 0) {
					$relsHeaderInsts = $relsHeader[0]->GetDestinationInstances();
					
					// $instLoginHeaderText = $instTenant->GetPropertyValue("LoginHeaderText", "")->GetInstance();
					$instLoginHeaderText = $relsHeaderInsts[0];
					
					$paraTopText->Content = $instLoginHeaderText->ToString();
				}
				
				$paraBottomText = $e->RenderingPage->GetControlByID("paraBottomText");
				
				$relsFooter = Relationship::GetBySourceInstance($instTenant, $instRel_Tenant__has_login_footer_TTC);
				if ($relsFooter != null && count($relsFooter) > 0) {
					$relsFooterInsts = $relsFooter[0]->GetDestinationInstances();
					
					// $instLoginFooterText = $instTenant->GetPropertyValue("LoginFooterText", "")->GetInstance();
					$instLoginFooterText = $relsFooterInsts[0];
					
					$paraBottomText->Content = $instLoginFooterText->ToString();
				}
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