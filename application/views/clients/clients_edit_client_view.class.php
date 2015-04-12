<?php


class Clients_Edit_Client_View extends Trident_Abstract_View
{
    public function render()
    {
        $this->include_shared_view('header');
        $this->include_shared_view('navbar');
        /** @var Client_Entity $client */
        $client = $this->get('client');
    ?>
    <div class="well well-sm top-fixed-header">
        <h2 class="no-margin"><i class="fa fa-fw fa-users"></i> לקוחות</h2>
    </div>
    <ol class="breadcrumb">
        <li><a href="<?php $this->public_path()?>">ראשי</a></li>
        <li><a href="<?php $this->public_path()?>/clients">לקוחות</a></li>
        <li><a href="<?php $this->public_path()?>/clients/show/<?php echo $this->escape($client->id)?>"><?php echo $this->escape($client->name)?></a></li>
        <li class="active">עריכה</li>
    </ol>
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong class="font-125"><i class="fa fa-fw fa-user"></i> כרטיס לקוח:  <?php echo $this->escape($client->name)?></strong>
        </div>
        <div class="panel-body">
            <form id="edit-client-form" method="post" data-toggle="validator">
                <div class="form-group col-xs-12 col-lg-3">
                    <label>שם לקוח</label>
                    <input type="text" class="form-control" name="client_name" required data-maxlength="128" value="<?php echo $this->escape($client->name)?>" placeholder="שם לקוח">
                    <div class="help-block with-errors">עד 128 תווים</div>
                </div>
                <div class="form-group col-xs-12 col-lg-3">
                    <label>כתובת לקוח</label>
                    <input type="text" class="form-control" name="client_address" required data-maxlength="128" value="<?php echo $this->escape($client->address)?>" placeholder="כתובת לקוח">
                    <div class="help-block with-errors">עד 128 תווים</div>
                </div>
                <div class="form-group col-xs-12 col-lg-3">
                    <label>טלפון לקוח</label>
                    <input type="tel" class="form-control" name="client_phone" required pattern="^0[0-9]{1,2}\-[0-9]{7}$" data-maxlength="10" value="<?php echo $this->escape($client->phone)?>" placeholder="טלפון לקוח">
                    <div class="help-block with-errors">פורמט #######-##?</div>
                </div>
                <div class="form-group col-xs-12 col-lg-3">
                    <label>פקס לקוח</label>
                    <input type="tel" class="form-control" name="client_fax" required pattern="^0[0-9]{1,2}\-[0-9]{7}$" data-maxlength="10" value="<?php echo $this->escape($client->fax)?>" placeholder="פקס לקוח">
                    <div class="help-block with-errors">פורמט #######-##?</div>
                </div>
                <div class="form-group col-xs-12 col-lg-3">
                    <label>דואר אלקטרוני לקוח</label>
                    <input type="email" class="form-control" name="client_email" required data-maxlength="255" value="<?php echo $this->escape($client->email)?>" placeholder="דואר אלקטרוני לקוח">
                    <div class="help-block with-errors">יש להזין כתובת דואר אלקטרוני חוקית</div>
                </div>
                <div class="form-group col-xs-12 col-lg-3">
                    <label>אתר אינטרנט לקוח</label>
                    <input type="url" class="form-control" name="client_website" required data-maxlength="255" value="<?php echo $this->escape($client->website)?>" placeholder="אתר אינטרנט לקוח">
                    <div class="help-block with-errors">יש להזין כתובת אתר אינטרנט חוקית</div>
                </div>
            </form>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-xs-12 text-left">
                    <a href="<?php $this->public_path()?>/clients" class="btn btn-link">בטל</a>
                    <button type="button" onclick="$('#edit-client-form').submit()" class="btn btn-success"><i class="fa fa-fw fa-check"></i> עדכן לקוח</button>
                </div>
            </div>
        </div>
    </div>
    <?php
        $this->include_shared_view('footer');
    }
}