<?php if (!defined('APPLICATION')) exit();

/**
 * Mapper class for defining APIs
 *
 * By extending this class, API classes can define their own GET, POST, PUT and
 * DELETE operations. If a given method is not extended by the API class, a 501
 * Method Not Implemented error will simply be thrown.
 *
 * @package    API
 * @since      0.1.0
 * @author     Kasper Kronborg Isager <kasperisager@gmail.com>
 * @copyright  Copyright 2013 © Kasper Kronborg Isager
 * @license    http://opensource.org/licenses/MIT MIT
 * @abstract
 */
class API_Mapper
{
   /**
    * API class GET operation
    *
    * This method will be run when a GET request is sent to a given API class.
    * The GET method only allows returning an API resource map.
    *
    * @since   0.1.0
    * @access  public
    * @param   array $Parameters Array of parameters defined in the API engine
    * @return  array An array containing a 'Resource' key with the URI to be 
    *                requested for the API call.
    */
   public function Get($Parameters)
   {
      throw new Exception("Method Not Implemented", 501);
   }

   /**
    * API class POST operation
    *
    * This method will be run when a POST request is sent to a given API class.
    * The POST method allows you to return an array of custom arguments which
    * will be included with the rest of the Form Data sent in the request body.
    *
    * @since   0.1.0
    * @access  public
    * @param   array $Parameters Array of parameters defined in the API engine
    * @return  array An array containing a 'Resource' key with the URI to be 
    *                requested for the API call. Also allows you to include an
    *                'Arguments' array containing key-value pairs to be included
    *                with the Form Data of the request body.
    */
   public function Post($Parameters)
   {
      throw new Exception("Method Not Implemented", 501);
   }

   /**
    * API class PUT operation
    *
    * This method will be run when a PUT request is sent to a given API class.
    * The PUT method allows you to return an array of custom arguments which
    * will be included with the rest of the Form Data sent in the request body.
    * 
    * @since   0.1.0
    * @access  public
    * @param   array $Parameters Array of parameters defined in the API engine
    * @return  array An array containing a 'Resource' key with the URI to be 
    *                requested for the API call. Also allows you to include an
    *                'Arguments' array containing key-value pairs to be included
    *                with the Form Data of the request body.
    */
   public function Put($Parameters)
   {
      throw new Exception("Method Not Implemented", 501);
   }

   /**
    * API class DELETE operation
    *
    * This method will be run when a DELETE request is sent to a given API class.
    * The DELETE method allows you to return an array of custom arguments but
    * doesn't allow for sending any Form Data directly in the request body
    * meaning that all data required for processing the request will have to be
    * included in the custom request arguments.
    *
    * @since   0.1.0
    * @access  public
    * @param   array $Parameters Array of parameters defined in the API engine
    * @return  array An array containing a 'Resource' key with the URI to be 
    *                requested for the API call. Also allows you to include an
    *                'Arguments' array containing key-value pairs to be included
    *                with the Form Data of the request body.
    */
   public function Delete($Parameters)
   {
      throw new Exception("Method Not Implemented", 501);
   }
}