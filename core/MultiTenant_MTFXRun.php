<?php
/**
 * DESCRIPTION.
 *
 *   MultiTenant Run
 *
 *  @author    Paragon Kingsley
 *  @copyright 2022 Paragon Kingsley
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License ("AFL") v. 3.0
 */


class MultiTenant_MTFXRun
{
    public $connection;

    public function getHeaders()
    {
        return array('Content-Type: application/json');
    }
    public function moveInstallationFiles($site_name)
    {
        $install1 = dirname(__FILE__).'/install.sh';
        $install2 = dirname(__FILE__).'/install.php';
        $folder   = ABSPATH.'/../'.$site_name;
        if (!is_dir($folder)) {
            umask(0);
            mkdir($folder, 0755, true);
        }
        if (!file_exists($folder.'/install.sh')) {
            copy($install1, $folder.'/install.sh');
            chmod($folder.'/install.sh', 0777);
        }
        if (!file_exists($folder.'/install.php')) {
            copy($install2, $folder.'/install.php');
            chmod($folder.'/install.php', 0777);
        }
    }


    public function makeConnection()
    {
        try {
            return $this->connection =  new PDO("mysql:host=".DB_HOST, DB_USER, DB_PASSWORD);
        } catch (PDOException $e) {
            $this->error[] = $e->getMessage();
        }
    }

    public function createNewDatabase($database)
    {
        $this->makeConnection();
        if ($database =='' || $database==false) {
            return false;
        }
        $sql = 'CREATE DATABASE '.$database.';';
        try {
            $this->connection->exec($sql);
            return true;
        } catch (PDOException $e) {
            //
            return false;
        }
    }

    public function sendFirstEmail($email, $site_name, $site_title, $admin_url)
    {
        $message = 'Hello '.$site_name.', Your new '.$site_title.' site has been successfully set up at: '.$admin_url.' You can log in to the administrator account with the information you provided during site creation. We hope you enjoy your new site. Thanks! --The Team @ '.get_bloginfo('name');
        $subject = 'Site Creation '.$site_name.' successful';

        wp_mail($email, $subject, $message);

        return true;
    }
    public function createNewSiteOnServer($ID, $database, $domain, $prefix, $site_name, $site_title, $password, $email)
    {
        ob_start();
        if (!$this->createNewDatabase($database)) {
            return array('status'=>'NK', 'message'=>'Could not create database');
        }

        $multitenant_dir = get_option('MTFX_WORKING_WPMULTI_DIR');

        if ($multitenant_dir =='') {
            return array('status'=>'NK', 'message'=>'Site directory not properly set, contact site admin for assistance.');
        }

        $site_domain = get_option('MTFX_MAIN_DOMAIN');
        if ($site_domain =='') {
            return array('status'=>'NK', 'message'=>'Domain is not set, contact admin for assistance.');
        }

        $this->editSh($database, $prefix, $domain, $multitenant_dir);

        $this->moveInstallationFiles($site_name);
        $r = $this->setFolderNSh($site_name, $site_domain);
        ob_clean();

        $response = $this->doWPInstall($site_title, $site_name, $password, $email);

        $value = (string) $r.' '.$response;

        $url = $this->getAdminURL($site_name);

        require_once plugin_dir_path(dirname(__FILE__)).'classes/MultiTenant_MTFXSite.php';

        $sitor = new MultiTenant_MTFXSite();
        if ($this->sendCurl($url, '', 'EXISTS') ===true) {
            $sitor->updateByEmail($email, 'description', $value);
            $sitor->updateByEmail($email, 'status', 'Active');

            $this->sendFirstEmail($email, $site_name, $site_title, $url);

            return array('status'=>'OK', 'message'=>'Site created successfully, manage your site from <a target="_blank" href="'.$url.'">'.$url.'</a>');
            return true;
        } else {
            return array('status'=>'NK', 'message'=>'Site creation could not complete, await email for confirmation');
        }
    }


    public function getAdminURL($site_name)
    {
        $admin_url = '';
        $domain = get_option('MTFX_MAIN_DOMAIN');
        $host = get_option('MTFX_THE_DOMAIN_HOST');
        if ($host=='') {
            $host = 'http';
        }

        if ($domain=='localhost') {
            $admin_url = $host.'://localhost/'.$site_name.'/wp-admin/index.php';
        } else {
            $admin_url = $host.'://'.$site_name.'.'.$domain.'/wp/wp-admin/index.php';
        }
        return $admin_url;
    }

    public function editSh($database, $prefix, $domain, $multitenant_dir)
    {
        $content = file_get_contents(dirname(__FILE__).'/install.txt');
        $content = str_replace(["DATABASE_NAME=''", "TABLE_PREFIX=''", "DOMAIN_NAME=''", "MULTITENANT_PATH=''" ], ["DATABASE_NAME=".$database, "TABLE_PREFIX=".$prefix, "DOMAIN_NAME=".$domain, "MULTITENANT_PATH=".$multitenant_dir], $content);
        $content = preg_replace('/\r\n?/', "\n", $content);
        file_put_contents(dirname(__FILE__).'/install.sh', $content);
        return true;
    }


    public function setFolderNSh($site_name, $site_domain)
    {
        $host = get_option('MTFX_THE_DOMAIN_HOST');
        if ($host =='') {
            $host ='http';
        }

        if ($site_domain=='localhost') {
            $url = $host.'://localhost/'.$site_name.'/install.php';
        } else {
            $url = $host.'://'.$site_name.'.'.$site_domain.'/install.php';
        }

        return  $this->sendCurl($url);
    }

    public function doWPInstall($title, $site_name, $passwd, $email)
    {
        $query = array('weblog_title'=>$title, 'user_login'=>$site_name, 'user_name'=>$site_name, 'admin_password'=>$passwd, 'admin_password2'=>$passwd, 'admin_email'=>$email, 'blog_public'=>1, 'lang'=>'en');
        $data = http_build_query($query);

        $domain = get_option('MTFX_MAIN_DOMAIN');
        $host = get_option('MTFX_THE_DOMAIN_HOST');
        if ($host=='') {
            $host = 'http';
        }

        if ($domain=='localhost') {
            $install_url = $host.'://localhost/'.$site_name.'/wp-admin/install.php?step=2';
        } else {
            $install_url = $host.'://'.$site_name.'.'.$domain.'/wp/wp-admin/install.php?step=2';
        }

        return $this->sendCurl($install_url, $data, 'INSTALL');
    }




    public function sendCurl($url, $data = '', $type = 'POST', $headers = array())
    {
        $curl = curl_init($url);
        if ($type =='INSTALL') {
            $headers = array('Content-type:application/x-www-form-urlencoded');

            curl_setopt(
                $curl,
                CURLOPT_USERAGENT,
                "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)"
            );
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        if ($type=='EXISTS') {
            curl_setopt($curl, CURLOPT_NOBODY, 1);
            curl_setopt($curl, CURLOPT_FAILONERROR, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            curl_close($curl);
            if ($result !== false) {
                return true;
            } else {
                return false;
            }
        } else {
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        }



        if (is_array($headers) && !empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        try {
            $data = curl_exec($curl);
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return false;
    }
}
