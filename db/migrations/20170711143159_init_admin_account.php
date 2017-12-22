<?php

use Phinx\Migration\AbstractMigration;

class InitAdminAccount extends AbstractMigration
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
        require_once dirname(__FILE__) . '/../../library/Bill/Security.php';
        require_once dirname(__FILE__) . '/../../library/Bill/GoogleAuthenticator.php';
        $userName = 'admin';
        $password = '123456';
        $insertData = [
            'brid' => 1,
            'role' => 'admin',
            'status' => 1,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
        ];
        $this->insert('backend_role', $insertData);
        //
        $security = new Bill_Security();
        $salt = $security->generateRandomString(64);
        $googleAuthenticator = Bill_GoogleAuthenticator::createUserSecretAndQRUrl($userName);
        $insertData = [
            'buid' => 1,
            'name' => $userName,
            'password' => md5($password . $salt),
            'salt' => $salt,
            'brid' => 1,
            'google_secret' => $googleAuthenticator['secret'],
            'google_qr_url' => $googleAuthenticator['qrCodeUrl'],
            'remark' => '',
            'status' => 1,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
        ];
        $this->insert('backend_user', $insertData);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $sqlRole = <<<EOF
DELETE FROM `backend_role`;
EOF;
        $sqlUser = <<<EOF
DELETE FROM `backend_user`;
EOF;
        $this->execute($sqlRole);
        $this->execute($sqlUser);
    }
}
