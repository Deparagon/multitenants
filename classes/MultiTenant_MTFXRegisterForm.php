<?php
/**
 * multitenants BY KINGSLEY PARAGON
 * HTTP://mrparagon.me/multitenants.
 */
if (!defined('ABSPATH')) {
    exit;
}
class MultiTenant_MTFXRegisterForm
{
    public function registerContent()
    {
        ob_start();
        ?>
    <div class="the-mtfx-form-register">
<div id="showdatregistrationState">  </div>
<!-- the form -->
<form action="" id="create_new_site_form_now" method="POST">
                            <div class="form-group">
                            <label for="username"> Username /Site Name </label>
                                <input type="text" class="form-control" name="Username" required="">
                            </div>

                              <div class="form-group">
                             <label for="site_title">Site Title </label>
                                <input type="text" class="form-control" name="site_title" required="" >
                            </div>

                             <div class="form-group">
                            <label for="password">Password </label>
                                <input type="password" class="form-control" name="Password" required="">
                            </div>

                            <div class="form-group">
                            <label for="email">Email </label>
                                <input type="email" class="form-control" name="Email" required="">
                            </div>
                            

                            <div class="form-group">
                            <label for="Firstname">First Name </label>
                                <input type="text" class="form-control" name="Firstname" required="">
                            </div>

                            <div class="form-group">
                            <label for ="lastname">Last Name </label>
                                <input type="text" class="form-control" name="Lastname" required="" >
                            </div>

                           

                            <div class="mbr-buttons mbr-buttons--right">
                            <button type="submit" class="mbr-buttons__btn btn btn-lg btn-primary btn-purpose pull-right">Create Site</button></div>
            
                       </form><!-- /form --> </div>
        <?php
        $html = ob_get_clean();

        return  $html;
    }


        public function siteContent()
        {
            ob_start();
            ?>
  <div class="the-mtfx-form-register">
<div id="showdatregistrationState">  </div>
<!-- the form -->
<form action="" id="create_new_site_form_by_existinguser" method="POST">
                            
                              <div class="form-group">
                             <label for="site_name">Site Name  </label>
                                <input type="text" class="form-control" name="site_name" required="">
                                 <span></span>
                            </div>

                             <div class="form-group">
                             <label for="password">Site Admin Password  </label>
                                <input type="password" class="form-control" name="password" required="" >
                            </div>
                            
                              <div class="form-group">
                             <label for="site_title">Site Title </label>
                                <input type="text" class="form-control" name="site_title" required="" >
                            </div>



                            <div class="mbr-buttons mbr-buttons--right"><button type="submit" class="mbr-buttons__btn btn 

btn-lg btn-primary btn-purpose pull-right">Create Site</button></div>
                        </form>

<!-- /form -->


</div>

<?php
        $content = ob_get_clean();
            return $content;
        }
}

?>