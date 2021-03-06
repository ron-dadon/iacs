<?php
/***********************************************************************************************************************
 * IACS Management System
 * ORT BRAUDE COLLEGE OF ENGINEERING
 * Information System Engineering - Final Project
 * Students: Ron Dadon, Guy Franco
 * Project adviser: PhD Miri Weiss-Cohen
 **********************************************************************************************************************/

namespace Application\Controllers;

use Application\Entities\User;
use Application\Models\Logs;
use Application\Models\Users;

/**
 * Class Administration
 *
 * This class provides the logic layer for the administration area.
 *
 * @package Application\Controllers
 */
class Administration extends IacsBaseController
{

    /**
     * Create a new instance.
     *
     * Allow only logged in admin users to access this controller.
     * If a un-logged user or an un-admin user is trying to access the controller,
     * redirect him the the error screen.
     *
     * @param \Trident\System\Configuration $configuration
     * @param \Trident\System\Log           $log
     * @param \Trident\System\Request       $request
     * @param \Trident\System\Session       $session
     */
    function __construct($configuration, $log, $request, $session)
    {
        parent::__construct($configuration, $log, $request, $session);
        if (!$this->isUserLogged() || !$this->getLoggedUser()->admin)
        {
            $this->getLog()->newEntry("User tried to access Administration area without having the right privilege", "danger");
            $this->addLogEntry("Access to administration denied", "danger");
            $this->redirect("/Error");
        }
    }

    /**
     * Show the administration index view.
     */
    public function Index()
    {
        $this->getView()->render();
    }

    /**
     * Show administration log of events in the system.
     *
     * @throws \Trident\Exceptions\ModelNotFoundException
     */
    public function Log()
    {
        /** @var Logs $logs */
        $logs = $this->loadModel('Logs');
        $list = $logs->getAll();
        $viewData['entries'] = $list;
        $this->getView($viewData)->render();
    }

    /**
     * Show system users list.
     *
     * @throws \Trident\Exceptions\ModelNotFoundException
     */
    public function Users()
    {
        /** @var Users $users */
        $users = $this->loadModel('Users');
        $list = $users->getAll();
        if (($message = $this->pullSessionAlertMessage()) !== null)
        {
            $viewData[$message['type']] = $message['message'];
        }
        $viewData['users'] = $list;
        $this->getView($viewData)->render();
    }

    /**
     * Delete a user from the system.
     * User ID for delete need to be supplied via POST delete_id.
     *
     * @throws \Trident\Exceptions\IOException
     * @throws \Trident\Exceptions\ModelNotFoundException
     */
    public function DeleteUser()
    {
        if ($this->getRequest()->isAjax())
        {
            /** @var Users $users */
            $users = $this->loadModel('Users');
            try
            {
                $id = $this->getRequest()->getPost()->item('delete_id');
                $user = $users->getById($id);
                if ($user === null)
                {
                    $this->addLogEntry("Delete of user with the ID $id failed. No user with this ID was found", "danger");
                    $this->jsonResponse(false);
                }
                if ($user->id === $this->getLoggedUser()->id) {
                    $this->addLogEntry("Delete of user with the ID $id failed. User can't delete it self.", "danger");
                    $this->jsonResponse(false);
                }
                $result = $users->delete($user);
                if ($result->isSuccess())
                {
                    $this->addLogEntry("Successfully deleted user with the ID $id", "success");
                    $this->jsonResponse(true, ['user' => $user->firstName . ' ' . $user->lastName]);
                }
                else
                {
                    $this->addLogEntry("Delete of user with the ID $id failed", "danger");
                    $this->getLog()->newEntry($result->getErrorString(), "database");
                    $this->jsonResponse(false, ['user' => $user->firstName . ' ' . $user->lastName]);
                }
            }
            catch (\InvalidArgumentException $e)
            {
                $this->getLog()->newEntry($e->getMessage(), "administration");
                $this->jsonResponse(false);
            }
        }
        $this->redirect("/Error");
    }

