<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace HumusMvc\Db;

use HumusMvc\Exception;
use Zend_Db_Adapter_Abstract;

class MultiDbManager implements MultiDbManagerInterface
{
    /**
     * Associative array containing all configured db's
     *
     * @var array
     */
    protected $dbs = array();

    /**
     * An instance of the default db, if set
     *
     * @var null|Zend_Db_Adapter_Abstract
     */
    protected $defaultDb;

    /**
     * Constructor
     *
     * @param array $dbs array of Zend_Db_Adapter_Abstract
     * @param null|Zend_Db_Adapter_Abstract $defaultDb
     */
    public function __construct(array $dbs, $defaultDb)
    {
        $this->dbs = $dbs;
        $this->defaultDb = $defaultDb;
    }

    /**
     * Get the default db connection
     *
     * @param  boolean $justPickOne If true, a random (the first one in the stack)
     *                           connection is returned if no default was set.
     *                           If false, null is returned if no default was set.
     * @return null|Zend_Db_Adapter_Abstract
     */
    public function getDefaultDb($justPickOne = true)
    {
        if (null !== $this->defaultDb) {
            return $this->defaultDb;
        }

        if ($justPickOne) {
            return reset($this->dbs); // Return first db in db pool
        }

        return null;
    }

    /**
     * Retrieve the specified database connection
     *
     * @param  null|string|Zend_Db_Adapter_Abstract $db The adapter to retrieve.
     *                                               Null to retrieve the default connection
     * @return Zend_Db_Adapter_Abstract
     * @throws Exception\InvalidArgumentException if the given parameter could not be found
     */
    public function getDb($db = null)
    {
        if ($db === null) {
            return $this->getDefaultDb();
        }

        if (isset($this->dbs[$db])) {
            return $this->dbs[$db];
        }

        throw new Exception\InvalidArgumentException(
            'A DB adapter was tried to retrieve, but was not configured'
        );
    }

    /**
     * Determine if the given db(identifier) is the default db.
     *
     * @param  string|Zend_Db_Adapter_Abstract $db The db to determine whether it's set as default
     * @return boolean True if the given parameter is configured as default. False otherwise
     */
    public function isDefault($db)
    {
        if(!$db instanceof Zend_Db_Adapter_Abstract) {
            $db = $this->getDb($db);
        }

        return $db === $this->defaultDb;
    }

}
