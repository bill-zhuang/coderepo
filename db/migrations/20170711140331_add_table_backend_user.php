<?php

use Phinx\Migration\AbstractMigration;

class AddTableBackendUser extends AbstractMigration
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
CREATE TABLE `backend_user` (
  `buid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `salt` char(64) NOT NULL DEFAULT '' COMMENT 'password salt',
  `brid` int(10) unsigned NOT NULL COMMENT 'backend role pkid',
  `google_secret` char(16) DEFAULT '' COMMENT 'google secret',
  `google_qr_url` varchar(512) DEFAULT '' COMMENT 'google qr code url',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT 'remark',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '1:valid, 0: invalid',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`buid`)
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
DROP TABLE IF EXISTS `backend_user`;
EOF;
        $this->execute($sql);
    }
}
