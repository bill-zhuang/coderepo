<?php

use Phinx\Migration\AbstractMigration;

class AddTableBackendLog extends AbstractMigration
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
CREATE TABLE `backend_log` (
  `blid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT 'type(insert, update, delete)',
  `table` varchar(255) NOT NULL DEFAULT '' COMMENT 'table name',
  `content` text NOT NULL COMMENT 'SQL',
  `buid` int(11) NOT NULL COMMENT 'backend_user primary key',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 : valid 0 : invalid',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`blid`)
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
DROP TABLE IF EXISTS `backend_log`;
EOF;
        $this->execute($sql);
    }
}
