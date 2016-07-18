<?php
namespace NethServer\Module\ProxyPass;

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

/**
 * CRUD actions on proxypass records
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 */
class Modify extends \Nethgui\Controller\Table\Modify
{

    public function initialize()
    {
        parent::initialize();
        $parameterSchema = array(
            array('Name', Validate::USERNAME, \Nethgui\Controller\Table\Modify::KEY),
            array('Target', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('HTTP', Validate::YES_NO, \Nethgui\Controller\Table\Modify::FIELD),
            array('HTTPS', FALSE, \Nethgui\Controller\Table\Modify::FIELD),
            array('ValidFrom', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('Description', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
        );
        $this->setSchema($parameterSchema);
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        parent::validate($report);

        if( ! $this->getRequest()->isMutation()) {
            return;
        }

        if ($this->getIdentifier() === 'create' && $this->getPlatform()->getDatabase('proxypass')->getKey($this->parameters['Name']) ) {
            $report->addValidationErrorMessage($this, 'Name', 'Name_Already_Exists');
        }

        if ( ! filter_var($this->parameters['Target'], FILTER_VALIDATE_URL) || strpos($this->parameters['Target'], 'http') !== 0 ) {
            $report->addValidationErrorMessage($this, 'Target', 'Invalid_Target');
        }

        $cidrList = preg_split('/\s*,\s*/', $this->parameters['ValidFrom']);
        $cidrValidator = $this->createValidator(Validate::CIDR_BLOCK);
        foreach($cidrList as $cidr) {
            if($cidr !== '' && ! $cidrValidator->evaluate($cidr)) {
                $report->addValidationErrorMessage($this, 'ValidFrom', 'ValidFrom_validator');
                break;
            }
        }
    }

    public function process()
    {
        if($this->getRequest()->isMutation() && $this->getIdentifier() === 'create') {
            // always enable HTTPS
            $this->parameters['HTTPS'] = 'yes';
        }

        if($this->getRequest()->isMutation()) {
            $this->parameters['ValidFrom'] = implode(', ', preg_split('/\s*,\s*/', $this->parameters['ValidFrom']));
        }

        parent::process();

    }

    protected function onParametersSaved($changedParameters)
    {
        $this->getPlatform()->signalEvent('nethserver-httpd-save');
    }


    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        if($this->getIdentifier() === 'delete') {
            $view->setTemplate('Nethgui\Template\Table\Delete');
        }
    }
}
