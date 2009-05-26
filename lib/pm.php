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


   public function post($data)
   {
      // Clean the data.
      $data = $this->clean($data);
      
      // Make sure that if if there is an id, it does a update not a post.
      if (!empty($data['id'])) {
         return $this->update($data);
      }
      
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
         return $this->db->insert_id;
      }
      
      return false;
   }
   
   public function update($data)
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
      $data = $this->menuUnserialize($_POST['data']);
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
   
   public function createMenu()
   {
      $query = "SELECT `id`, `order`, `parent` " .
               "FROM `pm` " .
               "ORDER BY `parent` ASC, `order` ASC";

      $rows = $this->db->get_results($query);

      echo '<pre>';
      print_r($this->menuLoop($rows));
   }
   
   private function menuLoop($data)
   {
      $menu = array();
      print_r($data);
      for ($i=0; $i < count($data); $i++) { 
         $value = $data[$i];
         // var_dump($value);
         // var_dump($value->parent);
         
         // Not the first in level or in root level
         if ($value->order != 0 || $value->parent == 0) {
            array_push($menu, array(
               'id'     => $value->id,
               'order'  => $value->order,
               'parent' => $value->parent
            ));
            unset($data[$i]);
            $i--;
         } 
         
         // Not in the root level
         else {
            $temp = array(
               'id'     => $value->id,
               'order'  => $value->order,
               'parent' => $value->parent
            );
            array_push($menu[$i-1], $temp);
            // unset($data[$i]);
            // $this->menuLoop($data);
            
            // array_push($menu[$i - 1], $temp);
            // print_r($temp);
            // print_r($menu[$i - 1]);
            // echo $i;
         }
      }
      return $menu;
      
      // print_r($menu);
   }
   
   
   
   
   
   
   // private $fetched_tree = array();
   //   
   //   public function tree_fetch($parent = 0)
   //   {
   //      $query = "SELECT `id`, `order`, `parent`, `title`" .
   //               "FROM `pm` " .
   //               "ORDER BY `parent` ASC, `order` ASC";
   // 
   //      $rows = $this->db->get_results($query);
   //      
   //      $tree = array();
   // 
   //      foreach ($rows as $row) {
   //         $tree[$row->parent][$row->id] = array('title' => $row->title, 'id' => $row->id);
   //      }
   // 
   //      echo '<pre>';
   // 
   //      $this->tree_print($tree, $parent);
   //      
   // 
   //      // print_r($tree);
   //      var_dump($this->fetched_tree);
   //      
   //   }
   //   
   //   public function tree_print($tree, $parent)
   //   {
   //      // print_r($tree[$parent]);
   //      
   //      foreach($tree[$parent] as $id => $value) {
   //      // for ($i=1; $i <= count($tree[$parent]); $i++) { 
   //      //    $value = $tree[$parent][$i];
   //      //    $id = $item['id'];
   //         // var_dump($id);
   //         // var_dump($value['title']);
   // 
   //         $this->fetched_tree[] = array('id' => $id, 'title' => $value['title']);
   //         // var_dump($this->fetched_tree);
   //         
   //         // var_dump($value['title']);
   //         // var_dump(isset($tree[$id]));
   //         // var_dump($id);
   //         // var_dump(is_array($tree[$id]));
   //         // var_dump($tree[$id]);
   //         // echo "\n--\n";
   //         
   //         if(isset($tree[$id]) && is_array($tree[$id])) {
   //            $this->tree_print($tree, $id);
   //         }
   //      }
   //   }
   
   public $a = array();
   
   // Builds category list 
   public function categories($category=0, $level=0) { 
      $query = "SELECT `ID`, `parent`, `title` " .
               "FROM `pm` " .
               "WHERE parent = '" . $category . "'"; 

      $rows = $this->db->get_results($query);

      if (count($rows) > 0) {
         $level++; 
         foreach ($rows as $row) {
            // $this->a[$row->ID] = array($row->ID, $row->title);
            // array_push($this->a, array($row->ID, $row->title));
            for ($i=0; $i < $level; $i++) { 
               echo '-';
            }
            echo ' i: ', $row->ID, ' t: ', $row->title, "\n"; 
            $this->categories($row->ID, $level);
         }
      }
   }
   
   
   
   
   
   
   
   
   
   
// Private Functions
   
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