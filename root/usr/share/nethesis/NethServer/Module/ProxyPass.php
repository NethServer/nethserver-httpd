<?php
namespace NethServer\Module;

/*
 * Copyright (C) 2018 Nethesis Srl
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
 *
 * Tabs and tables for Proxypass
 *
 * @author stephane de Labrusse <stephdl@de-labrusse.fr>
 */

class ProxyPass extends \Nethgui\Controller\TabsController
{
    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Gateway');
    }

    public function initialize()
    {
        parent::initialize();
        $this->addChild(new \NethServer\Module\ProxyPass\ProxyPassPath());
        $this->addChild(new \NethServer\Module\ProxyPass\ProxyPassVhost());
    }

}
