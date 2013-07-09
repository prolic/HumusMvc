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

namespace HumusMvc\Translate\Service;

use HumusMvc\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend_Translate;
use Zend_Translate_Adapter;

class TranslatorFactory implements FactoryInterface
{
    /**
     * @var Zend_Translate_Adapter
     */
    protected $translator;

    /**
     * Create translator adapter
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Zend_Translate_Adapter
     * @throws Exception\RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        if (!isset($config['translator'])) {
            throw new Exception\RuntimeException(
                'No translator config found.'
            );
        }
        $allOptions = $config['translator'];

        foreach ($allOptions as $module => $options) {
            if (!isset($options['content']) && !isset($options['data'])) {
                throw new Exception\RuntimeException('No translation source data provided.');
            } else if (array_key_exists('content', $options) && array_key_exists('data', $options)) {
                throw new Exception\RuntimeException(
                    'Conflict on translation source data: choose only one key between content and data.'
                );
            }

            if (empty($options['adapter'])) {
                $options['adapter'] = Zend_Translate::AN_ARRAY;
            }

            if (!empty($options['data'])) {
                $options['content'] = $options['data'];
                unset($options['data']);
            }

            if (isset($options['options'])) {
                foreach($options['options'] as $key => $value) {
                    $options[$key] = $value;
                }
            }

            if (!empty($options['cache']) && is_string($options['cache'])) {
                $cacheManager = $serviceLocator->get('CacheManager');
                if ($cacheManager->hasCache($options['cache'])) {
                    $options['cache'] = $cacheManager->getCache($options['cache']);
                }
            }
            if ($this->translator instanceof Zend_Translate_Adapter) {
                $this->translator->addTranslation($options);
            } else {
                $translate = new Zend_Translate($options);
                $this->translator = $translate->getAdapter();
            }
        }
        return $this->translator;
    }

}
