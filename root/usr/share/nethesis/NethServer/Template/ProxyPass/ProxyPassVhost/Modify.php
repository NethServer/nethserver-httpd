<?php

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = 'Update proxy pass `${0}`';
} else {
    $headerText = 'Create a new proxy pass vhost';
}


echo  $view->header('Vhost')->setAttribute('template', $T($headerText));

echo $view->textInput('Vhost', ($view->getModule()->getIdentifier() == 'update' ? $view::STATE_READONLY | $view::STATE_DISABLED : 0));
echo $view->textInput('Target');
echo $view->checkBox('HTTP', 'no')->setAttribute('uncheckedValue', 'yes');
echo $view->selector('SslCertificate', $view::SELECTOR_DROPDOWN);

echo $view->textInput('ValidFrom');
echo $view->textInput('Description');

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
