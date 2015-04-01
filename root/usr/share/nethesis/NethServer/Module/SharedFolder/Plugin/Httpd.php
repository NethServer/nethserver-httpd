<?php
namespace NethServer\Module\SharedFolder\Plugin;

/*
 * Copyright (C) 2012 Nethesis S.r.l.
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
 * Httpd SharedFolder plugin
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Httpd extends \Nethgui\Controller\Table\RowPluginAction
{

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Httpd', 20);
    }

    public function initialize()
    {
        $virtualHostValidator = $this->createValidator()->orValidator($this->createValidator(Validate::HOSTNAME_FQDN), $this->createValidator()->equalTo('__ANY__'));
        
        $schema = array(
            array('Status', Validate::SERVICESTATUS, Table::FIELD, 'HttpStatus'),
            array('VirtualHost', $virtualHostValidator, Table::FIELD, 'HttpVirtualHost'),
            array('PasswordStatus', Validate::SERVICESTATUS, Table::FIELD, 'HttpPasswordStatus'),
            array('PasswordValue', Validate::NOTEMPTY, Table::FIELD, 'HttpPasswordValue'),
            array('Access', $this->createValidator()->memberOf('public', 'private'), Table::FIELD, 'HttpAccess'),
            array('CgiBin', Validate::SERVICESTATUS, Table::FIELD, 'HttpCgiBinStatus'),
            array('AliasType', $this->createValidator()->memberOf('default', 'root', 'custom'), Table::FIELD, 'HttpAliasType'),
            array('AliasCustom', '/^([a-z]|[0-9]){1,12}$/', Table::FIELD, 'HttpAliasCustom'),
            array('ForceSsl', Validate::SERVICESTATUS, Table::FIELD, 'HttpForceSslStatus'),
            array('AllowOverride', Validate::SERVICESTATUS, Table::FIELD, 'HttpAllowOverrideStatus'),
        );

        $this
            ->setDefaultValue('Status', 'enabled')
            ->setDefaultValue('PasswordValue', '')
            ->setDefaultValue('PasswordStatus', 'disabled')
            ->setDefaultValue('Access', 'private')
            ->setDefaultValue('CgiBin', 'disabled')
            ->setDefaultValue('AliasType', 'default')
            ->setDefaultValue('ForceSsl', 'disabled')
            ->setDefaultValue('AllowOverride', 'disabled')
        ;

        $this->setSchemaAddition($schema);
        parent::initialize();
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);

        $view['VirtualHostDatasource'] = array_merge(
            array(array('__ANY__', $view->translate('ANY_VHOST'))),
            $this->getVirtualHostDatasource()
        );
    }

    public function getVirtualHostDatasource()
    {
        $ds = array();

        foreach ($this->getPlatform()->getDatabase('hosts')->getAll('self') as $hostName => $record) {
            if (isset($record['Description'])
                && $record['Description']) {
                $description = sprintf("%s (%s)", $hostName, trim($record['Description']));
            } else {
                $description = $hostName;
            }
            $ds[] = array($hostName, $description);
        }

        return $ds;
    }

}
