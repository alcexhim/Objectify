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
	$tblRelationships = new Table("Relationships", "relationship_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("TenantID", "INT", null, null, false),
		new Column("RelationshipInstanceID", "INT", null, null, false),
		new Column("SourceInstanceID", "INT", null, null, false)
	));
	$tblRelationships->ForeignKeys = array
	(
		new TableForeignKey("TenantID", new TableForeignKeyColumn($tblTenants, "ID")),
		new TableForeignKey("RelationshipInstanceID", new TableForeignKeyColumn($tblTenantObjectInstances, "ID")),
		new TableForeignKey("SourceInstanceID", new TableForeignKeyColumn($tblTenantObjectInstances, "ID"))
	);
	$tables[] = $tblRelationships;
	

	$tblRelationshipTargets = new Table("RelationshipTargets", "target_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("RelationshipID", "INT", null, null, false),
		new Column("DestinationInstanceID", "INT", null, null, false),
		new Column("Order", "INT", null, 0, false)
	));
	$tblRelationshipTargets->PrimaryKey = new TableKey(array
	(
		new TableKeyColumn("RelationshipID"),
		new TableKeyColumn("DestinationInstanceID")
	));
	$tblRelationshipTargets->ForeignKeys = array
	(
		new TableForeignKey("RelationshipID", new TableForeignKeyColumn($tblRelationships, "ID")),
		new TableForeignKey("DestinationInstanceID", new TableForeignKeyColumn($tblTenantObjectInstances, "ID"))
	);
	$tables[] = $tblRelationshipTargets;
?>