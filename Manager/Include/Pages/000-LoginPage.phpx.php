<?php
	namespace Objectify\Tenant\Pages;
	
	use Phast\CancelEventArgs;
	use Phast\System;
	use Phast\WebScript;
	
	use Phast\Parser\PhastPage;
	
	use Objectify\Objects\Instance;
	use Objectify\Objects\KnownAttributes;
	use Objectify\Objects\KnownObjects;
	use Objectify\Objects\KnownRelationships;
	use Objectify\Objects\Objectify;
	use Objectify\Objects\Relationship;
	use Objectify\Objects\Tenant;
	use Objectify\Objects\TenantObjectInstancePropertyValue;
	use Objectify\Objects\User;
	
	use Phast\RandomStringGenerator;
	use Phast\RandomStringGeneratorCharacterSets;
	
	class LoginPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$objTenant = KnownObjects::get___Tenant();
			if ($objTenant != null)
			{
				$instTenant = $objTenant->GetInstances()[0];
				
				$litTenantType = $e->RenderingPage->GetControlByID("litTenantType");
				$litTenantType->Value = Objectify::GenerateTenantBadgeHTML($instTenant);
				
				$imageHeader = $e->RenderingPage->GetControlByID("imageHeader");
				$instLogoImage = $instTenant->GetRelatedInstance(KnownRelationships::get___Tenant__has_logo_image__File());
				if ($instLogoImage != null)
				{
					$value = $instLogoImage->GetAttributeValue(KnownAttributes::get___Text___Value());
					$imageHeader->ImageUrl = "data:image/png;base64," . $value;
				}
				
				$instRel_Tenant__has_login_header_TTC = Instance::GetByGlobalIdentifier("{41D66ACB-AFDE-4B6F-892D-E66255F10DEB}");
				$instRel_Tenant__has_login_footer_TTC = Instance::GetByGlobalIdentifier("{A6203B6B-5BEB-4008-AE49-DB5E7DDBA45B}");
				
				$paraTopText = $e->RenderingPage->GetControlByID("paraTopText");

				$instLoginHeaderText = $instTenant->GetRelatedInstance($instRel_Tenant__has_login_header_TTC);
				if ($instLoginHeaderText != null) $paraTopText->Content = $instLoginHeaderText->ToString();
				
				$paraBottomText = $e->RenderingPage->GetControlByID("paraBottomText");
				
				$instLoginFooterText = $instTenant->GetRelatedInstance($instRel_Tenant__has_login_footer_TTC);
				if ($instLoginFooterText != null) $paraBottomText->Content = $instLoginFooterText->ToString();
			}
			
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				if (isset($_POST["user_LoginID"]) && isset($_POST["user_Password"]))
				{
					$username = $_POST["user_LoginID"];
					$password = $_POST["user_Password"];
					
					$objUser = KnownObjects::get___User();
					$inst = null;
					if ($objUser != null)
					{
						$insts = $objUser->GetInstanceUsingAttributes(array
						(
							new TenantObjectInstancePropertyValue("UserName", $username)
						));
						$inst = $insts[0];
					}
					
					if ($inst != null)
					{
						// we have an instance, validate the password
						$attPasswordSalt = Instance::GetByGlobalIdentifier("{8C5A99BC-40ED-4FA2-B23F-F373C1F3F4BE}");
						$attPasswordHash = Instance::GetByGlobalIdentifier("{F377FC29-4DF1-4AFB-9643-4191F37A00A9}");
						
						$passwordSalt = $inst->GetAttributeValue($attPasswordSalt);
						$passwordHash = $inst->GetAttributeValue($attPasswordHash);
						
						$expectedPasswordHash = hash("sha512", $password . $passwordSalt);
						
						if ($passwordHash != $expectedPasswordHash)
						{
							$inst = null;
						}
					}
					
					if ($inst != null)
					{
						$user_LoginToken = RandomStringGenerator::Generate(RandomStringGeneratorCharacterSets::AlphaNumericMixedCase, 32);
						
						$tenant = Tenant::GetCurrent();
						$_SESSION[$tenant->Name . ":Authentication.LoginToken"] = $user_LoginToken;
						
						$objUserLogin = KnownObjects::get___User_Login();
						$instUserLogin = $objUserLogin->CreateInstance();
						
						$instUserLogin->SetAttributeValue("Token", $user_LoginToken);
						$instUserLogin->SetAttributeValue("StartDate", new \DateTime());
						$instUserLogin->SetAttributeValue("IPAddress", $_SERVER["REMOTE_ADDR"]);
						
						Relationship::Create(KnownRelationships::get___User_Login__has__User(), $instUserLogin, array($inst));
						Relationship::Create(KnownRelationships::get___User__for__User_Login(), $inst, array($instUserLogin));
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
						System::RedirectFromLoginPage();
						return true;
					}
					else
					{
						$e->RenderingPage->GetControlByID("fv")->GetItemByID("txtUserName")->Value = $_POST["user_LoginID"];
						
						$this->Page->Scripts[] = WebScript::FromContent("window.addEventListener('load', function() { Notification.Show('Please ensure your CAPS LOCK and NUM LOCK keys are inactive and try again', 'Incorrect user name or password', 'Danger'); });");
					}
				}
			}
		}
	}
	class LogoutPage extends PhastPage
	{
		public function OnInitializing(CancelEventArgs $e)
		{
			$tenant = Tenant::GetCurrent();
			unset($_SESSION[$tenant->Name . ":Authentication.LoginToken"]);
			unset($_SESSION[$tenant->Name . ":LoginRedirectURL"]);
			System::Redirect("~/");
		}
	}
?>