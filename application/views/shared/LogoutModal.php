<?php
/***********************************************************************************************************************
 * IACS Management System
 * ORT BRAUDE COLLEGE OF ENGINEERING
 * Information System Engineering - Final Project
 * Students: Ron Dadon, Guy Franco
 * Project adviser: PhD Miri Weiss-Cohen
 **********************************************************************************************************************/

namespace Application\Views\Shared;

use Trident\MVC\AbstractView;

/**
 * Class LogoutModal
 *
 * Logout modal widget.
 *
 * @package Application\Views\Shared
 */
class LogoutModal extends AbstractView
{

    /**
     * Render logout modal widget.
     */
    public function render() { ?>
    <!-- Logout modal -->
    <div class="modal fade" id="logout-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="fa fa-fw fa-power-off"></i> Logout</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to logout?</p>
                    <p><span class="bg-error padded-5px">Warning:</span> Any unsaved work will be lost.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-fw fa-times"></i> Cancel</button>
                    <a href="<?php $this->publicPath() ?>Logout" class="btn btn-danger"><i class="fa fa-fw fa-check"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Logout modal -->
<?php
    }

} 