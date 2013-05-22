<?php

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * 
 * Cookie
 * 
 * This class makes cookie handling easy
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Cookie {

  /**
   * Set a new cookie
   * 
   * <code>
   * 
   * cookie::set('mycookie', 'hello', 60);
   * // expires in 1 hour
   * 
   * </code>
   * 
   * @param  string  $key The name of the cookie
   * @param  string  $value The cookie content
   * @param  int     $expires The number of minutes until the cookie expires
   * @param  string  $path The path on the server to set the cookie for
   * @param  string  $domain the domain 
   * @param  boolean $secure only sets the cookie over https
   * @return boolean true: the cookie has been created, false: cookie creation failed
   */
  static public function set($key, $value, $expires = 0, $path = '/', $domain = null, $secure = false) {
  
    // convert minutes to seconds    
    if($expires > 0) $expires = time() + ($expires * 60);
    
    // convert array values to json 
    if(is_array($value)) $value = a::json($value);

    // hash the value
    $value = self::hash($value) . '+' . $value;

    // store that thing in the cookie global 
    $_COOKIE[$key] = $value;
    
    // store the cookie
    return setcookie($key, $value, $expires, $path, $domain, $secure);
  
  }

  /**
   * Stores a cookie forever
   * 
   * <code>
   * 
   * cookie::forever('mycookie', 'hello');
   * // never expires
   * 
   * </code>
   * 
   * @param  string  $key The name of the cookie
   * @param  string  $value The cookie content
   * @param  string  $path The path on the server to set the cookie for
   * @param  string  $domain the domain 
   * @param  boolean $secure only sets the cookie over https
   * @return boolean true: the cookie has been created, false: cookie creation failed
   */
  static public function forever($key, $value, $path = '/', $domain = null, $secure = false) {
    return self::set($key, $value, 2628000, $path, $domain, $secure);
  }

  /**
   * Get a cookie value
   * 
   * <code>
   * 
   * cookie::get('mycookie', 'peter');
   * // sample output: 'hello' or if the cookie is not set 'peter'
   * 
   * </code>
   * 
   * @param  string  $key The name of the cookie
   * @param  string  $default The default value, which should be returned if the cookie has not been found
   * @return mixed   The found value
   */
  static public function get($key = null, $default = null) {
    if(is_null($key)) return $_COOKIE;
    $value = a::get($_COOKIE, $key);
    return (empty($value)) ? $default : self::parse($value);
  }

  /**
   * Checks if a cookie exists
   * 
   * @return boolean
   */
  static public function exists($key) {
    return !is_null(self::get($key));
  }

  /**
   * Creates a hash for the cookie value
   * salted with the secret cookie salt string from the config
   * 
   * @param string $value
   * @return string
   */
  static protected function hash($value) {
    return sha1($value . c::get('cookie.salt'));
  }

  /**
   * Parses the hashed value from a cookie
   * and tries to extract the value 
   * 
   * @param string $hash
   * @return mixed
   */
  static protected function parse($string) {

    // extract hash and value
    $parts = str::split($string, '+');
    $hash  = a::first($parts);
    $value = a::last($parts);

    // if the hash or the value is missing at all return null
    if(empty($hash) || empty($value)) return null;

    // compare the extracted hash with the hashed value
    if($hash !== self::hash($value)) return null;

    return $value;

  }

  /**
   * Remove a cookie
   * 
   * <code>
   * 
   * cookie::remove('mycookie');
   * // mycookie is now gone
   * 
   * </code>
   * 
   * @param  string  $key The name of the cookie
   * @param  string  $domain The domain of the cookie
   * @return mixed   true: the cookie has been removed, false: the cookie could not be removed
   */
  static public function remove($key, $path = '/', $domain = null, $secure = false) {
    unset($_COOKIE[$key]);
    return setcookie($key, false, -3600, $path, $domain, $secure);
  }

}
