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
 * Modify default virtual host
 *
 */
class ModifyDefault extends \Nethgui\Controller\Table\RowAbstractAction
{

    public function initialize()
    {

        $schema = array(
            array('name', Validate::USERNAME, Table::KEY),
            array('FtpStatus', Validate::SERVICESTATUS, Table::FIELD),
            array('FtpPassword', Validate::NOTEMPTY, Table::FIELD),
        );

        $this->setSchema($schema);

        parent::initialize();
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        $keyValue = \Nethgui\array_end($request->getPath());
        $this->getAdapter()->setKeyValue($keyValue);
        parent::bind($request);
    }

    protected function onParametersSaved($changedParameters)
    {
        parent::onParametersSaved($changedParameters);

        $this->getPlatform()->signalEvent('vhost-modify', array($this->parameters['name']));
    }


    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $view['FtpUserName'] = $view['name'];
        $view['HttpUserName'] = $view['name'];
    }

}
