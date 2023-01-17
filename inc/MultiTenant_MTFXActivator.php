<?php

/**
 * DESCRIPTION.
 *
 *   MultiTenant Activator
 *
 *  @author    Paragon Kingsley
 *  @copyright 2022 Paragon Kingsley
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License ("AFL") v. 3.0
 */


class MultiTenant_MTFXActivator
{
    public static function run()
    {
        ob_start();
        self::createRequiredTables();
        add_option('MULTITENANT_MTFXDB_VERSION', 1);
        self::autoCreatePages();
        add_option('MULTITENANT_MTFX_PLUGINVERSION', '1.0.0');
        add_option('MTFX_KEEP_LOCAL_EMAIL_LOGS', 'No');
        ob_clean();
        return true;
    }



    public static function createRequiredTables()
    {
        global $wpdb;
        WP_Filesystem();
        global $wp_filesystem;
        require_once ABSPATH.'wp-admin/includes/upgrade.php';
        $sql = $wp_filesystem->get_contents(plugin_dir_path(dirname(__FILE__)).'install/install.sql');
        $sql_content = str_replace(['PREFIX_'], [$wpdb->prefix], $sql);
        $sqls = explode(';', $sql_content);
        if (count($sqls)>0) {
            foreach ($sqls as $sq) {
                dbDelta($sq);
            }
        }

        return true;
    }





        public static function autoCreatePages()
        {
            $pp_pages = array('mtfxregister');

            foreach ($pp_pages as $page) {
                switch ($page) {
                    case 'mtfxregister':
                        $pagetitle = 'Create Your own Site';
                        break;

                    default:
                        $pagetitle = $page.' page';
                        break;
                }
                if (!get_option($page)) {
                    $pagecreator = array(
    'post_title' => $pagetitle,
    'post_content' => '['.$page.']',
    'post_status' => 'publish',
    'post_type' => 'page',
    'post_author' => get_current_user_id(),
    'post_date' => date('Y-m-d H:i:s'),
);
                    $pageid = wp_insert_post($pagecreator);
                    add_option($page, $pageid);
                }
            }
        }
}