    /**
     * Check if user exists by his e-mail address.
     *
     * @param string $email E-Mail address.
     *
     * @return bool
     * @throws \Trident\Exceptions\EntityNotFoundException
     */
    public function IsUserExists($email)
    {
        $result = $this->getORM()->find('User', 'user_email = :email AND user_delete = 0', [':email' => $email]);
        if ($result === null || count($result) === 0)
        {
            return false;
        }
        return true;
    }

    /**
     * Add a new user.
     *
     * @throws \Trident\Exceptions\IOException
     */
    public function NewUser()
    {
        $user = new User();
        if ($this->getRequest()->isPost())
        {
            $data = $this->getRequest()->getPost()->toArray();
            $user->fromArray($data, "user_");
            if (!$user->isValid() || $user->password !== $this->getRequest()->getPost()->item('user_confirm_password'))
            {
                $viewData['error'] = "Can not add user. The following fields are invalid:";
            }
            elseif ($this->IsUserExists($user->email))
            {
                $viewData['error'] = "Can not add user. User email already exists.";
            }
            else
            {
                $user->password = password_hash($user->password, PASSWORD_BCRYPT);
                $result = $this->getORM()->save($user);
                if ($result->isSuccess())
                {
                    $this->setSessionAlertMessage("The user " . htmlspecialchars($user->firstName . ' ' . $user->lastName) . " was successfully created.");
                    $this->addLogEntry("Successfully created a new user with the ID " . $result->getLastId(), "success");
                    $this->redirect("/Administration/Users");
                }
                else
                {
                    $this->addLogEntry("Creation of new user failed", "danger");
                    $this->getLog()->newEntry($result->getErrorString(), "database");
                    $viewData['error'] = "Can not add user.";
                }
            }
        }
        $viewData['user'] = $user;
        $this->getView($viewData)->render();
    }

    /**
     * Update a user in the system.
     * User updated properties need to be passed via POST.
     *
     * @param string|int $id User ID.
     *
     * @throws \Trident\Exceptions\IOException
     * @throws \Trident\Exceptions\ModelNotFoundException
     */
    public function UpdateUser($id)
    {
        /** @var Users $users */
        $users = $this->loadModel('Users');
        $user = $users->getById($id);
        if ($this->getRequest()->isPost())
        {
            if ($this->getRequest()->getPost()->item('user_password') !== $this->getRequest()->getPost()->item('user_confirm_password'))
            {
                $viewData['error'] = "Can not update user. Passwords don't match.";
            }
            else
            {
                $oldPassword = '';
                $data = $this->getRequest()->getPost()->toArray();
                if ($this->getRequest()->getPost()->item('user_password') === '')
                {
                    unset($data['user_password']);
                    $oldPassword = $user->password;
                    $user->password = '123456';
                }
                $user->fromArray($data, "user_");
                if (!$user->isValid())
                {
                    $viewData['error'] = "Can not update user. The following fields are invalid:";
                    $user = $users->getById($id);
                }
                else
                {
                    if ($this->getRequest()->getPost()->item('user_password') !== '')
                    {
                        $user->password = password_hash($user->password, PASSWORD_BCRYPT);
                    }
                    else
                    {
                        $user->password = $oldPassword;
                    }
                    $result = $this->getORM()->save($user);
                    if ($result->isSuccess())
                    {
                        $this->setSessionAlertMessage("The user " . htmlspecialchars($user->firstName . ' ' . $user->lastName) . " was successfully updated.");
                        $this->addLogEntry("Successfully updated user with the ID $id", "success");
                        $this->redirect('/Administration/Users');
                    }
                    else
                    {
                        $this->addLogEntry("Update of user with ID $id failed", "danger");
                        $this->getLog()->newEntry($result->getErrorString(), "database");
                        $viewData['error'] = "Can not update user.";
                    }
                }
            }
        }
        $viewData['user'] = $user;
        $this->getView($viewData)->render();
    }

