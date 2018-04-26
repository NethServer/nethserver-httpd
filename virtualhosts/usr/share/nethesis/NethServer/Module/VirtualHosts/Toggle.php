<?php

namespace NethServer\Module\VirtualHosts;

/*
 * Copyright (C) 2016 Nethesis Srl
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use Nethgui\System\PlatformInterface as Validate;
use Nethgui\Controller\Table\Modify as Table;

/**
 * Set status enabled/disabled for the given virtual host
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 */
class Toggle extends \Nethgui\Controller\Table\AbstractAction
{

    public function __construct($identifier = NULL)
    {
        if ($identifier !== 'enable' && $identifier !== 'disable') {
            throw new \InvalidArgumentException(sprintf('%s: module identifier must be one of "enable" or "disable".', get_class($this)), 1466691422);
        }
        parent::__construct($identifier);
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        $this->declareParameter('name', Validate::USERNAME);

        parent::bind($request);
        $name = \Nethgui\array_end($request->getPath());

        if ( ! $name) {
            throw new \Nethgui\Exception\HttpException('Not found', 404, 1466691466);
        }

        $this->parameters['name'] = $name;
    }

    public function process()
    {
        if ( ! $this->getRequest()->isMutation()) {
            return;
        }

        $this->getPlatform()->getDatabase('vhosts')->setProp($this->parameters['name'], array('status' => $this->getIdentifier() === 'enable' ? 'enabled' : 'disabled'));

        $this->getPlatform()->signalEvent('vhost-modify', array($this->parameters['name']));
    }

}
