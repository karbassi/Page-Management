<?php
/**
 * A PHP Class interface for Sierra Bravo's X-Box voting SOAP server.
 *
 * @author Ali Karbassi
 * @version $Id$
 * @copyright Ali Karbassi, May 17, 2009
 * @package Page Management
 **/

/**
 * A PHP Class interface for Sierra Bravo's X-Box voting SOAP server.
 *
 * @package Page Management
 * @author Ali Karbassi
 */
class PageManagement
{
   private $db;
   
   function __construct()
   {
      // Include the config file;
      require_once '_config.php';
      
      // Include ezSQL
      require_once 'lib/ezsql/ez_sql_mysql.php';
      $this->db = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
   }


   public function post($data)
   {
      $title   = $this->db->escape($data['title']);
      $content = $this->db->escape($data['body']);
      $status  = $this->db->escape($data['status']);
      $type    = $this->db->escape($data['type']);
      $hidden  = $this->db->escape((bool)$data['hidden']);
      
      $this->db->query("INSERT INTO pm (type, content, title, status, hidden) VALUES ('" . $type . "', '" . $content ."', '" . $title . "', '" . $status ."', '" . $hidden . "')");
      return;
   }
}

   // require_once 'lib/ezsql/ez_sql_mysql.php';
   // $db = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
   // 
   // $db->get_results("DESC pm");
   // $db->debug();
   // 
   // $db->get_results("SELECT * FROM pm");
   // $db->debug();
   // 
   // $title = $db->escape("First Page");
   // $content = $db->escape("Hello, my name is Ali Karbassi. This is a test.");
   // $type = $db->escape("page");
   // $status = $db->escape("draft");
   // 
   // $db->query("INSERT INTO pm (type, content, title, status) VALUES ('" . $type . "', '" . $content ."', '" . $title . "', '" . $status ."')");
   // $db->debug();
   // 
   // $db->get_results("SELECT * FROM pm");
   // $db->debug();
   // 
   // $db->query("TRUNCATE TABLE pm");
   // $db->debug();
   // 
   // $db->get_results("SELECT * FROM pm");
   // $db->debug();