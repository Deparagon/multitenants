<?php
/**
 * DESCRIPTION.
 *
 *   Multitenants Sites
 *
 *  @author    Paragon Kingsley
 *  @copyright 2022 Paragon Kingsley
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License ("AFL") v. 3.0
 */

if (!defined('ABSPATH')) {
    exit;
}
class MultiTenant_MTFXConfig
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'configTent'));
        add_action('admin_menu', array($this, 'addMainMenu'));
    }


    public function addMainMenu()
    {
        add_menu_page(esc_html__('MultiTenant Settings', 'multitenants'), esc_html__('MultiTenants Config', 'multitenants'), 'manage_options', 'multitenant_config_page', array($this, 'ConfigurationContent'), 'dashicons-exerpt-view', '3.3');
    }


    public function configTent()
    {
        register_setting('mtfx_set_multitenant', 'MTFX_MAIN_DOMAIN', ['type'=>'string','sanitize_callback'=>array($this, 'validateReturnString')]);

        register_setting('mtfx_set_multitenant', 'MTFX_WORKING_WPMULTI_DIR', ['type'=>'string','sanitize_callback'=>array($this, 'validateReturnString')]);
        register_setting('mtfx_set_multitenant', 'MTFX_THE_DOMAIN_HOST', ['type'=>'string','sanitize_callback'=>array($this, 'validateReturnString')]);
        register_setting('mtfx_set_multitenant', 'MTFX_KEEP_LOCAL_EMAIL_LOGS', ['type'=>'string','sanitize_callback'=>array($this, 'validateReturnString')]);
    }



    public function ConfigurationContent()
    {
        $MTFX_MAIN_DOMAIN = esc_html(get_option('MTFX_MAIN_DOMAIN'));
        $MTFX_WORKING_WPMULTI_DIR = esc_html(get_option('MTFX_WORKING_WPMULTI_DIR'));
        $MTFX_THE_DOMAIN_HOST = esc_html(get_option('MTFX_THE_DOMAIN_HOST'));
        $MTFX_KEEP_LOCAL_EMAIL_LOGS = esc_html(get_option('MTFX_KEEP_LOCAL_EMAIL_LOGS'));
        settings_errors();



        ?>
<div class="admin-main-container-tenants">
<form method="post" action="options.php">
        <?php settings_fields('mtfx_set_multitenant');
        ?>
<div class="card">
    <div class="card-header bg-white"><?php esc_html_e('Settings', 'multitenants'); ?> </div>
  <div class="card-body">
    <div class="row">
   <div class="col-lg-7 col-sm-7 col-12">


<div class="row mb-3">
    <label for="MTFX_MAIN_DOMAIN" class="col-sm-4 form-check-label col-form-label col-form-label-sm">
        <?php esc_html_e('Domain', 'multitenants'); ?>
  </label>
    <div class="col-sm-8">
        <input class="form-control" value="<?php echo $MTFX_MAIN_DOMAIN; ?>"  name="MTFX_MAIN_DOMAIN" id="MTFX_MAIN_DOMAIN">
        <span>Domain for the mainsite </span>
    </div>
</div>


<div class="row mb-3">
    <label for="MTFX_THE_DOMAIN_HOST" class="col-sm-4 form-check-label col-form-label col-form-label-sm">
        <?php esc_html_e('Domain Host (http/https)', 'multitenants'); ?>
  </label>
    <div class="col-sm-8">
        <select class="form-control" name="MTFX_THE_DOMAIN_HOST" id="MTFX_THE_DOMAIN_HOST">
           <option value="http" <?php if ($MTFX_THE_DOMAIN_HOST=='http') {
               echo 'selected';
           }?>> HTTP</option>
           <option value="https"  <?php if ($MTFX_THE_DOMAIN_HOST=='https') {
               echo 'selected';
           }?>> HTTPS</option>
        </select>
    </div>
</div>

<div class="row mb-3">
    <label for="MTFX_WORKING_WPMULTI_DIR" class="col-sm-4 form-check-label col-form-label col-form-label-sm">
        <?php esc_html_e('WP-multitenant Folder /Directory location', 'multitenants'); ?>
  </label>
    <div class="col-sm-8">
        <input class="form-control" placeholder="/var/www/theuser/wp-multitenant" value="<?php echo $MTFX_WORKING_WPMULTI_DIR; ?>"  name="MTFX_WORKING_WPMULTI_DIR" id="MTFX_WORKING_WPMULTI_DIR">
        <span>E.g /var/www/theuser/wp-multitenant </span>
    </div>
</div>





<div class="row mb-3">
    <label for="MTFX_KEEP_LOCAL_EMAIL_LOGS" class="col-sm-4 form-check-label col-form-label col-form-label-sm">
        <?php esc_html_e('Keep Email Logs?', 'multitenants'); ?>
  </label>
    <div class="col-sm-8">
        <select class="form-control" name="MTFX_KEEP_LOCAL_EMAIL_LOGS" id="MTFX_KEEP_LOCAL_EMAIL_LOGS">
           <option value="No" <?php if ($MTFX_KEEP_LOCAL_EMAIL_LOGS=='No') {
               echo 'selected';
           }?>> No</option>
           <option value="Yes"  <?php if ($MTFX_KEEP_LOCAL_EMAIL_LOGS=='Yes') {
               echo 'selected';
           }?>> Yes</option>
        </select>
    </div>
</div>

</div>

<div class="col-lg-5 col-sm-5 col-12 col-xs-12">
    <div class="holding_messages_notes">
        <p>Note: If your main wordpress site is located in /var/www/$theuser/my-main-wp-site.com, the plugin will create new site folder on the same level with your main wp site  <br> e.g /var/www/$theuser/mysitetwo <br> /var/www/$theuser/mysitethree</p>
   
<p>Note: All DNS and apache configuration relating to documentRoot and subdomain needs to be set properly. </p>
<p> If you main domain name is mydomain.com new sites will be created as subdomain e.g newsite.mydomain.com </p>
<p> If the main domain is localhost, the site will be created in localhost/newsite</p>

    </div>
</div>
</div>


</div>
  
  <div class="card-footer">
       <div class="col-12">
    <button class="btn btn-primary" type="submit"><?php esc_html_e('Save', 'multitenants'); ?></button>
  </div>
  </div>
</div>
</form>
</div>


        <?php
    }

    public function validateReturnString($stext)
    {
        return sanitize_text_field($stext);
    }

    public function validateReturnInt($value)
    {
        return (int) $value;
    }

    public function validateReturnNumber($no)
    {
        return (float) $no;
    }
}// close class


new MultiTenant_MTFXConfig();
