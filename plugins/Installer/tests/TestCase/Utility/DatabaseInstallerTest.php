<?php
/**
 * Licensed under The GPL-3.0 License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since     2.0.0
 * @author     Christopher Castro <chris@quickapps.es
 * @link     http://www.quickappscms.org
 * @license     http://opensource.org/licenses/gpl-3.0.html GPL-3.0 License
 */
namespace Installer\Test\TestCase\View;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Installer\Utility\DatabaseInstaller;

/**
 * DatabaseInstallerTest class.
 */
class DatabaseInstallerTest extends TestCase
{

    /**
     * Fixtures.
     *
     * @var array
     */
    public $fixtures = [];

    /**
     * Instance of DatabaseInstaller.
     *
     * @var \Installer\Utility\DatabaseInstaller
     */
    public $installer = null;

    /**
     * setUp.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->installer = new DatabaseInstaller(['settingsPath' => TMP . 'settings_test.php']);
    }

    /**
     * tearDown.
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        if (is_writable(TMP . 'settings_test.php')) {
            unlink(TMP . 'settings_test.php');
        }
    }

    /**
     * test database population.
     *
     * @return void
     */
    public function testPopulate()
    {
        $this->skipIf(true, 'DB installation on CI server fails.');
        $this->_dropTables();

        $config = include ROOT . '/config/settings.php';
        $result = $this->installer->install($this->_getConn());
        $this->assertTrue($result);
        $this->assertEmpty($this->installer->errors());
    }

    /**
     * Gets connection information for installation.
     *
     * @return array
     */
    protected function _getConn()
    {
        $conn = [];
        if (getenv('DB') == 'sqlite') {
            $conn = [
                'url' => getenv('db_dsn'),
                'log' => true,
            ];
        } elseif (in_array(getenv('DB'), ['mysql', 'pgsql'])) {
            $conn = [
                'url' => getenv('db_dsn') . '_install',
                'log' => true,
            ];
        }

        return $conn;
    }

    /**
     * Removes all tables in current installation DB.
     *
     * @return void
     */
    protected function _dropTables()
    {
        // drop all tables
        ConnectionManager::drop('installation');
        ConnectionManager::config('installation', $this->_getConn());
        $db = ConnectionManager::get('installation');
        $db->connect();
        $tables = $db->schemaCollection()->listTables();
        foreach ($tables as $table) {
            $Table = TableRegistry::get($table, ['connection' => $db]);
            $schema = $Table->schema();
            $sql = $schema->dropSql($db);
            foreach ($sql as $stmt) {
                $db->execute($stmt)->closeCursor();
            }
        }
        unset($db);
        ConnectionManager::drop('installation');
    }
}
