<?php
/**
 * A light-weight page management system.
 *
 * @author Ali Karbassi
 * @version 1.0
 * @copyright Ali Karbassi - May 27, 2009
 * @package Page Management
 **/

/**
 * A light-weight page management system.
 *
 * @package PageManagement
 * @author Ali Karbassi
 */
class PageManagement
{
   private $db;
   private $safe = array('title', 'content', 'status', 'type', 'display',
                         'id');

   /**
    * Default constructor
    *
    * @author Ali Karbassi
    */
   function __construct()
   {
      // Include the config file;
      require_once '_config.php';

      // Include ezSQL
      require_once 'lib/ezsql/ez_sql_mysql.php';
      $this->db = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
   }

   /**
    * Posts new content to the database.
    *
    * @param string $data
    * @return Id/false - Returns the inserted ID or false.
    * @author Ali Karbassi
    */
   public function postNewContent($data)
   {
      // Clean the data.
      $data = $this->clean($data);

      // Make sure that if if there is an id, it does a update not a post.
      if (!empty($data['id'])) {
         return $this->update($data);
      }

      // Setting this to an extreme so new content is listed last.
      $query = "SELECT MAX(`order`) FROM `pm`";
      $data['order'] = (int)$this->db->get_var($query) + 1;

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
         return $id;
      }

      return false;
   }

   /**
    * Updates content. The ID is passed in via $data.
    *
    * @param string $data Content to be updated.
    * @return Id/false - Returns the inserted ID or false.
    * @author Ali Karbassi
    */
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

   /**
    * Deletes a specific item.
    *
    * @param string $id Item to be deleted
    * @return boolean True if item was deleted, else false
    * @author Ali Karbassi
    */
   public function deleteContent($id)
   {
      if (empty($id)) {
         return;
      }

      $query = "SELECT `ID`, `order`, `parent`".
               "FROM `pm` " .
               "WHERE `ID` = '" . $id . "' ";

      $parent = $this->db->get_row($query);

      // Move all children up a level.
      // Keep the order of $id and assign it to the children being
      // moved.
      $query = "SELECT `ID`, `order`, `parent` ".
               "FROM `pm` " .
               "WHERE `parent` = '" . $id . "' ".
               "ORDER BY `order` ASC, `parent` ASC ";

      $children = $this->db->get_results($query);

      if (count($children) > 0) {
         foreach ($children as $child) {
            $query = "UPDATE `pm` ".
                     "SET `order` = '" . $parent->order . "', ".
                     " `parent` = '" . $parent->parent . "' " .
                     "WHERE `ID` = '" . $child->ID . "'";
            $this->db->query($query);
            $parent->order++;
         }
      }

      // Delete the item
      $query = "DELETE FROM `pm`" .
               "WHERE `ID` = '" . $id . "'";

      return $this->db->query($query);
   }

   /**
    * Updates the menu structure to the database.
    *
    * @param string $data Menu structure
    * @return void
    * @author Ali Karbassi
    */
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

   /**
    * Builds the menu structure to be passed to jQuery.
    *
    * @return string String containing the json structure to be returned.
    * @author Ali Karbassi
    */
   public function buildMenu()
   {
      $items = $this->buildListing();

      // Catching empty list
      if ($items == ']') {
         $items = "[]";
      }

      return '{"columns":["Page Names"],"items":' . $items . '}';
   }

   /**
    * Checks the login against the simple password.
    *
    * @param string $password Password being checked.
    * @return boolean True if password matches, else false.
    * @author Ali Karbassi
    */
   public function login($password='')
   {
      return sha1(MD5($password) . PASSWORD_SEED) === PROJECT_PASSWORD;
   }

   /**
    * Loads a specific item by id.
    *
    * @param string $id Item id to be loaded.
    * @return hash The hash element containing the item loaded.
    * @author Ali Karbassi
    */
   public function loadContent($id='')
   {
      if (empty($id)) {
         return;
      }

      $query = "SELECT `ID`, `type`, `content`, `title`, `status`, " .
               "`display` " .
               "FROM `pm` " .
               "WHERE `ID` = '" . $id . "'";

      return $this->db->get_row($query);
   }

// Private Functions

   /**
    * Recursive function to build the menu.
    *
    * @param string $parent Parent ID used to find children.
    * @return string String to be parsed as json object
    * @author Ali Karbassi
    */
   private function buildListing($parent=0) {
      $query = "SELECT `ID`, `parent`, `title` " .
               "FROM `pm` " .
               "WHERE parent = '" . $parent . "' " .
               "ORDER BY `order` ASC, `parent` ASC ";

      $rows = $this->db->get_results($query);

      $str = '[';
      if (count($rows) > 0) {
         foreach ($rows as $row) {
            $str .= '{"id":' . $row->ID . ', "info":["' . $row->title . '"]';
            $ret = $this->buildListing($row->ID);
            if (strlen($ret) > 2) {
               $str .= ', "children": ' . $ret;
            }
            $str .= '},';
         }
      }
      return substr($str, 0, -1) . ']';
   }

   /**
    * Update the menu order, recursively.
    *
    * @param string $data Data to be updated
    * @param string $parent The parent ID.
    * @return array Menu structure in array form.
    * @author Ali Karbassi
    */
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

   /**
    * Converts json serialized string into PHP array.
    *
    * @param string $data json serialized string
    * @return array PHP array formed
    * @author Ali Karbassi
    */
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

   /**
    * Removes any data passed that is not used. It uses the private safe list.
    * The function also escapes the data being passed in.
    *
    * @param string $data Data to be cleaned
    * @return string Cleaned data.
    * @author Ali Karbassi
    */
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