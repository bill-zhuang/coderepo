<?php

use Phinx\Migration\AbstractMigration;

class AddTableBackendRoleAcl extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    /*public function change()
    {

    }*/

    /**
     * Migrate Up.
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `backend_role_acl` (
  `braid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `brid` int(10) unsigned NOT NULL COMMENT 'backend role pkid',
  `baid` int(10) unsigned NOT NULL COMMENT 'backend_acl pkid',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'status: 1-valid, 0-invalid',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`braid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOF;
        $this->execute($sql);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $sql = <<<EOF
DROP TABLE IF EXISTS `backend_role_acl`;
EOF;
        $this->execute($sql);
    }
}
