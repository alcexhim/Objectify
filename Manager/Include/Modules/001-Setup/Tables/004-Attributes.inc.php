<?php
	use Phast\Data\Table;
	use Phast\Data\Column;
	use Phast\Data\ColumnValue;
	use Phast\Data\Record;
	use Phast\Data\RecordColumn;
	use Phast\Data\TableKey;
	use Phast\Data\TableKeyColumn;
	use Phast\Data\TableForeignKey;
	use Phast\Data\TableForeignKeyColumn;
	use Phast\Data\TableForeignKeyReferenceOption;
	
	// Instances of the objects.
	$tblAttributes = new Table("Attributes", "attval_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("TenantID", "INT", null, null, false),
		new Column("AttributeTenantID", "INT", null, null, false),
		new Column("AttributeObjectID", "INT", null, null, false),
		new Column("AttributeInstanceID", "INT", null, null, false),
		new Column("ObjectID", "INT", null, null, false),
		new Column("InstanceID", "INT", null, null, false),
		new Column("EffectiveDateTime", "DATETIME", null, null, false),
		new Column("UserTenantID", "INT", null, null, true),
		new Column("UserObjectID", "INT", null, null, true),
		new Column("UserInstanceID", "INT", null, null, true),
		new Column("Value", "LONGTEXT", null, null, false)
	));
	$tblAttributes->PrimaryKey = new TableKey(array
	(
		new TableKeyColumn("TenantID"),
		new TableKeyColumn("AttributeTenantID"),
		new TableKeyColumn("AttributeObjectID"),
		new TableKeyColumn("AttributeInstanceID"),
		new TableKeyColumn("ObjectID"),
		new TableKeyColumn("InstanceID"),
		new TableKeyColumn("EffectiveDateTime")
	));
	$tblAttributes->ForeignKeys = array
	(
		new TableForeignKey(array("AttributeTenantID", "AttributeObjectID", "AttributeInstanceID"), new TableForeignKeyColumn($tblInstances, array("TenantID", "ObjectID", "ID"))),
		new TableForeignKey(array("UserTenantID", "UserObjectID", "UserInstanceID"), new TableForeignKeyColumn($tblInstances, array("TenantID", "ObjectID", "ID"))),
		new TableForeignKey(array("TenantID", "ObjectID", "InstanceID"), new TableForeignKeyColumn($tblInstances, array("TenantID", "ObjectID", "ID")))
	);
	$tables[] = $tblAttributes;
?>