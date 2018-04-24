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

use Nethgui\System\PlatformInterface as Validate;
use Nethgui\Controller\Table\Modify as Table;

/**
 * Manage virtual hosts
 */
class VirtualHosts extends \Nethgui\Controller\TableController
{

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\CompositeModuleAttributesProvider::extendModuleAttributes($base, 'Management')->extendFromComposite($this);
    }

    public function initialize()
    {
        $columns = array(
            'Key',
            'Description',
            'ServerNames',
            'Actions'
        );

        $this
                ->setTableAdapter($this->getPlatform()->getTableAdapter('vhosts', 'vhost'))
                ->setColumns($columns)
                ->addTableActionPluggable(new \NethServer\Module\VirtualHosts\Modify('create'), 'ModifyPlugin')
                ->addTableAction(new \Nethgui\Controller\Table\Help('Help'))
                ->addRowActionPluggable(new \NethServer\Module\VirtualHosts\Modify('update'), 'ModifyPlugin')
                ->addRowAction(new \NethServer\Module\VirtualHosts\Toggle('enable'))
                ->addRowAction(new \NethServer\Module\VirtualHosts\Toggle('disable'))
                ->addRowAction(new \NethServer\Module\VirtualHosts\Modify('delete'))
                ->addRowAction(new \NethServer\Module\VirtualHosts\ModifyDefault())
        ;

        parent::initialize();
    }

    public function prepareViewForColumnActions(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        $cellView = $action->prepareViewForColumnActions($view, $key, $values, $rowMetadata);
        if ($values['status'] === 'disabled') {
            unset($cellView['disable']);
        } elseif ($values['status'] === 'enabled') {
            unset($cellView['enable']);
        }
        
        # Protect 'default' virtual host
        if ($key == 'default') {
            unset($cellView['delete']);
            unset($cellView['enable']);
            unset($cellView['disable']);
            unset($cellView['update']);
        } else {
            unset($cellView['ModifyDefault']);
        }

        return $cellView;
    }

    public function prepareViewForColumnKey(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        if ($values['status'] === 'disabled') {
            $rowMetadata['rowCssClass'] = trim($rowMetadata['rowCssClass'] . ' user-locked');
        }
        return strval($key);
    }

    public function prepareViewForColumnServerNames(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        if ($values['ServerNames']) {
            return str_replace(',', ', ', $values['ServerNames']);
        } else {
            return $view->translate('any_label');
        }
    }

}
