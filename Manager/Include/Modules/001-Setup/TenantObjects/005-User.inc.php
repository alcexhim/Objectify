<?php

	use Phast\CancelEventArgs;
	use Phast\Parser\PhastPage;
	use Phast\Data\DataSystem;
	use Phast\System;
	use Phast\RandomStringGenerator;
	use Phast\RandomStringGeneratorCharacterSets;
	
	use Objectify\Objects\DataType;
	use Objectify\Objects\TenantObject;
	use Objectify\Objects\TenantObjectInstancePropertyValue;
	use Objectify\Objects\TenantObjectMethodParameter;
	use Objectify\Objects\MultipleInstanceProperty;
	use Objectify\Objects\SingleInstanceProperty;
	
	$objDeviceType = TenantObject::Create("DeviceType", $objObject);
	$instLanguageString_English_DeviceType_Title = $objLanguageString->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue("Language", new SingleInstanceProperty($instLanguageEnglish, array($objLanguage))),
		new TenantObjectInstancePropertyValue("Value", "Device Type")
	));
	$objDeviceType->SetPropertyValue("Title", new MultipleInstanceProperty(array
	(
		$instLanguageString_English_DeviceType_Title
	), array($objLanguageString)));

	$objAuthenticationType = TenantObject::Create("AuthenticationType", $objObject);
	$instLanguageString_English_AuthenticationType_Title = $objLanguageString->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue("Language", new SingleInstanceProperty($instLanguageEnglish, array($objLanguage))),
		new TenantObjectInstancePropertyValue("Value", "Authentication Type")
	));
	$objAuthenticationType->SetPropertyValue("Title", new MultipleInstanceProperty(array
	(
		$instLanguageString_English_AuthenticationType_Title
	), array($objLanguageString)));
	
	$objUserLogin = TenantObject::Create("UserLogin", $objObject);
	$objUserLogin->CreateInstanceProperty("Token", DataType::GetByName("Text"));
	$objUserLogin->CreateInstanceProperty("SignonTime", DataType::GetByName("DateTime"));
	$objUserLogin->CreateInstanceProperty("User", DataType::GetByName("SingleInstance"), new SingleInstanceProperty(null, array($objUser)));
	$objUserLogin->CreateInstanceProperty("SignoffTime", DataType::GetByName("DateTime"));
	$objUserLogin->CreateInstanceProperty("DeviceType", DataType::GetByName("SingleInstance"), new SingleInstanceProperty(null, array($objDeviceType)));
	$objUserLogin->CreateInstanceProperty("IPAddress", DataType::GetByName("Text"));

	$instLanguageString_English_UserLogin_Title = $objLanguageString->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Language"), new SingleInstanceProperty($instLanguageEnglish, array($objLanguage))),
		new TenantObjectInstancePropertyValue($objLanguageString->GetInstanceProperty("Value"), "User Login")
	));
	$objUserLogin->SetPropertyValue("Title", new MultipleInstanceProperty(array
	(
		$instLanguageString_English_UserLogin_Title
	), array($objLanguageString)));

	$objUser = TenantObject::Create("User", $objObject);
	$objUser->CreateInstanceProperty("UserName", DataType::GetByName("Text"));
	$objUser->CreateInstanceProperty("PasswordHash", DataType::GetByName("Text"));
	$objUser->CreateInstanceProperty("PasswordSalt", DataType::GetByName("Text"));
	$objUser->CreateInstanceProperty("IsGlobal", DataType::GetByName("Boolean"));
	$objUser->CreateInstanceProperty("Logins", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(null, array($objUserLogin)));
	$objUser->CreateInstanceProperty("SecurityGroups", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(null, array($objSecurityGroup)));
	
	$objUser->CreateInstanceMethod("RequestLoginToken", array(), <<<'EOF'
	
$propLogins = $thisInstance->GetPropertyValue("Logins");
if (!is_array($propLogins)) return false;

$user_LoginToken = RandomStringGenerator::Generate(RandomStringGeneratorCharacterSets::AlphaNumericMixedCase, 32);

// create an instance of UserLogin
$objUserLogin = TenantObject::GetByName("UserLogin");

$instUserLogin = $objUserLogin->CreateInstance(array
(
	new TenantObjectInstancePropertyValue
	(
		$objUserLogin->GetInstanceProperty("Token"),
		$user_LoginToken
	),
	new TenantObjectInstancePropertyValue
	(
		$objUserLogin->GetInstanceProperty("IPAddress"),
		$_SERVER["REMOTE_ADDR"]
	)
));
if ($instUserLogin !== null)
{
	$propLogins[] = $instUserLogin;
	$thisInstance->SetPropertyValue("Logins", $propLogins);
	
	$_SESSION["Authentication.LoginToken"] = $user_LoginToken;
	return true;
}
EOF
	);
	
	$instUser = $objUser->CreateInstance(array
	(
		new TenantObjectInstancePropertyValue
		(
			$objUser->GetInstanceProperty("UserName"),
			$Administrator_UserName
		),
		new TenantObjectInstancePropertyValue
		(
			$objUser->GetInstanceProperty("PasswordHash"),
			$Administrator_PasswordHash
		),
		new TenantObjectInstancePropertyValue
		(
			$objUser->GetInstanceProperty("PasswordSalt"),
			$Administrator_PasswordSalt
		),
		new TenantObjectInstancePropertyValue
		(
			$objUser->GetInstanceProperty("IsGlobal"),
			true
		)
	));
	
	$instUser->SetPropertyValue($objUser->GetInstanceProperty("SecurityGroups"), new MultipleInstanceProperty(
	array
	(
		$instSecurityGroup_SystemAdministrator
	),
	array
	(
		$objSecurityGroups
	)));
	
?>