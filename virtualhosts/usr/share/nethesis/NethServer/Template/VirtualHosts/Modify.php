<?php

/* @var $view \Nethgui\Renderer\Xhtml */

$view->requireFlag($view::INSET_FORM);

if ($view->getModule()->getIdentifier() == 'update') {
    $keyFlags = $view::STATE_DISABLED | $view::STATE_READONLY;
    $template = 'VirtualHost_Update_Header';
} else {
    $keyFlags = 0;
    $template = 'VirtualHost_Create_Header';
}

echo $view->header('name')->setAttribute('template', $T($template));

$basicInfo = $view->panel()->setAttribute('title', $T('Basic_tab_title'))
        ->insert($view->textInput('name', $keyFlags))
        ->insert($view->textInput('Description'))
        ->insert($view->textInput('ServerNames'))
;

$basicInfo->insert($view->checkBox('Access', 'private')->setAttribute('uncheckedValue', 'public'))
        ->insert($view->fieldsetSwitch('PasswordStatus', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
                ->setAttribute('uncheckedValue', 'disabled')
                    ->insert($view->textInput('HttpUserName', $view::STATE_DISABLED | $view::STATE_READONLY))
                    ->insert($view->textInput('PasswordValue', $view::TEXTINPUT_PASSWORD))
                )
        ->insert($view->checkbox('ForceSslStatus', 'enabled')->setAttribute('uncheckedValue', 'disabled'))

        ->insert($view->checkBox('Indexes', 'enabled')->setAttribute('uncheckedValue', 'disabled')) 

        ->insert($view->selector('SslCertificate', $view::SELECTOR_DROPDOWN))
        ->insert($view->fieldsetSwitch('FtpStatus', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
                ->setAttribute('uncheckedValue', 'disabled')
                ->insert($view->textInput('FtpUserName', $view::STATE_DISABLED | $view::STATE_READONLY))
                ->insert($view->textInput('FtpPassword', $view::TEXTINPUT_PASSWORD))
                )
;

if ($keyFlags === 0) {
    $basicInfo->insert($view->fieldset()->setAttribute('template', $T('ExtraFields_label'))->insert($view->checkBox('CreateHostRecords', '1')));
}

echo $view->tabs()
        ->insert($basicInfo)
        ->insertPlugins('ModifyPlugin')
;


echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);

$nameTarget = $view->getClientEventTarget('name');
$ftpUserTarget = $view->getClientEventTarget('FtpUserName');
$httpUserTarget = $view->getClientEventTarget('HttpUserName');

$jsCode = <<<"EOJSCODE"
jQuery(document).ready(function($) {    
    $('.TextInput.{$nameTarget}').on('keyup', function(e) {
        $('.{$ftpUserTarget}, .{$httpUserTarget}').val($(this).val());
    });
});
EOJSCODE;

$view->includeJavascript($jsCode);

