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
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Service', 20);
    }

    public function initialize()
    {
        $schema = array(
            array('CgiBin', Validate::SERVICESTATUS, Table::FIELD, 'HttpCgiBin'),
            array('PasswordState', Validate::SERVICESTATUS, Table::FIELD, 'HttpPasswordState'),
            array('PasswordValue', $this->getPlatform()->createValidator()->platform('password-strength', 'Ibays'), Table::FIELD, 'HttpPasswordValue'),
            array('Access', $this->getPlatform()->createValidator()->memberOf('public', 'private'), Table::FIELD, 'HttpAccess'),
            array('VirtualHost', Validate::HOSTNAME_FQDN, Table::FIELD, 'HttpVirtualHost')
        );

        $this->parameters['VirtualHostDatasource'] = array(array('default', 'Default'));

        $this
            ->setDefaultValue('PasswordValue', '')
            ->setDefaultValue('PasswordState', 'disabled')
            ->setDefaultValue('Access', 'private')
            ->setDefaultValue('CgiBin', 'disabled')
        ;

        $this->setSchemaAddition($schema);
        parent::initialize();
    }

}
