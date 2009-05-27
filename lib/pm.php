<?php
/**
 *
 * @author Ali Karbassi
 * @version $Id$
 * @copyright Ali Karbassi - May 17, 2009
 * @package Page Management
 **/

/**
 *
 *
 * @package default
 * @author Ali Karbassi
 */
class PageManagement
{
   private $db;
   private $safe = array('title', 'content', 'status', 'type', 'hidden',
                         'id');

   function __construct()
   {
      // Include the config file;
      require_once '_config.php';

      // Include ezSQL
      require_once 'lib/ezsql/ez_sql_mysql.php';
      $this->db = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
   }


   public function postNewContent($data)
   {
      // Clean the data.
      $data = $this->clean($data);

      // Make sure that if if there is an id, it does a update not a post.
      if (!empty($data['id'])) {
         return $this->update($data);
      }

      // Setting this to an extreme so new content is listed last.
      $data['order'] = (int)$this->db->get_var("SELECT MAX(`order`) FROM `pm`") + 1;
      
      // Start the query string parts.
      $query = 'INSERT INTO pm (';
      $part2 = 'VALUES (';

      // Step through the list of values (except ID) and add it the the
      // string.
      foreach ($data as $key => $value) {
         if ($key != 'id') {
            $query .= "`" . $key . "`, ";
            $part2 .= "'" . $value . "', ";
         }
      }

      // Remove last pointless ", " and combine the parts.
      $query = substr($query, 0, -2) . ') ' . substr($part2, 0, -2) . ')';

      // Run the query and return the auto generated id.
      if ($this->db->query($query)) {
         $id = $this->db->insert_id;
         // $order = (int)$this->db->get_var("SELECT `order` FROM `pm` WHERE `id` = '" . $id . "'");
         // return array($id, $order);
         return $id;
      }

      return false;
   }

   public function updateContent($data)
   {
      // Clean up the data.
      $data = $this->clean($data);

      // Make sure that if if there is no id, it does a post not a update.
      if (empty($data['id'])) {
         return $this->post($data);
      }

      // Start the query string.
      $query = 'UPDATE `pm` SET ';

      // Step through the list of values (except ID) and add it the the
      // string.
      foreach ($data as $key => $value) {
         if ($key != 'id') {
            $query .= "`" . $key . "` = '" . $value . "', ";
         }
      }

      // Remove last pointless ", "
      $query = substr($query, 0, -2);

      // Add WHERE clause
      $query .= " WHERE `ID` = '" . $data['id'] . "'";

      // Run the query and return the id.
      if ($this->db->query($query)) {
         return $data['id'];
      }

      return false;
   }


   public function updateMenu($data)
   {
      $data = $this->menuUnserialize($data);
      $queries = $this->menuOrder($data);

      // Flatten array
      $objTmp = (object) array('aFlat' => array());
      array_walk_recursive($queries,
         create_function('&$v, $k, &$t', '$t->aFlat[] = $v;'), $objTmp);
      $queries = $objTmp->aFlat;

      // Run each query.
      // It's faster to run each update rather than create a temporary table.
      foreach ($queries as $query) {
         $this->db->query($query);
      }

      return true;
   }

    public function buildMenu($value='')
    {
       return '{"columns":["Page Names"],"items":' . $this->buildListing() . '}';
    }

// Private Functions

    private function buildListing($parent=0, $level=0) {
      $query = "SELECT `ID`, `parent`, `title` " .
               "FROM `pm` " .
               "WHERE parent = '" . $parent . "' " .
               "ORDER BY `order` ASC, `parent` ASC ";

      $rows = $this->db->get_results($query);

      $str = '[';
      if (count($rows) > 0) {
         $level++;
         foreach ($rows as $row) {
            $str .= '{"id":' . $row->ID . ', "info":["' . $row->title . '"]';
            $ret = $this->buildListing($row->ID, $level);
            if (strlen($ret) > 2) {
               $str .= ', "children": ' . $ret;
            }
            $str .= '},';
         }
      }
      return substr($str, 0, -1) . ']';
   }

   private function menuOrder($data, $parent = 0)
   {
      $query = array();

      foreach ($data as $key => $value) {
         array_push($query, "UPDATE `pm` SET `order` = '" . $key .
            "', `parent` = '" . $parent . "' WHERE `ID` = '" .
            $value['id'] . "';");

         if ($value['children']) {
            array_push($query,
               $this->menuOrder($value['children'], $value['id']) );
         }
      }

      return $query;
   }

   private function menuUnserialize($data)
   {
      $fields = explode("&", $_POST['data']);
      foreach($fields as $field) {
         $key_value = explode("=", $field);
         $key = urldecode($key_value[0]);
         $value = urldecode($key_value[1]);

         // Monkey work
         $t = explode('[', $key, 2);
         $key = "nav[" . $t[1];

         eval('$' . $key . ' = ' . $value . ';');
      }

      return $nav;
   }

   private function clean($data)
   {
      // Remove any data not in the safe list.
      foreach (array_diff(array_keys($data), $this->safe) as $key => $value) {
         unset($data[$value]);
      }

      foreach ($data as $key => $value) {

         // Escape all variables to be safe.
         $data[$key] = $this->db->escape($value);

         // Convert text "false" or "true" to booleans
         $tmp = strtoupper(trim($data[$key]));
         if ($tmp === 'FALSE') {
            $data[$key] = FALSE;
         }

         if ($tmp === 'TRUE') {
            $data[$key] = TRUE;
         }

      }
      return $data;
   }
}