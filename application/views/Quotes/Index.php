<?php
/***********************************************************************************************************************
 * IACS Management System
 * ORT BRAUDE COLLEGE OF ENGINEERING
 * Information System Engineering - Final Project
 * Students: Ron Dadon, Guy Franco
 * Project adviser: PhD Miri Weiss-Cohen
 **********************************************************************************************************************/

namespace Application\Views\Quotes;

use \Trident\MVC\AbstractView;
use Application\Entities\Quote;

/**
 * Class Index
 *
 * Show system quotes.
 *
 * @package Application\Views\Quotes
 */
class Index extends AbstractView
{

    /**
     * Render system quotes.
     *
     * @throws \Trident\Exceptions\ViewNotFoundException
     */
    public function render()
    {
        /** @var Quote[] $quotes */
        $quotes = $this->data['quotes'];
        $this->getSharedView('Header')->render();
        $this->getSharedView('TopBar')->render();
        $this->getSharedView('SideBar')->render(); ?>
<div class="container-fluid">
    <div class="page-head bg-main">
        <h1><i class="fa fa-fw fa-database"></i> Quotes</h1>
    </div>
<?php if (isset($this->data['error'])): ?>
    <div class="alert alert-danger alert-dismissable">
        <h4><i class="fa fa-fw fa-times-circle"></i><?php echo $this->data['error'] ?></h4>
    </div>
<?php endif; ?>
<?php if (isset($this->data['success'])): ?>
    <div class="alert alert-success alert-dismissable">
        <h4><i class="fa fa-fw fa-check-circle"></i><?php echo $this->data['success'] ?></h4>
    </div>
<?php endif; ?>
    <div id="alerts-container"></div>
    <div class="panel">
        <div class="table-responsive">
            <table class="table table-bordered" id="quotes-table">
                <thead>
                <tr>
                    <th data-column-id="id" data-identifier="true"  data-order="desc" data-converter="quote" data-formatter="quoteLink">Number</th>
                    <th data-column-id="quoteClient" data-formatter="client">Client</th>
                    <th data-column-id="quoteDate">Date</th>
                    <th data-column-id="quoteDateExpire">Expires</th>
                    <th data-column-id="quoteStatus" data-formatter="statusFilter">Status</th>
                    <th data-column-id="statusSet" data-sortable="false" data-formatter="quoteStatus">Quote actions</th>
                    <th data-column-id="actions" data-sortable="false" data-formatter="quoteActions">Actions</th>
                </tr>
                </thead>
                <tbody>
<?php foreach ($quotes as $quote): ?>
                    <tr data-user-id="<?php echo $quote->id ?>">
                        <td><?php echo $quote->id ?></td>
                        <td><?php echo $this->escape($quote->client->name) ?></td>
                        <td><?php echo $this->formatSqlDateTime(substr($quote->date,0,10), "Y-m-d", "d/m/Y") ?></td>
                        <td><?php echo $this->formatSqlDateTime(substr($quote->expire,0,10), "Y-m-d", "d/m/Y") ?></td>
                        <td><?php echo $this->escape($quote->status->name) ?></td>
                        <td>Set</td>
                        <td>Actions</td>
                    </tr>
<?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="panel-footer text-right">
                <div class="hidden-xs">
                    <button type="button" class="btn btn-default pull-left" onclick="$('#quotes-table').bootgrid('search','')"><i class="fa fa-fw fa-eraser"></i> Clear filter</button>
                    <a href="<?php $this->publicPath() ?>Quotes/New" class="btn btn-primary"><i class="fa fa-fw fa-plus"></i> New quote</a>
                </div>
                <div class="visible-xs">
                    <a href="<?php $this->publicPath() ?>Quotes/New" class="btn btn-primary btn-block"><i class="fa fa-fw fa-plus"></i> New quote</a>
                    <button type="button" class="btn btn-default btn-block" onclick="$('#quotes-table').bootgrid('search','')"><i class="fa fa-fw fa-eraser"></i> Clear filter</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php $this->publicPath() ?>js/quotes/index.js?<?php echo date('YmdHis'); ?>"></script>
<?php
        $this->getSharedView('ConfirmModal')->render();
        $this->getSharedView('Footer')->render();
    }

}