<?php

namespace Application\Views\Clients;

use \Trident\MVC\AbstractView;
use Application\Entities\Client;
use Application\Entities\Contact;

class Show extends AbstractView
{

    public function render()
    {
        /** @var Client $client */
        $client = $this->data['client'];
        /** @var Contact[] $contacts */
        $contacts = $this->data['contacts'];
        $this->getSharedView('Header')->render();
        $this->getSharedView('TopBar')->render();
        $this->getSharedView('SideBar')->render(); ?>
<div class="container-fluid">
    <div class="page-head bg-main">
        <h1><i class="fa fa-fw fa-user"></i> Client: <?php echo $this->escape($client->name) ?></h1>
    </div>
<?php if (isset($this->data['error'])): ?>
    <div class="alert alert-dismissable alert-danger fade in">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4><i class="fa fa-fw fa-times-circle"></i><?php echo $this->escape($this->data['error']) ?></h4>
    </div>
<?php endif; ?>
    <div class="panel">
        <div class="row">
            <div class="col-xs-12 col-lg-6">
                <h3 class="bg-main margin-bottom padded-5px"><i class="fa fa-fw fa-suitcase"></i> Contact information:</h3>
                <p><strong><i class="fa fa-fw fa-phone"></i> Phone:</strong> <?php echo $this->escape($client->phone) ?></p>
                <p><strong><i class="fa fa-fw fa-map-marker"></i> Address:</strong> <?php echo $this->escape($client->address) ?></p>
                <p><strong><i class="fa fa-fw fa-envelope"></i> E-Mail:</strong> <?php echo $this->escape($client->email) ?></p>
                <p><strong><i class="fa fa-fw fa-globe"></i> Web site:</strong> <?php echo $this->escape($client->webSite) ?></p>
            </div>
            <div class="col-xs-12 col-lg-6">
                <div class="embed-responsive embed-responsive-16by9" id="client-map">
                    <iframe class="embed-responsive-item" src="https://maps.google.com/maps?q=<?php echo str_replace(' ', '+', $client->address)?>&output=embed"></iframe>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <h3 class="bg-main margin-bottom padded-5px"><i class="fa fa-fw fa-users"></i> Contacts:</h3>
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
<?php foreach ($contacts as $contact): ?>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#contact<?php echo $contact->id?>" aria-expanded="false" aria-controls="collapseOne">
                                    <i class="fa fa-fw fa-user"></i> <?php echo $contact->firstName . ' ' . $contact->lastName ?>
                                </a>
                            </h4>
                        </div>
                        <div id="contact<?php echo $contact->id?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                Email: <?php echo $contact->email ?> | Phone: <?php echo $contact->phone ?>
                            </div>
                        </div>
                    </div>
<?php endforeach; ?>
                </div>
<?php if (count($contacts) === 0): ?>
                <h4>No contacts</h4>
<?php endif;?>
            </div>
        </div>
    </div>
    <div class="panel">
        <div class="panel-footer text-right">
            <a href="<?php $this->publicPath() ?>Clients" class="btn btn-link">Back</a>
            <a href="<?php $this->publicPath() ?>Clients/Update/<?php echo $client->id ?>" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i> Update client</a>
        </div>
    </div>
</div>
<script src="<?php $this->publicPath() ?>js/clients/client-show.js?<?php echo date('YmdHis') ?>"></script>
<?php
        $this->getSharedView('ConfirmModal')->render();
        $this->getSharedView('MessageModal')->render();
        $this->getSharedView('Footer')->render();
    }

}