<?php

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
