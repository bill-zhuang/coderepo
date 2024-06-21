<?php

use Phinx\Migration\AbstractMigration;

class AddTableBackendAcl extends AbstractMigration
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
CREATE TABLE `backend_acl` (
  `baid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'acl name',
  `module` varchar(100) NOT NULL DEFAULT '' COMMENT 'module name',
  `controller` varchar(100) NOT NULL DEFAULT '' COMMENT 'controller name',
  `action` varchar(100) NOT NULL DEFAULT '' COMMENT 'action name',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT 'status: 1-valid, 0-invalid',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`baid`),
  UNIQUE KEY `idx_m_c_a` (`module`,`controller`,`action`)
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
DROP TABLE IF EXISTS `backend_acl`;
EOF;
        $this->execute($sql);
    }
}
