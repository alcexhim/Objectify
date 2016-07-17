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
		

		const Relationship___Class__has_title__Translatable_Text_Constant = "{B8BDB905-69DD-49CD-B557-0781F7EF2C50}";
		public static function get___Class__has_title__Translatable_Text_Constant()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Class__has_title__Translatable_Text_Constant);
			return $inst;
		}
		
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

		const Relationship___User__has_display_name__Translatable_Text_Constant = "{6C29856C-3B10-4F5B-A291-DD3CA4C04A2F}";
		const Relationship___Translatable_Text_Constant__display_name_for__User = "{C0B4140B-92C1-4D52-B6DA-8E818A83F3FA}";
		public static function get___User__has_display_name__Translatable_Text_Constant()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___User__has_display_name__Translatable_Text_Constant);
			return $inst;
		}
		public static function get___Translatable_Text_Constant__display_name_for__User()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__display_name_for__User);
			return $inst;
		}

		const Relationship___User__has__Security_Group = "{1921D642-84EC-4E16-B568-9C333909C018}";
		const Relationship___Security_Group__for__User = "{EDC79A23-3324-472D-B2F8-8CB9D2CF9C5D}";
		public static function get___User__has__Security_Group()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___User__has__Security_Group);
			return $inst;
		}
		public static function get___Security_Group__for__User()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Security_Group__for__User);
			return $inst;
		}

		const Relationship___Security_Group__has__Security_Permission = "{5E305133-1380-4736-93E4-6B280B35FCE3}";
		const Relationship___Security_Permission__for__Security_Group = "{52DAC6FC-7E3A-4B09-B3FC-205FF38FE952}";
		public static function get___Security_Group__has__Security_Permission()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Security_Group__has__Security_Permission);
			return $inst;
		}
		public static function get___Security_Permission__for__Security_Group()
		{
			$inst = Instance::GetByGlobalIdentifier(self::Relationship___Security_Permission__for__Security_Group);
			return $inst;
		}
		
		const Relationship___Class__has_owner__User = "{D1A25625-C90F-4A73-A6F2-AFB530687705}";
		const Relationship___User__owner_for__Class = "{04DD2E6B-EA57-4840-8DD5-F0AA943C37BB}";
		public static function get___Class__has_owner__User()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Class__has_owner__User);
		}
		public static function get___User__owner_for__Class()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___User__owner_for__Class);
		}
		
		const Relationship___Extract_Single_Instance_String_Component__has__Relationship = "{5E499753-F50F-4A9E-BF53-DC013820499C}";
		const Relationship___Relationship__for__Extract_Single_Instance_String_Component = "{B0111132-8721-405C-967B-0BEFA92CFE9A}";
		public static function get___Extract_Single_Instance_String_Component__has__Relationship()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Extract_Single_Instance_String_Component__has__Relationship);
		}
		public static function get___Relationship__for__Extract_Single_Instance_String_Component()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Relationship__for__Extract_Single_Instance_String_Component);
		}
		
		const Relationship___Menu_Item_Command__has_title__Translatable_Text_Constant = "{65E3C87E-A2F7-4A33-9FA7-781EFA801E02}";
		const Relationship___Translatable_Text_Constant__title_for__Menu_Item_Command = "{901A5427-6344-40F4-B81C-D905EA152EB9}";
		public static function get___Menu_Item_Command__has_title__Translatable_Text_Constant()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Menu_Item_Command__has_title__Translatable_Text_Constant);
		}
		public static function get___Translatable_Text_Constant__title_for__Menu_Item_Command()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Translatable_Text_Constant__title_for__Menu_Item_Command);
		}
		
		const Relationship___Relationship__has_source__Class = "{7FB5D234-042E-45CB-B11D-AD72F8D45BD3}";
		const Relationship___Class__source_for__Relationship = "{20FFFDE8-11A5-48D6-894B-21C6B234B811}";
		public static function get___Relationship__has_source__Class()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Relationship__has_source__Class);
		}
		public static function get___Class__source_for__Relationship()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Class__source_for__Relationship);
		}
		const Relationship___Relationship__has_destination__Class = "{F220F1C2-0499-4E87-A32E-BDBF80C1F8A4}";
		const Relationship___Class__destination_for__Relationship = "{A66CD08C-A155-42AF-8995-A1D96C5A0C06}";
		public static function get___Relationship__has_destination__Class()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Relationship__has_destination__Class);
		}
		public static function get___Class__destination_for__Relationship()
		{
			return Instance::GetByGlobalIdentifier(self::Relationship___Class__destination_for__Relationship);
		}
	}
?>