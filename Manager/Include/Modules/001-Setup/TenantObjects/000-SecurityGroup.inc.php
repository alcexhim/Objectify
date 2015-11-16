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
	
	$objSecurityPermission = TenantObject::Create("SecurityPermission");
	$objSecurityPermission->CreateInstanceProperty("Title", DataType::GetByName("TranslatableText"));
	
	$objSecurityGroup = TenantObject::Create("SecurityGroup");
	$objSecurityGroup->CreateInstanceProperty("Title", DataType::GetByName("TranslatableText"));
	$objSecurityGroup->CreateInstanceProperty("Permissions", DataType::GetByName("MultipleInstance"), new MultipleInstanceProperty(null, array($objSecurityPermission)));
	
?>