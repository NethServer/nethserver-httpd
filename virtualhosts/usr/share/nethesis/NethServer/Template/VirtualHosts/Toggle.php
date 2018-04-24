<?php
/* @var $view \Nethgui\Renderer\Xhtml */

$view->requireFlag($view::INSET_DIALOG);

if($view->getModule()->getIdentifier() === 'enable') {
    $headerText = $T('ToggleEnable_header');
    $panelText = $T('ToggleEnable_text');
} else {
    $headerText = $T('ToggleDisable_header');
    $panelText = $T('ToggleDisable_text');
}

echo $view->panel()
    ->insert($view->header('name')->setAttribute('template', $headerText))
    ->insert($view->textLabel('name')->setAttribute('template', $panelText))
;

echo $view->buttonList()
    ->insert($view->button('Yes', $view::BUTTON_SUBMIT))
    ->insert($view->button('No', $view::BUTTON_CANCEL)->setAttribute('value', $view['Cancel']))
;




