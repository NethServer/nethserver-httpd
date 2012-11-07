<?php

/* @var $view \Nethgui\Renderer\Xhtml */
echo $view->fieldsetSwitch('Status', 'enabled', $view::FIELDSETSWITCH_CHECKBOX)->setAttribute('uncheckedValue', 'disabled')
    // ->setAttribute('icon-before', 'ui-icon-link')
    ->insert($view->selector('VirtualHost', $view::SELECTOR_DROPDOWN))
    ->insert(
        $view->fieldset()->setAttribute('template', $T('Alias_label'))
        ->insert($view->radioButton('AliasType', 'default'))
        ->insert($view->radioButton('AliasType', 'root'))
        ->insert($view->fieldsetSwitch('AliasType', 'custom', $view::FIELDSETSWITCH_EXPANDABLE)
            ->insert($view->textInput('AliasCustom', $view::LABEL_NONE))
        )
    )
    ->insert($view->checkBox('Access', 'private')->setAttribute('uncheckedValue', 'public'))
    ->insert(
        $view->fieldsetSwitch('PasswordStatus', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
        ->setAttribute('uncheckedValue', 'disabled')
        ->insert($view->textInput('PasswordValue', $view::LABEL_NONE))
    )
    ->insert($view->checkBox('CgiBin', 'enabled')->setAttribute('uncheckedValue', 'disabled'))

;
