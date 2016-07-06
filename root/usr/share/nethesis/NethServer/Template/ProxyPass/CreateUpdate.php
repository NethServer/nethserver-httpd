<?php

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = 'Update proxy pass `${0}`';
} else {
    $headerText = 'Create a new proxy pass';
}

echo $view->panel()
    ->insert($view->header()->setAttribute('template', $T($headerText)))
    ->insert($view->textInput('Name', ($view->getModule()->getIdentifier() == 'update' ? $view::STATE_READONLY : 0)))
    ->insert($view->textInput('Target'));

echo $view->fieldset()->setAttribute('template', $T('Protocol_label'))
    ->insert($view->checkBox('HTTP', 'on')->setAttribute('uncheckedValue', 'off'))
    ->insert($view->checkBox('HTTPS', 'on')->setAttribute('uncheckedValue', 'off'));

echo $view->panel()
    ->insert($view->textInput('ValidFrom'))
    ->insert($view->textInput('Description'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL);
