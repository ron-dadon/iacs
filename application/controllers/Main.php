<?php

namespace Application\Controllers;

use Application\Models\Users;
use Application\Entities\User;

class Main extends IacsBaseController
{

    public function Index()
    {
        if (!$this->isUserLogged())
        {
            $this->redirect("/Login");
        }
        $this->getView()->render();
    }

    public function Settings()
    {
        if (!$this->isUserLogged())
        {
            $this->redirect("/Login");
        }
        $this->getView()->render();
    }

    public function Login()
    {
        if ($this->isUserLogged())
        {
            $this->redirect("/");
        }
        if ($this->getRequest()->isAjax())
        {
            list($email, $password) = [$this->getRequest()->getPost()->item('user_email'),
                                       $this->getRequest()->getPost()->item('user_password')];
            /** @var Users $model */
            $model = $this->loadModel('Users');
            /** @var User[]|User|null $user */
            $user = $model->search("user_email = ?", [$email]);
            if ($user === null || count($user) !== 1)
            {
                $this->jsonResponse(false);
            }
            $user = $user[0];
            if (!password_verify($password, $user->password))
            {
                $this->jsonResponse(false);
            }
            $this->getSession()->set('iacs-logged-user', serialize($user));
            $this->jsonResponse(true);
        }
        $this->getView()->render();
    }

    public function Logout()
    {
        $this->getSession()->destroy();
        if (!$this->getRequest()->isAjax())
        {
            $this->redirect("/");
        }
        else
        {
            echo "OK";
        }
    }

} 