<?php
/**
 * DESCRIPTION.
 *
 *   MultiTenant Tools
 *
 *  @author    Paragon Kingsley
 *  @copyright 2022 Paragon Kingsley
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License ("AFL") v. 3.0
 */

class MultiTenant_MTFXTools
{
    public static function naError($e)
    {
        echo'<div class="alert alert-danger" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close">
  <span aria-hidden="true">&times;</span>
</button>'.$e.'</div>';
    }

    public static function naSuccess($s)
    {
        echo'<div class="alert alert-success" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close">
  <span aria-hidden="true">&times;</span>
</button>'.$s.'</div>';
    }


    public static function naInfo($s)
    {
        echo'<div class="alert alert-info" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close">
  <span aria-hidden="true">&times;</span>
</button>'.$s.'</div>';
    }
}
