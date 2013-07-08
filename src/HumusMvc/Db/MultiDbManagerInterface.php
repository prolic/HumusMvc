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

interface MultiDbManagerInterface
{
    /**
     * Get the default db connection
     *
     * @param  boolean $justPickOne If true, a random (the first one in the stack)
     *                           connection is returned if no default was set.
     *                           If false, null is returned if no default was set.
     * @return null|\Zend_Db_Adapter_Abstract
     */
    public function getDefaultDb($justPickOne = true);

    /**
     * Retrieve the specified database connection
     *
     * @param  null|string|\Zend_Db_Adapter_Abstract $db The adapter to retrieve.
     *                                               Null to retrieve the default connection
     * @return \Zend_Db_Adapter_Abstract
     * @throws \HumusMvc\Exception\InvalidArgumentException if the given parameter could not be found
     */
    public function getDb($db = null);

    /**
     * Determine if the given db(identifier) is the default db.
     *
     * @param  string|\Zend_Db_Adapter_Abstract $db The db to determine whether it's set as default
     * @return boolean True if the given parameter is configured as default. False otherwise
     */
    public function isDefault($db);
}
