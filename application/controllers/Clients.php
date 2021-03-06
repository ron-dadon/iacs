<?php
/***********************************************************************************************************************
 * IACS Management System
 * ORT BRAUDE COLLEGE OF ENGINEERING
 * Information System Engineering - Final Project
 * Students: Ron Dadon, Guy Franco
 * Project adviser: PhD Miri Weiss-Cohen
 **********************************************************************************************************************/

namespace Application\Controllers;

use Application\Models\Clients as ClientsModel;
use Application\Entities\Client;

/**
 * Class Clients
 *
 * This class provides the logic layer for the clients data.
 *
 * @package Application\Controllers
 */
class Clients extends IacsBaseController
{

    /**
     * Show clients list.
     *
     * @throws \Trident\Exceptions\ModelNotFoundException
     */
    public function Index()
    {
        /** @var ClientsModel $clients */
        $clients = $this->loadModel('Clients');
        $list = $clients->getAll();
        if (($message = $this->pullSessionAlertMessage()) !== null)
        {
            $viewData[$message['type']] = $message['message'];
        }
        $viewData['clients'] = $list;
        $this->getView($viewData)->render();
    }

    /**
     * Add a new client.
     * Client information need to be passed via POST.
     *
     * @throws \Trident\Exceptions\IOException
     */
    public function Add()
    {
        $client = new Client();
        if ($this->getRequest()->isPost())
        {
            $data = $this->getRequest()->getPost()->toArray();
            $client->fromArray($data, "client_");
            if ($client->isValid())
            {
                $result = $this->getORM()->save($client);

                if ($result->isSuccess())
                {
                    $client->id = $result->getLastId();
                    $this->addLogEntry("Created client with ID: " . $client->id, "success");
                    $this->setSessionAlertMessage("Client " . $client->name . " created successfully.");
                    $this->redirect("/Clients/Show/" . $client->id);
                }
                else
                {
                    $viewData['error'] = "Error adding client to the database. Check the errors log for further information, or contact your system administrator.";
                    $this->getLog()->newEntry("Error adding client to database: " . $result->getErrorString(), "Database");
                    $this->addLogEntry("Failed to create a new client", "danger");
                }
            }
            else
            {
                $viewData['error'] = "Error adding client";
                $this->addLogEntry("Failed to create a new client - invalid data", "danger");
            }
        }
        $viewData['client'] = $client;
        $this->getView($viewData)->render();
    }

    /**
     * Delete client.
     * Client ID to delete need to be passed via POST delete_id.
     *
     * @throws \Trident\Exceptions\IOException
     * @throws \Trident\Exceptions\ModelNotFoundException
     */
    public function Delete()
    {
        if ($this->getRequest()->isPost())
        {
            /** @var ClientsModel $clients */
            $clients = $this->loadModel('Clients');
            try
            {
                $id = $this->getRequest()->getPost()->item('delete_id');
                $client = $clients->getById($id);
                if ($client === null)
                {
                    $this->addLogEntry("Failed to delete client - supplied ID is invalid", "danger");
                    if ($this->getRequest()->isAjax())
                    {
                        $this->jsonResponse(false);
                    }
                    else
                    {
                        $this->redirect("/Clients");
                    }
                }
                $result = $clients->delete($client);
                if ($result->isSuccess())
                {
                    $this->addLogEntry("Client with ID " . $id . " deleted successfully", "success");
                    if ($this->getRequest()->isAjax())
                    {
                        $this->jsonResponse(true, ['client' => addslashes(htmlspecialchars($client->name, ENT_NOQUOTES))]);
                    }
                    else
                    {
                        $this->setSessionAlertMessage("Client " . $client->name . " deleted successfully.");
                        $this->redirect("/Clients");
                    }
                }
                else
                {
                    $this->getLog()->newEntry("Failed to delete client with ID " . $id . ": " . $result->getErrorString(), "database");
                    $this->addLogEntry("Failed to delete client from the database. Check the errors log for further information, or contact your system administrator.", "danger");
                    if ($this->getRequest()->isAjax())
                    {
                        $this->jsonResponse(false);
                    }
                    else
                    {
                        $this->redirect("/Clients");
                    }
                }
            }
            catch (\InvalidArgumentException $e)
            {
                $this->addLogEntry("Failed to delete client - no ID supplied", "danger");
                if ($this->getRequest()->isAjax())
                {
                    $this->jsonResponse(false);
                }
                else
                {
                    $this->redirect("/Clients");
                }
            }
        }
    }

    /**
     * Show client profile.
     *
     * @param string|int $id Client ID.
     *
     * @throws \Trident\Exceptions\ModelNotFoundException
     */
    public function Show($id)
    {
        /** @var ClientsModel $clients */
        $clients = $this->loadModel('Clients');
        $client = $clients->getById($id);
        if ($client === null)
        {
            $this->setSessionAlertMessage("Can't show client with ID $id. Client was not found.", "error");
            $this->redirect("/Clients");
        }
        $viewData['client'] = $client;
        if (($message = $this->pullSessionAlertMessage()) !== null)
        {
            $viewData[$message['type']] = $message['message'];
        }
        $this->getView($viewData)->render();
    }

    /**
     * Update client.
     * Client updated information need to be passed via POST.
     *
     * @param string|int $id Client ID.
     *
     * @throws \Trident\Exceptions\IOException
     * @throws \Trident\Exceptions\ModelNotFoundException
     */
    public function Update($id)
    {
        /** @var ClientsModel $clients */
        $clients = $this->loadModel('Clients');
        $client = $clients->getById($id);
        if ($client === null)
        {
            $this->setSessionAlertMessage("Can't edit client with ID $id. Client was not found.", "error");
            $this->redirect("/Clients");
        }
        if ($this->getRequest()->isPost())
        {
            $data = $this->getRequest()->getPost()->toArray();
            $client->fromArray($data, "client_");
            if ($client->isValid())
            {
                $result = $this->getORM()->save($client);
                if ($result->isSuccess())
                {
                    $this->addLogEntry("Updated client with ID: " . $client->id, "success");
                    $this->setSessionAlertMessage("Client {$client->name} updated.", "success");
                    $this->redirect('/Clients/Show/' . $client->id);
                }
                else
                {
                    $viewData['error'] = "Error updating client to the database. Check the errors log for further information, or contact your system administrator.";
                    $this->getLog()->newEntry("Error updating client in the database: " . $result->getErrorString(), "Database");
                    $this->addLogEntry("Failed to update client", "danger");
                }
            }
            else
            {
                $viewData['error'] = "Error updating client";
                $this->addLogEntry("Failed to update client - invalid data", "danger");
            }
        }
        $viewData['client'] = $client;
        if (($message = $this->pullSessionAlertMessage()) !== null)
        {
            $viewData[$message['type']] = $message['message'];
        }
        $this->getView($viewData)->render();
    }

}