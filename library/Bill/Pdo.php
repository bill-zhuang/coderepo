<?php

class Bill_Pdo
{
    private $dbh;

    public function __construct($hostName, $databaseName, $userName, $password, $option = array())
    {
        try {
            $this->dbh = new PDO("mysql:host={$hostName};dbname={$databaseName}", $userName, $password, $option);
        } catch (Exception $ex) {
            return 'Database connected failed: ' . $ex->getMessage();
        }
    }

    public function sqlQuery($sqlStatement)
    {
        if (isset($this->dbh)) {
            $queryResults = $this->dbh->query($sqlStatement)->fetchAll();

            if ($queryResults !== false) {
                return $queryResults;
            } else {
                return 'sql query error.';
            }
        } else {
            return 'connect database first.';
        }
    }

    public function sqlExecute($sqlStatement, $bindParameters = array())
    {
        if (isset($this->dbh)) {
            $query = $this->dbh->prepare($sqlStatement);
            $query->execute($bindParameters);
        } else {
            return 'connect database first.';
        }
    }

    public function sqlExecuteWithTransaction($sqlStatements)
    {
        if (isset($this->dbh)) {
            try {
                $this->dbh->beginTransaction();

                if (is_array($sqlStatements)) {
                    foreach ($sqlStatements as $sqlStatement) {
                        $this->dbh->exec($sqlStatement);
                    }
                } else {
                    $this->dbh->exec($sqlStatements);
                }

                $this->dbh->commit();

                return 'success';
            } catch (Exception $ex) {
                $this->dbh->rollBack();
                return 'error: ' . $ex->getMessage();
            }
        } else {
            return 'connect database first.';
        }
    }

    public function closeConnection()
    {
        $this->dbh = null;
    }
}