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
    ->insert($view->literal('<div class="labeled-control generated-url-title">'.$T('generated-url_url').'</div>'))
    ->insert($view->panel()->setAttribute('class', 'generated-url')->setAttribute('tag','ul'))
    ->insert($view->checkBox('Access', 'private')->setAttribute('uncheckedValue', 'public'))
    ->insert(
        $view->fieldsetSwitch('PasswordStatus', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
        ->setAttribute('uncheckedValue', 'disabled')
        ->insert($view->textInput('PasswordValue', $view::LABEL_NONE))
    )
    ->insert($view->checkbox('ForceSsl', 'enabled')->setAttribute('uncheckedValue', 'disabled'))
    ->insert($view->checkbox('AllowOverride', 'enabled')->setAttribute('uncheckedValue', 'disabled'))
;
$aliastype_id = $view->getClientEventTarget('AliasType');
$aliascustom_id = $view->getClientEventTarget('AliasCustom');
$virtualhost_id = $view->getClientEventTarget('VirtualHost');
$status_id = $view->getClientEventTarget('Status');
$ibay_id = $view->getClientEventTarget('../../ibay');
$actionId = $view->getUniqueId();

$view->includeJavascript("
(function ( $ ) {
   function getDomains() {
       var domains = [];
       if ($('#{$actionId}_VirtualHost').val() == '__ANY__') {
           $('#{$actionId}_VirtualHost option').each(function() {
               if ($(this).val() != '__ANY__') {
                   domains.push($(this).val());
               }
           });
       } else {
              domains.push($('#{$actionId}_VirtualHost').val());
       }
       return domains;
   }

   function updateUrl(custom) {
       var urls = '';
       var name = undefined;
       var d = getDomains();
       val = $('.{$aliastype_id}:checked').val();
       if (val == 'default') {
           name = $('input.{$ibay_id}').val();
       } else if (val == 'root') {
           name = '';
       } else if (val == 'custom') { //custom
           name = custom;
       }
       if (name != undefined) {
           d.forEach(function(el) {
               urls += '<li>'+el+'/'+name+'</li>';
           });
           $('.generated-url').html(urls);
       }
   }

   function toggleUrl() {
       if ($('.{$status_id}').is(':checked')) {
           $('.generated-url').removeClass('ui-state-disabled');
           $('.generated-url-title').removeClass('ui-state-disabled');
       } else {
           $('.generated-url').addClass('ui-state-disabled');
           $('.generated-url-title').addClass('ui-state-disabled');
       }
   }

   updateUrl();
   toggleUrl();
   $('.{$aliascustom_id}').on('nethguiupdateview', function (e, results) {
       updateUrl(results);
   });

   $('.{$aliastype_id}').on('change', function (e) {
       updateUrl();
   });
   $('.{$aliascustom_id}').on('change', function (e) {
       updateUrl($(this).val());
   });
   $('.{$virtualhost_id}').on('change', function (e) {
       updateUrl();
   });
   $('.{$ibay_id}').on('change', function (e) {
       updateUrl();
   });
   $('.{$status_id}').on('change', function (e) {
       toggleUrl();
   });


} ( jQuery ));
");

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
