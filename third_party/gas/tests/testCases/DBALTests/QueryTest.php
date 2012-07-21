<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for query method
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.1
 * @author      Taufan Aditya
 */

class QueryTest extends PHPUnit_Framework_TestCase {

    /**
     * @var object Database holder
     */
    public $db;

    /**
     * @see Gas\Core   (./classes/core.php)
     * @see Model\User (./tests/dummyModels/user.php)
     * @see Model\Wife (./tests/dummyModels/wife.php)
     */
    public function setUp()
    {
        // Prepare some table(s) for test,
        // and connect using related environment
        Model\User::setUp();
        Model\Wife::setUp();
        $this->db = Gas\Core::$db;
    }

    /**
     * @see Model\User (./tests/dummyModels/user.php)
     */
    public function testSimpleQuery()
    {
        // Execute some simple query
        // this should return FALSE, 
        // since `foo` table was not exists
        $sql    = $this->_prep_query('SELECT * FROM `foo`');
        $result = $this->db->simple_query($sql);
        $this->assertFalse($result);

        // Execute some simple query
        // this should return a PDOStatement instance, 
        // since `user` table was exists
        $sql    = $this->_prep_query('SELECT * FROM `user`');
        $result = $this->db->simple_query($sql);
        $this->assertInstanceOf('PDOStatement', $result);

        // This also mean we could use some PDO fetch method directly on it
        $first_row = $result->fetch(PDO::FETCH_ASSOC);

        // Check the result
        $this->assertArrayHasKey('id', $first_row);
        $this->assertArrayHasKey('name', $first_row);
        $this->assertArrayHasKey('username', $first_row);
        $this->assertArrayHasKey('email', $first_row);
    }

    /**
     * @see Model\Wife (./tests/dummyModels/wife.php)
     */
    public function testQuery()
    {
        // Execute some query this should return FALSE,
        // since `foo` table was not exists
        // Note that unlike with simple query, 
        // we do not need to escape it will automatically prepared and escaped
        $sql    = 'SELECT * FROM foo';
        $result = $this->db->query($sql);
        $this->assertFalse($result);

        // this should return a CI_DB_Result instance, 
        // and in this particular test it would be a CI_DB_pdo_Result instance, 
        // since `wife` table was exists
        $sql    = 'SELECT * FROM wife';
        $result = $this->db->query($sql);
        $this->assertInstanceOf('CI_DB_result', $result);
        $this->assertInstanceOf('CI_DB_pdo_result', $result);

        // A result should can calling `result`, `result_array` and `result_object`
        $this->assertEquals(is_callable(array($result, 'result'), TRUE), TRUE);
        $this->assertEquals(is_callable(array($result, 'result_array'), TRUE), TRUE);
        $this->assertEquals(is_callable(array($result, 'result_object'), TRUE), TRUE);

        // Check the meta-information about this query
        $this->assertEquals($result->num_rows(), 3);
        $this->assertEquals($result->num_fields(), 4);
    }

    /**
     * Prep the query
     *
     * @param   string  an SQL query
     * @return  string
     */
    protected function _prep_query($sql)
    {
        if ($this->db->subdriver === 'pgsql')
        {
            // Change the backtick(s) for Postgre
            $sql = str_replace('`', '"', $sql);
        }
        elseif ($this->db->subdriver === 'sqlite')
        {
            // Change the backtick(s) for SQLite
            $sql = str_replace('`', '', $sql);
        }

        return $sql;
    }
}