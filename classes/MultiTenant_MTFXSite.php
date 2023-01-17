<?php
/**
 * DESCRIPTION.
 *
 *   MultiTenant Sites
 *
 *  @author    Paragon Kingsley
 *  @copyright 2022 Paragon Kingsley
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License ("AFL") v. 3.0
 */



class MultiTenant_MTFXSite
{
    public $id_site;
    public $user_id;
    public $site_login;
    public $admin_email;
    public $db;
    public $prefix;
    public $md_pass;
    public $site_url;
    public $status;
    public $site_title;
    public $description;
    public $other_details;
    public $payload;
    public $date_add;
    public $date_upd;


    public function __construct()
    {
        global $wpdb;
        $this->table = $wpdb->prefix.'mt_sites';
    }


    public function addSite($user_id, $site_login, $md_pass, $site_title, $admin_email, $db, $prefix)
    {
        global $wpdb;
        $args = array('user_id'=>$user_id, 'site_login'=>$site_login, 'md_pass'=>$md_pass, 'site_title'=>$site_title, 'admin_email'=>$admin_email, 'db'=>$db, 'prefix'=>$prefix, 'status'=>'Pending', 'date_add'=>date('Y-m-d'));

        $inserted = $wpdb->insert($this->table, $args, array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'));
        if ($inserted == 1) {
            return $wpdb->insert_id;
        }
    }

      public function getAllSites()
      {
          global $wpdb;
          $sql = $wpdb->prepare("SELECT  a.*, display_name FROM $this->table a INNER JOIN ".$wpdb->prefix."users b ON b.ID = a.user_id  WHERE %d ORDER BY id_site DESC ", 1);
          $result = $wpdb->get_results($sql);
          return $result;
      }


       public function updateKey($id, $key, $value, $t = '%s')
       {
           global $wpdb;
           $updated = $wpdb->update($this->table, array($key => $value), array('id_site' => $id), array($t), array('%d'));
           if ($updated == 1) {
               return true;
           }

           return false;
       }


     public function updateByEmail($email, $key, $value, $t = '%s')
     {
         global $wpdb;
         $updated = $wpdb->update($this->table, array($key => $value), array('admin_email' => $email), array($t), array('%s'));
         if ($updated == 1) {
             return true;
         }

         return false;
     }


     public function existsSiteName($name)
     {
         global $wpdb;
         $sql = $wpdb->prepare("SELECT * FROM $this->table WHERE site_login = %s", $name);
         $result = $wpdb->get_row($sql, OBJECT);
         if (is_object($result) && isset($result->id_site) && $result->id_site >0) {
             return true;
         }
         return false;
     }



    public function deleteD($id)
    {
        global $wpdb;
        $del = $wpdb->delete($this->table, array('id_site' => $id), array('%d'));
        if ($del == 1) {
            return true;
        }

        return false;
    }

   // getAllSites
}
