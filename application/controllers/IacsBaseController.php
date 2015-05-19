<?php


namespace Application\Controllers;

use application\Entities\LogEntry;
use Trident\MVC\AbstractController;
use Trident\Exceptions\ViewNotFoundException;
use Trident\MVC\AbstractView;
use Application\Entities\User;

class IacsBaseController extends AbstractController
{

    /**
     * @var \Application\Models\Logs
     */
    private $logs;

    function __construct($configuration, $log, $request, $session)
    {
        parent::__construct($configuration, $log, $request, $session);
        $this->loadMySql();
        $this->loadORM();
        $this->logs = $this->loadModel('Logs');
        try
        {
            /** @var User $user */
            $user = unserialize($this->getSession()->item('iacs-logged-user'));
            $autoLogout = $this->getConfiguration()->item('user.security.allow-auto-logout');
            $idleTime = $this->getConfiguration()->item('user.security.auto-logout-time');
            $lastActive = \DateTime::createFromFormat("Y-m-d H:i:s", $user->lastActive);
            $now = new \DateTime();
            if ($now->diff($lastActive, true)->i > $autoLogout)
            {
                $this->getSession()->destroy();
                $this->redirect("/login");
            }
            $user->lastActive = $now->format('Y-m-d H:i:s');
            $this->getORM()->save($user);
        }
        catch (\InvalidArgumentException $e)
        {
            // Do nothing
        }
    }

    /**
     * Load view instance.
     * If $view is not specified, loads the view according to the calling callable.
     *
     * @param array $viewData View data array.
     * @param null  $viewName View name.
     *
     * @throws ViewNotFoundException
     * @return AbstractView View instance.
     */
    protected function getView($viewData = [], $viewName = null)
    {
        $reflect = new \ReflectionClass($this);
        $class = $reflect->getShortName();
        $viewData['currentMenuItem'] = $class;
        try
        {
            $user = $this->getSession()->item('iacs-logged-user');
        }
        catch (\InvalidArgumentException $e)
        {
            $user = null;
        }
        if (isset($user))
        {
            $viewData['currentUser'] = unserialize($user);
        }
        if (is_null($viewName))
        {
            $viewName = debug_backtrace()[1]['function'];
            $viewName = "$class\\$viewName";
        }
        return parent::getView($viewData, $viewName);
    }

    /**
     * Check if user is logged in.
     *
     * @return bool True if logged in, false otherwise.
     */
    protected function isUserLogged()
    {
        try
        {
            $this->getSession()->item('iacs-logged-user');
            return true;
        }
        catch (\InvalidArgumentException $e)
        {
            return false;
        }
    }

    /**
     * Get the logged user.
     *
     * @return User The loggged user entity.
     */
    protected function getLoggedUser()
    {
        /** @var User $user */
        $user = unserialize($this->getSession()->item('iacs-logged-user'));
        return $user;
    }

    protected function jsonResponse($result, $details = [])
    {
        $json = ['result' => $result, 'details' => $details];
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    protected function addLogEntry($entry, $level = "info")
    {
        $logEntry = new LogEntry();
        $logEntry->browser = $this->getRequest()->getBrowser() . '(' . $this->getRequest()->getBrowserVersion() . ')';
        $logEntry->platform = $this->getRequest()->getPlatform();
        $logEntry->ip = $this->getRequest()->getIp();
        /** @var User $user */
        $user = unserialize($this->getSession()->item('iacs-logged-user'));
        $logEntry->user = $user->id;
        $logEntry->entry = $entry;
        $logEntry->level = $level;
        $this->getORM()->save($logEntry);
    }
}