<?php

echo $view->panel()    
    // ->setAttribute('icon-before', 'ui-icon-link')
    ->insert($view->selector('VirtualHost', $view::SELECTOR_DROPDOWN))
    ->insert($view->radioButton('Access', 'private'))
    ->insert($view->radioButton('Access', 'public'))    
    ->insert(
        $view->fieldsetSwitch('PasswordState', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
            ->setAttribute('uncheckedValue', 'disabled')
            ->insert($view->textInput('PasswordValue', $view::LABEL_NONE ))
    )    
    ->insert($view->checkBox('CgiBin', 'enabled')->setAttribute('uncheckedValue', 'disabled'))

;
