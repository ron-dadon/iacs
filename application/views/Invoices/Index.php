<?php

namespace Application\Views\Invoices;

use \Trident\MVC\AbstractView;

class Index extends AbstractView
{

    public function render()
    {
        $this->getSharedView('Header')->render();
        $this->getSharedView('TopBar')->render();
        $this->getSharedView('SideBar')->render(); ?>
<div class="container-fluid">
    <h1>Invoices</h1>
</div>
<?php
        $this->getSharedView('Footer')->render();
    }

}