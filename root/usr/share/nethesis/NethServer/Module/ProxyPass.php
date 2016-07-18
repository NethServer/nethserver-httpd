<?php
namespace NethServer\Module;

/*
 * Copyright (C) 2016 Nethesis S.r.l.
 * 
 * This script is part of NethServer.
 * 
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Manage proxy pas
 */
class ProxyPass extends \Nethgui\Controller\TableController
{

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Gateway');
    }

    public function initialize()
    {
        $columns = array(
            'Key',
            'Target',
            'Protocol',
            'Description',
            'Actions'
        );

        $this
            ->setTableAdapter($this->getPlatform()->getTableAdapter('proxypass', 'ProxyPass'))
            ->setColumns($columns)
            ->addTableAction(new \NethServer\Module\ProxyPass\Modify('create'))
            ->addTableAction(new \Nethgui\Controller\Table\Help('Help'))
            ->addRowAction(new \NethServer\Module\ProxyPass\Modify('update'))
            ->addRowAction(new \NethServer\Module\ProxyPass\Modify('delete'))
        ;

        parent::initialize();
    }

    public function prepareViewForColumnProtocol(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
         $protocols = array();
         if ($values['HTTP'] == 'yes') {
            $protocols[] = 'HTTP';
         }
         if ($values['HTTPS'] == 'yes') {
            $protocols[] = 'HTTPS';
         }

         return implode(' / ', $protocols);
    }

}

