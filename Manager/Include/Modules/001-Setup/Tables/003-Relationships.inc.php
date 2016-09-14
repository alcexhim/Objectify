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
	
	$tblRelationships = new Table("Relationships", "relationship_", array
	(
		new Column("SourceTenantID", "INT", null, null, false),
		new Column("SourceObjectID", "INT", null, null, false),
		new Column("SourceInstanceID", "INT", null, null, false),
		new Column("RelationshipTenantID", "INT", null, null, false),
		new Column("RelationshipObjectID", "INT", null, null, false),
		new Column("RelationshipInstanceID", "INT", null, null, false),
		new Column("DestinationTenantID", "INT", null, null, false),
		new Column("DestinationObjectID", "INT", null, null, false),
		new Column("DestinationInstanceID", "INT", null, null, false),
		new Column("Order", "INT", null, 0, false)
	));
	$tblRelationships->PrimaryKey = new TableKey(array
	(
		new TableKeyColumn("SourceTenantID"),
		new TableKeyColumn("SourceObjectID"),
		new TableKeyColumn("SourceInstanceID"),
		new TableKeyColumn("RelationshipTenantID"),
		new TableKeyColumn("RelationshipObjectID"),
		new TableKeyColumn("RelationshipInstanceID"),
		new TableKeyColumn("DestinationTenantID"),
		new TableKeyColumn("DestinationObjectID"),
		new TableKeyColumn("DestinationInstanceID"),
		new TableKeyColumn("Order")
	));
	$tblRelationships->ForeignKeys = array
	(
		new TableForeignKey(array("RelationshipTenantID", "RelationshipObjectID", "RelationshipInstanceID"), new TableForeignKeyColumn($tblInstances, array("TenantID", "ObjectID", "ID"))),
		new TableForeignKey(array("SourceTenantID", "SourceObjectID", "SourceInstanceID"), new TableForeignKeyColumn($tblInstances, array("TenantID", "ObjectID", "ID"))),
		new TableForeignKey(array("DestinationTenantID", "DestinationObjectID", "DestinationInstanceID"), new TableForeignKeyColumn($tblInstances, array("TenantID", "ObjectID", "ID")))
	);
	$tables[] = $tblRelationships;
?>