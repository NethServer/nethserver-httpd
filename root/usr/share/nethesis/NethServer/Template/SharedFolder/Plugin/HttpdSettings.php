<?php

/* @var $view \Nethgui\Renderer\Xhtml */
echo $view->fieldsetSwitch('Status', 'enabled', $view::FIELDSETSWITCH_CHECKBOX)->setAttribute('uncheckedValue', 'disabled')
->insert($view->checkbox('ForceSsl', 'enabled')->setAttribute('uncheckedValue', 'disabled'))
->insert($view->checkbox('AllowOverride', 'enabled')->setAttribute('uncheckedValue', 'disabled'))
->insert($view->checkbox('AllowUrlfOpen', 'enabled')->setAttribute('uncheckedValue', 'disabled'))

->insert($view->slider('MemoryLimit', $view::SLIDER_ENUMERATIVE | $view::LABEL_ABOVE)
    ->setAttribute('label', $T('Php memory limit (${0})')))

->insert($view->slider('UpMaxFileSize', $view::SLIDER_ENUMERATIVE | $view::LABEL_ABOVE)
    ->setAttribute('label', $T('Maximum upload file size (${0})')))

->insert($view->slider('PostMaxSize', $view::SLIDER_ENUMERATIVE | $view::LABEL_ABOVE)
    ->setAttribute('label', $T('Maximum post size (${0})')))

->insert($view->slider('MaxExecTime', $view::SLIDER_ENUMERATIVE | $view::LABEL_ABOVE)
    ->setAttribute('label', $T('Maximum execution time (${0})')))

->insert($view->slider('MaxFileUploads', $view::SLIDER_ENUMERATIVE | $view::LABEL_ABOVE)
    ->setAttribute('label', $T('Maximum file uploads (${0})')));

$view->includeCss("
    .generated-url {
        margin-bottom: 10px;
    }
    .generated-url li {
        margin-left: 25px;
        list-style-type:circle;
        font-family: monospace;
    }
");
