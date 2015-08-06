<?php

class Bill_Pdo
{
    private $dbh;

    public function __construct($host_name, $database_name, $user_name, $password, $option = array())
    {
        try
        {
            $this->dbh = new PDO("mysql:host={$host_name};dbname={$database_name}", $user_name, $password, $option);
        }
        catch (Exception $ex)
        {
            return 'Database connected failed: ' . $ex->getMessage();
        }
    }

    public function sqlQuery($sql_statement)
    {
        if (isset($this->dbh))
        {
            $query_results = $this->dbh->query($sql_statement)->fetchAll();

            if ($query_results !== false)
            {
                return $query_results;
            }
            else
            {
                return 'sql query error.';
            }
        }
        else
        {
            return 'connect database first.';
        }
    }

    public function sqlExecute($sql_statement, $bind_parameters = array())
    {
        if (isset($this->dbh))
        {
            $query = $this->dbh->prepare($sql_statement);
            $query->execute($bind_parameters);
        }
        else
        {
            return 'connect database first.';
        }
    }

    public function sqlExecuteWithTransaction($sql_statements)
    {
        if (isset($this->dbh))
        {
            try
            {
                $this->dbh->beginTransaction();

                if (is_array($sql_statements))
                {
                    foreach ($sql_statements as $sql_statement)
                    {
                        $this->dbh->exec($sql_statement);
                    }
                }
                else
                {
                    $this->dbh->exec($sql_statements);
                }

                $this->dbh->commit();

                return 'success';
            }
            catch (Exception $ex)
            {
                $this->dbh->rollBack();
                return 'error: ' . $ex->getMessage();
            }
        }
        else
        {
            return 'connect database first.';
        }
    }

    public function closeConnection()
    {
        $this->dbh = null;
    }
}