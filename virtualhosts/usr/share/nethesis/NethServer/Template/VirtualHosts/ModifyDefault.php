<?php

/* @var $view \Nethgui\Renderer\Xhtml */

$view->requireFlag($view::INSET_FORM);

$template = 'VirtualHostDefault_Update_Header';

echo $view->header('name')->setAttribute('template', $T($template));

echo $view->panel()
        ->insert($view->fieldsetSwitch('FtpStatus', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
                ->setAttribute('uncheckedValue', 'disabled')
                ->insert($view->textInput('FtpUserName', $view::STATE_DISABLED | $view::STATE_READONLY))
                ->insert($view->textInput('FtpPassword'))
                )
;


echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
