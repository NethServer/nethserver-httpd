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

/**
 * Manage proxy pas
 */
class ProxyPass extends \Nethgui\Controller\TableController
{

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Configuration');
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

        $onoffValidator = $this->getPlatform()->createValidator()->memberOf(array('on', 'off'));
        $parameterSchema = array(
            array('Name', Validate::USERNAME, \Nethgui\Controller\Table\Modify::KEY),
            array('Target', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('HTTP', $onoffValidator, \Nethgui\Controller\Table\Modify::FIELD),
            array('HTTPS', $onoffValidator, \Nethgui\Controller\Table\Modify::FIELD),
            array('ValidFrom', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('Description', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
        );

        $this
            ->setTableAdapter($this->getPlatform()->getTableAdapter('proxypass', 'ProxyPass'))
            ->setColumns($columns)
            ->addTableAction(new \Nethgui\Controller\Table\Modify('create', $parameterSchema, 'NethServer\Template\ProxyPass\CreateUpdate'))            
            ->addTableAction(new \Nethgui\Controller\Table\Help('Help'))
            ->addRowAction(new \Nethgui\Controller\Table\Modify('update', $parameterSchema, 'NethServer\Template\ProxyPass\CreateUpdate'))
            ->addRowAction(new \Nethgui\Controller\Table\Modify('delete', $parameterSchema, 'Nethgui\Template\Table\Delete'))
        ;

        parent::initialize();
    }

    /*public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        parent::validate($report);

        if( ! $this->getRequest()->isMutation()) {
            return;
        }

        if ( $this->getPlatform()->getDatabase('proxypass')->getKey($this->parameters['Name']) ) {
            $report->addValidationErrorMessage($this, 'Name', 'Name_Alread_Exists');
        }

        if ($this->parameters['HTTP'] == 'off' && $this->parameters['HTTPS'] == 'off') {
            $report->addValidationErrorMessage($this, 'HTTP', 'Invalid_Protocol');
        }

        if ( ! filter_var($this->parameters['Target'], FILTER_VALIDATE_URL) || strpos($this->parameters['Target'], 'http') !== 0 ) {
            $report->addValidationErrorMessage($this, 'Target', 'Invalid_Target');
        }
    }*/

    public function prepareViewForColumnProtocol(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
         $protocols = array();
         if ($values['HTTP'] == 'on') {
            $protocols[] = 'HTTP';
         }
         if ($values['HTTPS'] == 'on') {
            $protocols[] = 'HTTPS';
         }

         return implode(' / ', $protocols);
    }

    public function onParametersSaved(\Nethgui\Module\ModuleInterface $currentAction, $changes, $parameters)
    {
        $this->getPlatform()->signalEvent('nethserver-httpd-update@post-process');
    }

}