    /**
     * Show and update system settings.
     * Settings update need to be passed via POST using AJAX.
     *
     * @throws \Trident\Exceptions\IOException
     */
    public function Settings()
    {
        if ($this->getRequest()->isAjax())
        {
            try
            {
                $securityAllowIdle = $this->getRequest()->getPost()->item('securityAllowIdle');
                if ($securityAllowIdle !== '0' && $securityAllowIdle !== '1')
                {
                    $this->addLogEntry("Failed system settings update", "danger");
                    $this->jsonResponse(false);
                }
                $securityIdleTime = $this->getRequest()->getPost()->item('securityIdleTime');
                if (!filter_var($securityIdleTime, FILTER_VALIDATE_INT) || ($securityIdleTime < 0 || $securityIdleTime > 120))
                {
                    $this->addLogEntry("Failed system settings update", "danger");
                    $this->jsonResponse(false);
                }
                $emailHost = $this->getRequest()->getPost()->item('emailHost');
                if (!preg_match('/^[a-z0-9A-Z\:\/\.\-]{0,}$/', $emailHost))
                {
                    $this->addLogEntry("Failed system settings update", "danger");
                    $this->jsonResponse(false);
                }
                $emailPort = $this->getRequest()->getPost()->item('emailPort');
                if (!filter_var($emailPort, FILTER_VALIDATE_INT) || ($emailPort < 0 || $emailPort > 65535))
                {
                    $this->addLogEntry("Failed system settings update", "danger");
                    $this->jsonResponse(false);
                }
                $emailSecurity = $this->getRequest()->getPost()->item('emailSecurity');
                if (array_search($emailSecurity, ['none', 'ssl', 'tls']) === false)
                {
                    $this->addLogEntry("Failed system settings update", "danger");
                    $this->jsonResponse(false);
                }
                $emailUser = $this->getRequest()->getPost()->item('emailUser');
                if (!preg_match('/^[a-zA-Z0-9\.\-\@\_]{0,}$/', $emailUser))
                {
                    $this->addLogEntry("Failed system settings update", "danger");
                    $this->jsonResponse(false);
                }
                $tax = $this->getRequest()->getPost()->item('tax');
                if (!preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $tax))
                {
                    $this->addLogEntry("Failed system settings update", "danger");
                    $this->jsonResponse(false);
                }
                $emailPassword = $this->getRequest()->getPost()->item('emailPassword');
                $this->getConfiguration()->set('user.security.allow-auto-logout', $securityAllowIdle === '1');
                $this->getConfiguration()->set('user.security.auto-logout-time', intval($securityIdleTime));
                $this->getConfiguration()->set('user.email.host', $emailHost);
                $this->getConfiguration()->set('user.email.port', intval($emailPort));
                $this->getConfiguration()->set('user.email.security', $emailSecurity);
                $this->getConfiguration()->set('user.email.user', $emailUser);
                $this->getConfiguration()->set('user.email.password', stripslashes($emailPassword));
                $this->getConfiguration()->set('user.general.tax', $tax);
                $this->getConfiguration()->save(CONFIGURATION_FILE);
                $this->addLogEntry("Successful system settings update", "success");
                $this->jsonResponse(true);
            }
            catch (\Exception $e)
            {
                $this->addLogEntry("Failed system settings update", "danger");
                $this->getLog()->newEntry($e->getMessage(), "administration");
                $this->jsonResponse(false);
            }
        }
        $viewData['security-idle-logout'] = $this->getConfiguration()->item('user.security.allow-auto-logout');
        $viewData['security-idle-logout-time'] = $this->getConfiguration()->item('user.security.auto-logout-time');
        $viewData['email-host'] = $this->getConfiguration()->item('user.email.host');
        $viewData['email-port'] = $this->getConfiguration()->item('user.email.port');
        $viewData['email-security'] = $this->getConfiguration()->item('user.email.security');
        $viewData['email-user'] = $this->getConfiguration()->item('user.email.user');
        $viewData['email-password'] = $this->getConfiguration()->item('user.email.password');
        $viewData['tax'] = $this->getConfiguration()->item('user.general.tax');
        $this->getView($viewData)->render();
    }

}