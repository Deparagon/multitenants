<?php
/**
 * DESCRIPTION.
 *
 *   Multitenants Sites List
 *
 *  @author    Paragon Kingsley
 *  @copyright 2022 Paragon Kingsley
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License ("AFL") v. 3.0
 */

if (!defined('ABSPATH')) {
    exit;
}


class MultiTenant_MTFXSiteList
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'addMainMenu'));
        add_action('wp_ajax_mtfxajaxDeleteSiteByAdminPHP', array($this, 'mtfxajaxDeleteSiteByAdminPHP'));
    }


    public function addMainMenu()
    {
        add_submenu_page('multitenant_config_page', 'Site List', 'Tenants List', 'manage_options', 'multitenant_tenants_page', array($this, 'thePage'), 1);
    }


    public function mtfxajaxDeleteSiteByAdminPHP()
    {
        require_once plugin_dir_path(dirname(__FILE__)).'classes/MultiTenant_MTFXSite.php';

        $id_site = (int) $_POST['id_site'];
        if($id_site > 0) {
            (new MultiTenant_MTFXSite())->deleteD($id_site);
            echo 'OK';
            wp_die();
        }

        echo 'NK';
        wp_die();
    }


    public function thePage()
    {
        require_once plugin_dir_path(dirname(__FILE__)).'classes/MultiTenant_MTFXSite.php';
        $so = new MultiTenant_MTFXSite();
        $allsites =$so->getAllSites();

        ?>

   

<div class="admin-main-container-tenants">
<div class="card">
    <div class="card-header bg-white"><?php esc_html_e('Sites & Users', 'multitenants'); ?> </div>
  <div class="card-body">
    <div class="table-responsive">
    <table id="mytenants_classic_table" class="table table-striped table-bordered">
        <thead>
            <th><?php esc_html_e('Site Name', 'multitenants'); ?> </th>  <th><?php esc_html_e('Title', 'multitenants'); ?> </th>  <th><?php esc_html_e('User', 'multitenants'); ?> </th>  <th><?php esc_html_e('Admin Email', 'multitenants'); ?> </th>  <th><?php esc_html_e('Registered', 'multitenants'); ?> </th>  <th><?php esc_html_e('Status', 'multitenants'); ?> </th> <th>Delete</th> 
        </thead>
        <tbody>
            <?php if (count($allsites) >0) :
                foreach ($allsites as $site) : ?>
                    <tr>
                    <td><?php echo esc_html($site->site_login); ?></td>
                    <td><?php echo esc_html($site->site_title); ?></td>
                    <td><?php echo esc_html($site->display_name); ?></td>
                    <td><?php echo esc_html($site->admin_email); ?></td>
                    <td><?php echo esc_html($site->date_add); ?></td>
                    <td><?php echo esc_html($site->status); ?></td>
                   
                    <td><button data-id_site="<?php echo esc_html($site->id_site); ?>" data-action="delete" class="btn btn-danger btn-small proceed_to_delete_site_local"> Delete</button></td>
                    
                </tr>

                <?php endforeach;
            else :
                ?>
                 <tr> <td colspan="9"><?php esc_html_e('No Sites Yet', 'multitenants'); ?></td></tr>
            <?php endif; ?>

            
        </tbody>
    </table>
</div>

  </div>
</div>
</div>


<?php
if(count($allsites) >0): ?>
<script>
    jQuery(document).ready(function(){
    jQuery('#mytenants_classic_table').DataTable();
    });

  jQuery('body').on('click', '.proceed_to_delete_site_local', function(ev){
       ev.preventDefault();
         let tr = jQuery(this).parents('tr');
        let data = {id_site:jQuery(this).data('id_site'), 'action':'mtfxajaxDeleteSiteByAdminPHP'};

       let cfirm = confirm('Are you sure that you want to delete this site');
       if(cfirm ==true){

           jQuery.post(ajaxurl, data, function(report){

            if(report=='OK'){
                   tr.remove();
            }

           });
       }





  });

 </script>


<?php endif; ?>



<div class="right_side_alert_message"></div>

        <?php
    }
}



new MultiTenant_MTFXSiteList();
