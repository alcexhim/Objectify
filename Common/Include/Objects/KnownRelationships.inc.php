<?php
	namespace Objectify\Objects;
	
	class KnownRelationships
	{
		const Relationship___Class__instance_labeled_by__String = "{F52FC851-D655-48A9-B526-C5FE0D7A29D2}";
		const Relationship___Translatable_Text_Constant__has__Translatable_Text_Constant_Value = "{F9B60C00-FF1D-438F-AC74-6EDFA8DD7324}";
		const Relationship___Class__has__Object_Source = "{B62F9B81-799B-4ABE-A4AF-29B45347DE54}";
		const Relationship___Object_Source__for__Class = "{FBB9391D-C4A2-4326-9F85-7801F377253C}";
		const Relationship___Tenant__has_sidebar__Menu_Item = "{D62DFB9F-48D5-4697-AAAD-1CAD0EA7ECFA}";
		const Relationship___Menu_Item__sidebar_for__Tenant = "{4E0A8C3D-5D10-44E5-A6D7-31F262711E01}";
		
		public static function get___Class__instance_labeled_by__String()
		{
			$instRelationship__Class__instance_labeled_by_String = Instance::GetByGlobalIdentifier(self::Relationship___Class__instance_labeled_by__String);
			return $instRelationship__Class__instance_labeled_by_String;
		}
		public static function get___Translatable_Text_Constant__has__Translatable_Text_Constant_Value()
		{
			$instRelationship__Translatable_Text_Constant__has__Translatable_Text_Constant_Value = Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__has__Translatable_Text_Constant_Value);
			return $instRelationship__Translatable_Text_Constant__has__Translatable_Text_Constant_Value;
		}
		public static function get___Class__has__Object_Source()
		{
			$instRelationship__Class__has__Object_Source = Instance::GetByGlobalIdentifier(self::Relationship___Class__has__Object_Source);
			return $instRelationship__Class__has__Object_Source;
		}
		public static function get___Object_Source__for__Class()
		{
			$instRelationship__Object_Source__for__Class = Instance::GetByGlobalIdentifier(self::Relationship___Object_Source__for__Class);
			return $instRelationship__Object_Source__for__Class;
		}
		public static function get___Tenant__has_sidebar__Menu_Item()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Tenant__has_sidebar__Menu_Item);
			return $inst;
		}
		public static function get___Menu_Item__sidebar_for__Tenant()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Menu_Item__sidebar_for__Tenant);
			return $inst;
		}
	}
?>