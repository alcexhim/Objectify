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
	
	$objUserLogin = TenantObject::Create("UserLogin");
	$objUserLogin->CreateInstanceProperty("Token", DataType::GetByName("Text"));
	$objUserLogin->CreateInstanceProperty("IPAddress", DataType::GetByName("Text"));
	
	$objUser = TenantObject::Create("User");
	$objUser->CreateInstanceProperty("UserName", DataType::GetByName("Text"));
	$objUser->CreateInstanceProperty("PasswordHash", DataType::GetByName("Text"));
	$objUser->CreateInstanceProperty("PasswordSalt", DataType::GetByName("Text"));
	$objUser->CreateInstanceProperty("IsGlobal", DataType::GetByName("Boolean"));
	$objUser->CreateInstanceProperty("Logins", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(null, array($objUserLogin)));
	
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
	
	$instUser->SetPropertyValue($objUser->GetInstanceProperty("SecurityGroups"), array
	(
			$instTenantManager
	));
	
?>