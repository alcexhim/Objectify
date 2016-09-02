<?php
	namespace Objectify\Objects;
	
	class KnownObjects
	{
		const GUID___Class = "{B9C9B9B7-AD8A-4CBD-AA6B-E05784630B6B}";
		public static function get___Class()
		{
			return TenantObject::GetByGlobalIdentifier(self::GUID___Class);
		}

		const GUID___Attribute = "{F9CD7751-EF62-4F7C-8A28-EBE90B8F46AA}";
		public static function get___Attribute()
		{
			return TenantObject::GetByGlobalIdentifier(self::GUID___Attribute);
		}

		const GUID___Text_Attribute = "{C2F36542-60C3-4B9E-9A96-CA9B309C43AF}";
		public static function get___Text_Attribute()
		{
			return TenantObject::GetByGlobalIdentifier(self::GUID___Text_Attribute);
		}
		
		const GUID___Translatable_Text_Constant = "{04A53CC8-3206-4A97-99C5-464DB8CAA6E6}";
		public static function get___Translatable_Text_Constant()
		{
			return TenantObject::GetByGlobalIdentifier(self::GUID___Translatable_Text_Constant);
		}
		
		const GUID___Translatable_Text_Constant_Value = "{6D38E757-EC18-43AD-9C35-D15BB446C0E1}";
		public static function get___Translatable_Text_Constant_Value()
		{
			return TenantObject::GetByGlobalIdentifier(self::GUID___Translatable_Text_Constant_Value);
		}
		
		const GUID___Language = "{61102B47-9B2F-4CF3-9840-D168B84CF1E5}";
		public static function get___Language()
		{
			return TenantObject::GetByGlobalIdentifier(self::GUID___Language);
		}
		
		const GUID___Security_Group = "{498CCFD4-8D94-4EF4-B947-1D07ECC9342B}";
		public static function get___Security_Group()
		{
			return TenantObject::GetByGlobalIdentifier(self::GUID___Security_Group);
		}
		
		const GUID___Security_Privilege = "{8EA7D1D3-6FC8-40C9-98C6-F72798307E9F}";
		public static function get___Security_Privilege()
		{
			return TenantObject::GetByGlobalIdentifier(self::GUID___Security_Privilege);
		}
		
		const GUID___Method = "{D2813913-80B6-4DD6-9AD6-56D989169734}";
		public static function get___Method()
		{
			return TenantObject::GetByGlobalIdentifier(self::GUID___Method);
		}
		
		const GUID___Sequence_Method = "{8BF91515-89EA-4633-80D3-C096A38E7B89}";
		public static function get___Sequence_Method()
		{
			return TenantObject::GetByGlobalIdentifier(self::GUID___Sequence_Method);
		}
		
		const GUID___Task = "{D4F2564B-2D11-4A5C-8AA9-AF52D4EACC13}";
		public static function get___Task()
		{
			return TenantObject::GetByGlobalIdentifier(self::GUID___Task);
		}
		
		const GUID___User = "{9C6871C1-9A7F-4A3A-900E-69D1D9E24486}";
		public static function get___User()
		{
			return TenantObject::GetByGlobalIdentifier(self::GUID___User);
		}
		
		const GUID___User_Login = "{64F4BCDB-38D0-4373-BA30-8AE99AF1A5F7}";
		public static function get___User_Login()
		{
			return TenantObject::GetByGlobalIdentifier(self::GUID___User_Login);
		}
		
		const GUID___Page = "{D9626359-48E3-4840-A089-CD8DA6731690}";
		public static function get___Page()
		{
			return TenantObject::GetByGlobalIdentifier(self::GUID___Page);
		}
		
		const GUID___Tenant = "{703F9D65-C584-4D9F-A656-D0E3C247FF1F}";
		public static function get___Tenant()
		{
			return TenantObject::GetByGlobalIdentifier(self::GUID___Tenant);
		}
	}
?>