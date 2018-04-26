<?php

namespace NethServer\Module\VirtualHosts;

/*
 * Copyright (C) 2016Nethesis S.r.l.
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
 * Modify Virtual Hosts
 *
 */
class Modify extends \Nethgui\Controller\Table\Modify
{

    public function initialize()
    {

        $schema = array(
            array('name', Validate::USERNAME, Table::KEY),
            array('Description', Validate::ANYTHING, Table::FIELD),
            array('ServerNames', Validate::NOTEMPTY, Table::FIELD),
            array('PasswordStatus', Validate::SERVICESTATUS, Table::FIELD),
            array('PasswordValue', Validate::NOTEMPTY, Table::FIELD),
            array('Access', $this->createValidator()->memberOf('public', 'private'), Table::FIELD),
            array('ForceSslStatus', Validate::SERVICESTATUS, Table::FIELD),
            array('SslCertificate', Validate::ANYTHING, Table::FIELD),
            array('FtpStatus', Validate::SERVICESTATUS, Table::FIELD),
            array('FtpPassword', Validate::NOTEMPTY, Table::FIELD),
            array('Indexes', Validate::SERVICESTATUS, Table::FIELD),
        );

        $this->setSchema($schema);

        $this->declareParameter('CreateHostRecords', Validate::ANYTHING);

        $this
                ->setDefaultValue('PasswordValue', '')
                ->setDefaultValue('PasswordStatus', 'disabled')
                ->setDefaultValue('Access', 'private')
                ->setDefaultValue('ForceSslStatus', 'disabled')
                ->setDefaultValue('FtpStatus', 'enabled')
                ->setDefaultValue('FtpPassword', '')
                ->setDefaultValue('CreateHostRecords', '1')
                ->setDefaultValue('Indexes', 'disabled')
        ;

        if ($this->getIdentifier() === 'delete') {
            $this->setViewTemplate('Nethgui\Template\Table\Delete');
        }
        parent::initialize();
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        parent::validate($report);
        if ( ! $this->getRequest()->isMutation()) {
            return;
        }

        // Key validator 
        if ($this->getIdentifier() === 'create' && $this->getPlatform()->getDatabase('vhosts')->getKey($this->parameters['name']) ) {
           $report->addValidationErrorMessage($this, 'name', 'Name_Already_Exists', array($this->parameters['name']));
       }


        $serverList = explode(',', $this->sanitizeServerNames($this->parameters['ServerNames']));
        $validHostname = $this->createValidator()->hostname();
        foreach ($serverList as $serverName) {
            if ( ! $validHostname->evaluate($serverName)) {
                $report->addValidationError($this, 'ServerNames', $validHostname);
            }
            if($this->findServerName($serverName, array($this->parameters['name']))) {
                $report->addValidationErrorMessage($this, 'ServerNames', 'valid_servername_already_used', array($serverName));
            }
        }
    }

    private function findServerName($serverName, $ignoreList = array())
    {
        $results = array();
        foreach($this->getParent()->getAdapter() as $vhost => $record) {
            if(in_array($vhost, $ignoreList)) {
                continue;
            }
            $serverNames = explode(',', $record['ServerNames']);
            if(in_array($serverName, $serverNames)) {
                $results[]=$serverName;
            }
        }
        return array_unique($results);
    }

    public function readServerNames($v)
    {
        return str_replace(',', ', ', $v);
    }

    public function writeServerNames($s)
    {
        $serverNames = explode(',', $this->sanitizeServerNames($s));
        return array(implode(',', array_unique($serverNames)));
    }

    protected function sanitizeServerNames($s)
    {
        return preg_replace('/[;,\s]+/', ',', $s);
    }

    protected function processCreate($key)
    {
        $this->getAdapter()->offsetSet('status', 'enabled');

        if ($this->parameters['CreateHostRecords'] !== '1') {
            return;
        }

        $serverList = explode(',', $this->sanitizeServerNames($this->parameters['ServerNames']));
        $hostsDb = $this->getPlatform()->getDatabase('hosts');
        foreach ($serverList as $serverName) {
            if( ! $hostsDb->getKey($serverName)) {
                $hostsDb->setKey($serverName, 'self', array('Description' => $this->parameters['Description'] ?: 'Virtual host'));
            }
        }

        $this->getPlatform()->signalEvent('host-modify', array(\Nethgui\array_head($serverList)));
    }

    protected function onParametersSaved($changedParameters)
    {
        parent::onParametersSaved($changedParameters);

        $action = $this->getIdentifier();
        if($action === 'update') {
            $action = 'modify';
        }

        $this->getPlatform()->signalEvent('vhost-' . $action, array($this->parameters['name']));
    }

    protected function getSslCertificateDatasource()
    {
        static $ds;
        if (isset($ds)) {
            return $ds;
        }

        $output = array();
        \exec('/usr/libexec/nethserver/cert-list', $output);
        $data = json_decode($output[0], TRUE);
        if ( ! is_array($data)) {
            $data = array();
        }

        foreach ($data as $key => $value) {
            $ds[] = array($key, $value['file']);
        }

        return $ds;
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        if($this->getIdentifier() !== 'delete') {
            // avoid "Cannot redeclare class Nethgui\Controller\Table\PluggableAction" error:
            $view->setTemplate('NethServer\Template\VirtualHosts\Modify');
        }
        $view['SslCertificateDatasource'] = array_merge(array(array('', $view->translate('Default_Ssl_certificate_label'))), $this->getSslCertificateDatasource());
        $view['FtpUserName'] = $view['name'];
        $view['HttpUserName'] = $view['name'];
    }

}
