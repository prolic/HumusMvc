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

namespace HumusMvc\Db\Service;

use HumusMvc\Db\MultiDbManager;
use HumusMvc\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend_Cache_Core;
use Zend_Db;
use Zend_Db_Table;

class MultiDbManagerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        if (!isset($config['multidb'])) {
            throw new Exception\RuntimeException(
                'No multi db config found.'
            );
        }
        $options = $config['multidb'];

        if (isset($options['defaultMetadataCache'])) {
            $this->setDefaultMetadataCache($options['defaultMetadataCache'], $serviceLocator);
            unset($options['defaultMetadataCache']);
        }

        $dbs = array();
        $defaultAdapter = null;

        foreach ($options as $id => $params) {
            $adapter = $params['adapter'];
            $default = (int) (
                isset($params['isDefaultTableAdapter']) && $params['isDefaultTableAdapter']
                    || isset($params['default']) && $params['default']
            );
            unset(
            $params['adapter'],
            $params['default'],
            $params['isDefaultTableAdapter']
            );

            $dbs[$id] = $adapter = Zend_Db::factory($adapter, $params);


            if ($default) {
                Zend_Db_Table::setDefaultAdapter($adapter);
                $defaultAdapter = $adapter;
            }
        }

        $manager = new MultiDbManager($dbs, $defaultAdapter);
        return $manager;
    }

    /**
     * Set the default metadata cache
     *
     * @param string|Zend_Cache_Core $cache
     * @return void
     */
    protected function setDefaultMetadataCache($cache, ServiceLocatorInterface $serviceLocator)
    {
        $metadataCache = null;

        if (is_string($cache)) {
            if ($serviceLocator->has($cache)) {
                $metadataCache = $serviceLocator->get($cache);
            }
        } else if ($cache instanceof Zend_Cache_Core) {
            $metadataCache = $cache;
        }

        if ($metadataCache instanceof Zend_Cache_Core) {
            Zend_Db_Table::setDefaultMetadataCache($metadataCache);
        }

    }
}