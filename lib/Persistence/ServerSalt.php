<?php

/**
 * PrivateBin
 *
 * a zero-knowledge paste bin
 *
 * @link      https://github.com/PrivateBin/PrivateBin
 * @copyright 2012 Sébastien SAUVAGE (sebsauvage.net)
 * @license   https://www.opensource.org/licenses/zlib-license.php The zlib/libpng License
 * @version   1.5.1
 */

/**
 * ServerSalt
 *
 * This is a random string which is unique to each PrivateBin installation.
 * It is automatically created if not present.
 *
 * Salt is used:
 * - to generate unique VizHash in discussions (which are not reproductible across PrivateBin servers)
 * - to generate unique deletion token (which are not re-usable across PrivateBin servers)
 */
namespace PrivateBin\Persistence;

use PrivateBin\Data\AbstractData;

class ServerSalt extends AbstractPersistence
{
    /**
     * generated salt
     *
     * @access private
     * @static
     * @var    string
     */
    private static $salt = '';

    /**
     * generate a large random hexadecimal salt
     *
     * @access public
     * @static
     * @return string
     */
    public static function generate()
    {
        return bin2hex(random_bytes(256));
    }

    /**
     * get server salt
     *
     * @access public
     * @static
     * @return string
     */
    public static function get()
    {
        if (strlen(self::$salt)) {
            return self::$salt;
        }

        $salt = self::$_store->getValue('salt');
        if ($salt) {
            self::$salt = $salt;
        } else {
            self::$salt = self::generate();
            if (!self::$_store->setValue(self::$salt, 'salt')) {
                error_log('failed to store the server salt, delete tokens, traffic limiter and user icons won\'t work');
            }
        }
        return self::$salt;
    }

    /**
     * set the path
     *
     * @access public
     * @static
     * @param  AbstractData $store
     */
    public static function setStore(AbstractData $store)
    {
        self::$salt = '';
        parent::setStore($store);
    }
}
