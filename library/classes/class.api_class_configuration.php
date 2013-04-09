<?php if (!defined('APPLICATION')) exit();

/**
 * Configuration API
 *
 * @package    API
 * @since      0.1.0
 * @author     Kasper Kronborg Isager <kasperisager@gmail.com>
 * @copyright  Copyright 2013 © Kasper Kronborg Isager
 * @license    http://opensource.org/licenses/MIT MIT
 */
class API_Class_Configuration extends API_Mapper
{
   /**
    * Retrieve Vanilla configuration
    *
    * GET /configuration
    *
    * @since   0.1.0
    * @access  public
    * @param   array $Parameters
    * @return  array
    */
   public function Get($Parameters)
   {
      $Format = $Parameters['Format'];

      $Return = array();
      $Return['Resource'] = 'dashboard/settings/configuration.' . $Format;
      $Return['Authenticate'] = 'Required';

      return $Return;
   }
}