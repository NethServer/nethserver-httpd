<?php

echo $view->fieldset()
    ->setAttribute('template', $T('Httpd options'))
    // ->setAttribute('icon-before', 'ui-icon-link')
    ->insert($view->selector('VirtualHost', $view::SELECTOR_DROPDOWN))
    ->insert($view->radioButton('Access', 'public'))
    ->insert($view->radioButton('Access', 'private'))
    ->insert(
        $view->fieldsetSwitch('PasswordState', 'enabled')
        ->insert($view->textInput('PasswordValue', $view::LABEL_NONE ))
    )
    ->insert($view->fieldsetSwitch('PasswordState', 'disabled'))    
    ->insert($view->checkBox('CgiBin', 'enabled')->setAttribute('uncheckedValue', 'disabled'))

;
