<?php
/**
 * DESCRIPTION.
 *
 *   MultiTenant shortcodes
 *
 *  @author    Paragon Kingsley
 *  @copyright 2022 Paragon Kingsley
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License ("AFL") v. 3.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class MultiTenant_MTFXShortcode
{
    public function __construct()
    {
        add_action('wp_head', array($this, 'addAjaxUrl'));
        add_action('wp_ajax_nopriv_mtfxajaxRegistrationPHP', array($this, 'mtfxajaxRegistrationPHP'));
        add_action('wp_ajax_mtfxajaxRegistrationLoggedInPHP', array($this, 'mtfxajaxRegistrationLoggedInPHP'));

        add_shortcode('mtfxregister', array($this, 'createRegisterForm'));
    }


    public function createRegisterForm()
    {
        if (is_user_logged_in()===false) {
            require_once plugin_dir_path(dirname(__FILE__)).'classes/MultiTenant_MTFXRegisterForm.php';
            $register = new MultiTenant_MTFXRegisterForm();
            return $register->registerContent();
        }
        return $this->createSiteForm();
    }


    public function createSiteForm()
    {
        require_once plugin_dir_path(dirname(__FILE__)).'classes/MultiTenant_MTFXRegisterForm.php';
        $register = new MultiTenant_MTFXRegisterForm();
        return $register->siteContent();
    }



    public function addAjaxUrl()
    {
        ?>
        <script>  
        var mtfx_ajaxurl = "<?php echo esc_url(admin_url().'admin-ajax.php');
        ?>";
         var mtfx_spinner_gif = "<?php echo esc_url(home_url().'/wp-content/plugins/multitenants/assets/img/spinner.gif');
        ?>";
        </script>
        <?php
    }



    public function mtfxajaxRegistrationLoggedInPHP()
    {
        $user = wp_get_current_user();

        require_once plugin_dir_path(dirname(__FILE__)).'classes/MultiTenant_MTFXTools.php';
        require_once plugin_dir_path(dirname(__FILE__)).'classes/MultiTenant_MTFXSite.php';
        require_once plugin_dir_path(dirname(__FILE__)).'core/MultiTenant_MTFXRun.php';

        $run = new MultiTenant_MTFXRun();
        $sitor = new MultiTenant_MTFXSite();
        if (isset($_POST['token']) && $_POST['token'] =='W98L7LP3WSI8JHFYRH98SO0Q3JGOLAQ4JURHFY34HD') {
            if (!is_object($user)) {
                echo json_encode(array('status'=>'NK', 'message'=>'Account does not exist, refresh your browser and try again'));
                exit;
            }

            if (!isset($user->user_email) || $user->user_email =='') {
                echo json_encode(array('status'=>'NK', 'message'=>'Account does not exist, refresh your browser and try again'));
                exit;
            }

            $site_title = htmlentities($_POST['site_title']);
            $site_name = htmlentities($_POST['site_name']);
            $password = htmlentities($_POST['password']);



            if (empty($site_title) || empty($site_name)) {
                echo json_encode(array('status'=>'NK', 'message'=>'Required form field is missing, site title and site name are required'));
                exit;
            }


            $site_name = $this->onlyAlpha($site_name);

            if (empty($password)) {
                echo json_encode(array('status'=>'NK', 'message'=>'Password is required, fill in the password and try again'));
                exit;
            }

            if (strlen($site_name) < 4) {
                echo json_encode(array('status'=>'NK', 'message'=>'Site name too short. At least 4 characters is required'));
                exit;
            }

            if (!validate_username($site_name)) {
                echo json_encode(array('status'=>'NK', 'message'=>'Sorry, the username you entered is not valid'));
                exit;
            }

            if ($sitor->existsSiteName($site_name)) {
                echo json_encode(array('status'=>'NK', 'message'=>'Sorry, this site name  '.$site_name.' is already in use'));
                exit;
            }


            if (username_exists($site_name) && $user->user_login != $site_name) {
                echo json_encode(array('status'=>'NK', 'message'=>'Sorry site name already exists'));
                exit;
            }

            $md_pass = md5($password);
            $admin_email = $Email;
            $db = $site_name.'_'.$user->ID;
            $prefix = 'wp'.$user->ID.'_';


            $coredomain = get_option('MTFX_MAIN_DOMAIN');
            if ($coredomain =='localhost') {
                $domain = 'localhost';
            } else {
                $domain = $Username.'.'.$coredomain;
            }



            (new MultiTenant_MTFXSite())->addSite($user->ID, $site_name, $md_pass, $site_title, $user->user_email, $db, $prefix);
            $response =$run->createNewSiteOnServer($user->ID, $db, $domain, $prefix, $site_name, $site_title, $password, $user->user_email);

            echo json_encode($response);
            exit;
        } else {
            echo json_encode(array('status'=>'NK', 'message'=>'Invalid request, check your credentials and try again'));
            exit;
        }
    }


    public function onlyAlpha($string)
    {
        return  trim(strtolower(preg_replace("/[^A-Za-z0-9 \- ]/", '', $string)), '-');
    }


    public function mtfxajaxRegistrationPHP()
    {
        require_once plugin_dir_path(dirname(__FILE__)).'classes/MultiTenant_MTFXTools.php';
        require_once plugin_dir_path(dirname(__FILE__)).'classes/MultiTenant_MTFXSite.php';
        require_once plugin_dir_path(dirname(__FILE__)).'core/MultiTenant_MTFXRun.php';

        $run = new MultiTenant_MTFXRun();

        if (isset($_POST['token']) && $_POST['token'] =='UTKK8JN48H98SO0Q3JG7DCBHJURHFY34HD') {
            foreach ($_POST as $postdata => $value) {
                $$postdata = htmlspecialchars($value);
            }

            if (!isset($Email) || !isset($Username) || !isset($Password)) {
                echo json_encode(array('status'=>'NK', 'message'=>'Email, Username and Password, must be provided'));
                wp_die();
            }
            $reg_errors = new WP_Error();

            if (empty($Username) || empty($Password) || empty($Email)) {
                echo json_encode(array('status'=>'NK', 'message'=>'Required form field is missing'));
                wp_die();
            }

            $Username = $this->onlyAlpha($Username);


            if (strlen($Username) < 4) {
                echo json_encode(array('status'=>'NK', 'message'=>'Username too short. At least 4 characters is required'));
                wp_die();
            }

            if (username_exists($Username)) {
                echo json_encode(array('status'=>'NK', 'message'=>'Sorry, that username already exists!'));
                wp_die();
            }

            if (!validate_username($Username)) {
                echo json_encode(array('status'=>'NK', 'message'=>'Sorry, the username you entered is not valid'));
                wp_die();
            }

            if (strlen($Password) < 5) {
                echo json_encode(array('status'=>'NK', 'message'=>'Password length must be greater than 4'));
                wp_die();
            }

            if (!is_email($Email)) {
                echo json_encode(array('status'=>'NK', 'message'=>'Email is not valid'));
                wp_die();
            }

            if (email_exists($Email)) {
                echo json_encode(array('status'=>'NK', 'message'=>'Email already in use'));
                wp_die();
            }

            if (is_wp_error($reg_errors)) {
                foreach ($reg_errors->get_error_messages() as $error) {
                    echo json_encode(array('status'=>'NK', 'message'=>$error));
                    wp_die();
                }
            }

            if (count($reg_errors->get_error_messages()) < 1) {
                $userdata = array(
        'user_login' => $Username,
        'user_email' => $Email,
        'user_pass' => $Password,
        );

                if (isset($Firstname)) {
                    $userdata['first_name'] = $Firstname;
                }
                if (isset($Lastname)) {
                    $userdata['last_name'] = $Lastname;
                }
                if (isset($Nickname)) {
                    $userdata['nickname'] = $Nickname;
                }


                $ID = wp_insert_user($userdata);

                $md_pass = md5($Password);
                $admin_email = $Email;
                $db = $Username.'_'.$ID;
                $prefix = 'wp'.$ID.'_';


                $coredomain = get_option('MTFX_MAIN_DOMAIN');
                if ($coredomain =='localhost') {
                    $domain = 'localhost';
                } else {
                    $domain = $Username.'.'.$coredomain;
                }


                (new MultiTenant_MTFXSite())->addSite($ID, $Username, $md_pass, $site_title, $admin_email, $db, $prefix);

                $response =$run->createNewSiteOnServer($ID, $db, $domain, $prefix, $Username, $site_title, $Password, $admin_email);

                echo json_encode($response);
                wp_die();
            } else {
                echo json_encode(array('status'=>'NK', 'message'=>'Unknow error occured, try gain'));
                wp_die();
            }
        }
    }
}
