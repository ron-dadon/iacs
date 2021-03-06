<?php
/***********************************************************************************************************************
 * IACS Management System
 * ORT BRAUDE COLLEGE OF ENGINEERING
 * Information System Engineering - Final Project
 * Students: Ron Dadon, Guy Franco
 * Project adviser: PhD Miri Weiss-Cohen
 **********************************************************************************************************************/

namespace Application\Views\Administration;

use \Trident\MVC\AbstractView;
use Application\Entities\User;

/**
 * Class NewUser
 *
 * Show a new user form.
 *
 * @package Application\Views\Administration
 */
class NewUser extends AbstractView
{

    /**
     * Render a new user form.
     *
     * @throws \Trident\Exceptions\ViewNotFoundException
     */
    public function render()
    {
        /** @var User $user */
        $user = $this->data['user'];
        $this->getSharedView('Header')->render();
        $this->getSharedView('TopBar')->render();
        $this->getSharedView('SideBar')->render(); ?>
<div class="container-fluid">
    <div class="page-head bg-main">
        <h1><i class="fa fa-fw fa-user-plus"></i> New user</h1>
    </div>
<?php if (isset($this->data['error'])): ?>
    <div class="alert alert-dismissable alert-danger fade in">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 <?php if ($user->getErrors() !== null && count($user->getErrors()) > 0): ?>class="margin-bottom"<?php endif; ?>><i class="fa fa-fw fa-times-circle"></i><?php echo $this->data['error'] ?></h4>
<?php if ($user->getErrors() !== null && count($user->getErrors()) > 0): ?>
        <ul>
<?php foreach ($user->getErrors() as $error): ?>
            <li><?php echo $error ?></li>
<?php endforeach; ?>
        </ul>
<?php endif; ?>
    </div>
<?php endif; ?>
    <form method="post" id="new-user-form" data-toggle="validator">
    <div class="panel">
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-lg-3">
                    <div class="form-group">
                        <label for="user-email">EMail:</label>
                        <input type="email" id="user-email" name="user_email" class="form-control" value="<?php echo $this->escape($user->email) ?>" required autofocus data-error="Please enter a valid e-mail address">
                        <div class="help-block with-errors" id="email-error"></div>
                    </div>
                </div>
                <div class="col-xs-12 col-lg-3">
                    <div class="form-group">
                        <label for="user-firstName">First name:</label>
                        <input type="text" id="user-firstName" name="user_firstName" class="form-control" value="<?php echo $this->escape($user->firstName) ?>" required maxlength="20" data-error="Please fill in first name">
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="col-xs-12 col-lg-3">
                    <div class="form-group">
                        <label for="user-lastName">Last name:</label>
                        <input type="text" id="user-lastName" name="user_lastName" class="form-control" value="<?php echo $this->escape($user->lastName) ?>" required maxlength="20" data-error="Please fill in last name">
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="col-xs-12 col-lg-3">
                    <div class="form-group">
                        <label for="user-admin">Admin</label>
                        <select id="user-admin" name="user_admin" class="form-control" required>
                            <option value="1" <?php if ($user->admin) echo 'selected' ?>>Yes</option>
                            <option value="0" <?php if (!$user->admin) echo 'selected' ?>>No</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-lg-3">
                    <div class="form-group">
                        <label for="user-password">Password:</label>
                        <input type="text" id="user-password" name="user_password" class="form-control" value="<?php echo $this->escape($user->password) ?>" required pattern="^.{6,20}$" data-error="Password must be at least 6 characters long and not exceed 20 characters">
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="col-xs-12 col-lg-3">
                    <div class="form-group">
                        <label for="user-confirm-password">Confirm password:</label>
                        <input type="text" id="user-confirm-password" name="user_confirm_password" class="form-control" required data-match="#user-password" data-match-error="Password does not match  " data-error="Please repeat the password">
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel">
            <div class="panel-footer text-right">
                <a href="<?php $this->publicPath() ?>Administration/Users" class="btn btn-link hidden-xs">Back</a>
                <button type="submit" class="btn btn-primary hidden-xs"><i class="fa fa-fw fa-plus"></i> Save user</button>
                <button type="submit" class="btn btn-primary btn-block visible-xs"><i class="fa fa-fw fa-plus"></i> Save user</button>
                <a href="<?php $this->publicPath() ?>Administration/Users" class="btn btn-link btn-block visible-xs">Back</a>
            </div>
        </div>
    </div>
    </form>
</div>
<script src="<?php $this->publicPath() ?>js/administration/user-new.js?<?php echo date('YmdHis') ?>"></script>
<?php
        $this->getSharedView('ConfirmModal')->render();
        $this->getSharedView('MessageModal')->render();
        $this->getSharedView('Footer')->render();

    }

}