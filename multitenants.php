<?php

/*
Plugin Name: MultiTenants
Plugin URI: https://mrparagon.me/multi-tenants-wordpress
Description: Multitenant plugin creates wordpress site that share same resources. Works with multitenant app. You need to install https://github.com/troychaplin/wp-multitenant and configure appropriately.
Author: Kingsley Paragon
Version: 1.0.0
Author URI: https://mrparagon.me
*/

class MultiTenantBuilder
{
    public function __construct()
    {
        $this->addScriptsAndStyles();
        $this->addAllShortCode();
        $this->adminPages();
        if (get_option('MTFX_KEEP_LOCAL_EMAIL_LOGS') =='Yes') {
            add_filter('wp_mail', array($this, 'logEmailMessages'));
        }
    }


    public function getVersion()
    {
        $version = get_option('MULTITENANT_MTFX_PLUGINVERSION');
        if ($version=='') {
            return '1.0.0';
        }
        return $version;
    }



     public function logEmailMessages($data)
     {
         file_put_contents(dirname(__FILE__).'/logs/email_logs.txt', date('Y-m-d H:i:s').' '.$data['message']."\n\n\n", FILE_APPEND);
         return $data;
     }





    public function adminPages()
    {
        require_once dirname(__FILE__).'/admin/MultiTenant_MTFXConfig.php';
        require_once dirname(__FILE__).'/admin/MultiTenant_MTFXSiteList.php';
        require_once dirname(__FILE__).'/admin/MultiTenant_MTFXLogEmail.php';

        $page = '';
        if (isset($_GET) && isset($_GET['page'])) {
            $page = esc_html($_GET['page']);
        }
        if (in_array($page, ['multitenant_config_page', 'multitenant_tenants_page'])) {
            add_action('admin_enqueue_scripts', array($this, 'adminScripts'));
        }
    }



     public function adminScripts()
     {
         wp_enqueue_script('adminmultitenantsjs', plugins_url().'/multitenants/assets/js/bootstrap.min.js', array('jquery'), '', true);
         wp_enqueue_script('datamultitenantsjs', plugins_url().'/multitenants/assets/js/datatables.min.js', array('jquery'), '', true);
         wp_enqueue_script('backmultitenantsjs', plugins_url().'/multitenants/assets/js/back.js', array('jquery'), '', true);
         wp_enqueue_style('adminmultitenantscss', plugins_url().'/multitenants/assets/css/bootstrap.min.css', array(), $this->getVersion(), 'All');
         wp_enqueue_style('datamultitenantscss', plugins_url().'/multitenants/assets/css/datatables.min.css', array(), $this->getVersion(), 'All');
         wp_enqueue_style('backmultitenantscss', plugins_url().'/multitenants/assets/css/back.css', array(), $this->getVersion(), 'All');
     }



    public function menuShowAllSites()
    {
        add_menu_page(esc_html__('All Sites Registered', 'multitenants'), esc_html__('All Sites', 'multitenants'), 'manage_options', 'multitenants_all_registered_sites', array($this, 'thePage'), 'dashicons-exerpt-view', '3.3');
    }


     public function addAllShortCode()
     {
         require_once dirname(__FILE__).'/shortcodes/MultiTenant_MTFXShortcode.php';
         return new MultiTenant_MTFXShortcode();
     }

    public function addScriptsAndStyles()
    {
        add_action('wp_enqueue_scripts', array($this, 'addFrontCSS'));
        add_action('wp_enqueue_scripts', array($this, 'addFrontJS'));
        return;
    }


    public function addFrontJS()
    {
        wp_enqueue_script('multitenantsjs', plugins_url().'/multitenants/assets/js/front.js', array('jquery'), '', true);
    }

    public function addFrontCSS()
    {
        wp_enqueue_style('multitenantscss', plugins_url().'/multitenants/assets/css/front.css', array(), $this->getVersion(), 'All');
    }
}




function doMTFXCallActivationBder()
{
    require_once plugin_dir_path(__FILE__) .'/inc/MultiTenant_MTFXActivator.php';
    MultiTenant_MTFXActivator::run();
}

function doMTFXCallDeactivationBder()
{
    require_once plugin_dir_path(__FILE__) . '/inc/MultiTenant_MTFXDeactivator.php';
    MultiTenant_MTFXDeactivator::run();
}

register_activation_hook(__FILE__, 'doMTFXCallActivationBder');

register_deactivation_hook(__FILE__, 'doMTFXCallDeactivationBder');

new MultiTenantBuilder();
