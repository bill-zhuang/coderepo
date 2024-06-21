<?php

use Phinx\Migration\AbstractMigration;

class AddTableEtfFund extends AbstractMigration
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
CREATE TABLE `etf_fund` (
  `fuid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tt_code` varchar(255) NOT NULL DEFAULT '' COMMENT '���������code',
  `name` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`fuid`),
  UNIQUE KEY `idx_ttid` (`tt_code`)
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
DROP TABLE IF EXISTS `etf_fund`;
EOF;
        $this->execute($sql);
    }
}
