<?php

use Phinx\Migration\AbstractMigration;

class AddTableLagouJobAnalysis extends AbstractMigration
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
CREATE TABLE `lagou_job_analysis` (
  `jaid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `joid` int(10) unsigned NOT NULL DEFAULT '0',
  `lg_ctid` int(10) unsigned NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `num` int(10) unsigned NOT NULL DEFAULT '0',
  `num_plus` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '职位数是否超过500（拉勾职位数超过500用500+标记）',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`jaid`)
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
DROP TABLE IF EXISTS `lagou_job_analysis`;
EOF;
        $this->execute($sql);
    }
}
