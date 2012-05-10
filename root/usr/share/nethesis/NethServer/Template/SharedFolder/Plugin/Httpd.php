<?php

echo $view->fieldset()
    ->setAttribute('template', $T('Web server options'))
    // ->setAttribute('icon-before', 'ui-icon-link')
    ->insert($view->checkBox('CgiBin', 'enabled')->setAttribute('uncheckedValue', 'disabled'))
;
