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
	$tblTenantObjectInstances = new Table("TenantObjectInstances", "instance_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("ObjectID", "INT", null, null, false),
		new Column("GlobalIdentifier", "CHAR", 32, null, true)
	));
	/*
	 $tblTenantObjectInstances->PrimaryKey = new TableKey(array
	 (
	 new TableKeyColumn("ID"),
	 new TableKeyColumn("TenantID"),
	 new TableKeyColumn("ObjectID")
	 ));
	 */
	$tblTenantObjectInstances->ForeignKeys = array
	(
		new TableForeignKey("TenantID", new TableForeignKeyColumn($tblTenants, "ID")),
		new TableForeignKey("ObjectID", new TableForeignKeyColumn($tblTenantObjects, "ID"))
	);
	$tables[] = $tblTenantObjectInstances;
	
	// Properties of the object instances.
	$tblTenantObjectInstanceProperties = new Table("TenantObjectInstanceProperties", "property_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("ObjectID", "INT", null, null, false),
		new Column("Name", "VARCHAR", 256, null, true),
		new Column("DataTypeID", "INT", null, null, true),
		new Column("DefaultValue", "LONGTEXT", null, null, true),
		new Column("IsRequired", "INT", null, 0, false),
		new Column("ColumnVisible", "INT", null, 0, false)
	));
	$tblTenantObjectInstanceProperties->ForeignKeys = array
	(
		new TableForeignKey("TenantID", new TableForeignKeyColumn($tblTenants, "ID")),
		new TableForeignKey("ObjectID", new TableForeignKeyColumn($tblTenantObjects, "ID")),
		new TableForeignKey("DataTypeID", new TableForeignKeyColumn($tblDataTypes, "ID"))
	);
	$tables[] = $tblTenantObjectInstanceProperties;
	
	// Values of the object instance properties.
	$tblTenantObjectInstancePropertyValues = new Table("TenantObjectInstancePropertyValues", "propval_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("TenantID", "INT", null, null, false),
		new Column("ObjectID", "INT", null, null, false),
		new Column("InstanceID", "INT", null, null, false),
		new Column("PropertyID", "INT", null, null, false),
		new Column("Value", "LONGTEXT", null, null, false)
	));
	$tblTenantObjectInstancePropertyValues->PrimaryKey = new TableKey(array
	(
		new TableKeyColumn("TenantID"),
		new TableKeyColumn("ObjectID"),
		new TableKeyColumn("InstanceID"),
		new TableKeyColumn("PropertyID")
	));
	$tblTenantObjectInstancePropertyValues->ForeignKeys = array
	(
		new TableForeignKey("TenantID", new TableForeignKeyColumn($tblTenants, "ID")),
		new TableForeignKey("ObjectID", new TableForeignKeyColumn($tblTenantObjects, "ID")),
		new TableForeignKey("InstanceID", new TableForeignKeyColumn($tblTenantObjectInstances, "ID")),
		new TableForeignKey("PropertyID", new TableForeignKeyColumn($tblTenantObjectInstanceProperties, "ID"))
	);
	$tables[] = $tblTenantObjectInstancePropertyValues;
?>