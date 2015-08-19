<?php
	use Phast\Data\Table;
	use Phast\Data\Column;
	use Phast\Data\ColumnValue;
	use Phast\Data\Record;
	use Phast\Data\RecordColumn;
	
	use PhoenixSNS\Objects\UserPresenceStatus;
	use PhoenixSNS\Objects\UserProfileVisibility;
	
	$tables[] = new Table("SecurityPermissions", "permission_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("Name", "VARCHAR", 50, null, false),
		new Column("Title", "LONGTEXT", null, null, false)
	));
	
	$tables[] = new Table("SecurityGroups", "group_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("Title", "VARCHAR", 50, null, false),
		new Column("ParentGroupID", "INT", null, null, true)
	));
	
	$tables[] = new Table("SecurityGroupPermissions", "grouppermission_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("GroupID", "INT", null, null, false),
		new Column("PermissionID", "INT", null, null, false)
	));
?>