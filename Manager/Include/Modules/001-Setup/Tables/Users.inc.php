<?php
	use Phast\Data\Table;
	use Phast\Data\Column;
	use Phast\Data\TableForeignKey;
	use Phast\Data\TableForeignKeyColumn;
	
	$tblUsers = new Table("Users", "user_", array
	(
		// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
		new Column("ID", "INT", null, null, false, true, true),
		new Column("LoginID", "VARCHAR", 50, null, false),
		new Column("PasswordHash", "VARCHAR", 256, null, false),
		new Column("PasswordSalt", "VARCHAR", 32, null, false)
	));
	$tables[] = $tblUsers;

	$tblUserLogins = new Table("UserLogins", "login_", array
	(
			// 			$name, $dataType, $size, $value, $allowNull, $primaryKey, $autoIncrement
			new Column("Token", "VARCHAR", 32, null, false),
			new Column("UserID", "INT", null, null, false),
			new Column("CreationTimestamp", "DATETIME", null, null, false),
			new Column("CreationIPAddress", "VARCHAR", 40, null, false)
	));
	$tblUserLogins->ForeignKeys[] = new TableForeignKey("UserID", new TableForeignKeyColumn($tblUsers, "ID"));
	$tables[] = $tblUserLogins;
?>