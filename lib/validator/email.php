<?php

namespace Kirby\Toolkit\Validator;

use Kirby\Toolkit\V;
use Kirby\Toolkit\Validator;

// direct access protection
if(!defined('KIRBY')) die('Direct access is not allowed');

/**
 * Email Validator
 * 
 * Checks for a valid email address
 * 
 * @package   Kirby Toolkit 
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Email extends Validator {

  public $message = 'The {attribute} must be a valid email';

  public function validate() {
    return filter_var($this->value, FILTER_VALIDATE_EMAIL) !== false;
  }

}