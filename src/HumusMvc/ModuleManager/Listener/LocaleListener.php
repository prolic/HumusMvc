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

namespace HumusMvc\ModuleManager\Listener;

use HumusMvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend_Locale as Locale;
use Zend_Registry as Registry;

class LocaleListener
{
    const DEFAULT_REGISTRY_KEY = 'Zend_Locale';

    /**
     * @param \HumusMvc\MvcEvent $e
     * @return void
     */
    public function __invoke(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $config = $serviceManager->get('Config');

        if (!isset($config['locale'])) {
            // no locale config found, return
            return;
        }
        // set cache in locale to speed up application
        if ($serviceManager->has('CacheManager')) {
            $cacheManager = $serviceManager->get('CacheManager');
            Locale::setCache($cacheManager->getCache('default'));
        }

        $options = $config['locale'];
        if (!isset($options['default'])) {
            $locale = new Locale();
        } elseif(!isset($options['force']) ||
            (bool) $options['force'] == false)
        {
            // Don't force any locale, just go for auto detection
            Locale::setDefault($options['default']);
            $locale = new Locale();
        } else {
            $locale = new Locale($options['default']);
        }
        $key = (isset($options['registry_key']) && !is_numeric($options['registry_key']))
            ? $options['registry_key']
            : self::DEFAULT_REGISTRY_KEY;
        Registry::set($key, $locale);
    }
}