<template>
  <div>
    <h2>{{$t('settings.title')}}</h2>
    <div v-if="!view.isLoaded" class="spinner spinner-lg"></div>
    <div v-if="view.isLoaded">
      <h3>{{$t('settings.PhpConfiguration')}}</h3>
      <form class="form-horizontal" v-on:submit.prevent="saveSettings('configuration')">
        <!-- slider -->
        <div :class="['form-group', errors.MaxExecutionTime.hasError ? 'has-error' : '']">
            <label class="col-sm-2 control-label">{{$t('settings.MaxExecutionTime')}}
                <doc-info
                  :placement="'top'"
                  :title="$t('settings.MaxExecutionTime')"
                  :chapter="'MaxExecutionTime'"
                  :inline="true"
                ></doc-info>
            </label>
            <div class="col-sm-5">
                <div v-if="configuration.MaxExecutionTime > 0">{{configuration.MaxExecutionTime + ' '+ (configuration.MaxExecutionTime === 1 ? $t('settings.second'): $t('settings.seconds'))}}</div>
                <div v-else >{{ $t('settings.MaxExecutionTime_'+configuration.MaxExecutionTime) }}</div>
                <vue-slider v-model="configuration.MaxExecutionTime" 
                  :use-keyboard="true" 
                  :tooltip="'none'"
                  :min="0"
                  :max="3600"
                  :interval="1"
                ></vue-slider>
                <span v-if="errors.MaxExecutionTime.hasError" class="help-block">{{$t('settings.Not_valid_MaxExecutionTime')}}</span>
            </div>
        </div>
        <div  :class="['form-group', errors.MemoryLimit.hasError ? 'has-error' : '']">
            <label class="col-sm-2 control-label">{{$t('settings.MemoryLimit')}}
                <doc-info
                  :placement="'top'"
                  :title="$t('settings.MemoryLimit')"
                  :chapter="'MemoryLimit'"
                  :inline="true"
                ></doc-info>
            </label>
            <div class="col-sm-5">
                <div>{{configuration.MemoryLimit + " MB" }}</div>
                <vue-slider v-model="configuration.MemoryLimit"  
                  :use-keyboard="true" 
                  :tooltip="'none'"
                  :min="16"
                  :max="2048"
                  :interval="4"
                ></vue-slider>
                <span v-if="errors.MemoryLimit.hasError" class="help-block">{{$t('settings.Not_valid_MemoryLimit')}}</span>
            </div>
        </div>
        <div :class="['form-group', errors.PostMaxSize.hasError ? 'has-error' : '']">
            <label class="col-sm-2 control-label" >{{$t('settings.PostMaxSize')}}
                <doc-info
                  :placement="'top'"
                  :title="$t('settings.PostMaxSize')"
                  :chapter="'PostMaxSize'"
                  :inline="true"
                ></doc-info>
            </label>
            <div class="col-sm-5">
                <div>{{configuration.PostMaxSize + " MB" }}</div>
                <vue-slider v-model="configuration.PostMaxSize"  
                  :use-keyboard="true" 
                  :tooltip="'none'"
                  :min="8"
                  :max="2048"
                  :interval="4"
                ></vue-slider>
                <span v-if="errors.PostMaxSize.hasError" class="help-block">{{$t('settings.Must_be_inferior_than_MemoryLimit')}}</span>
            </div>
        </div>
        <div :class="['form-group', errors.UploadMaxFilesize.hasError ? 'has-error' : '']">
            <label class="col-sm-2 control-label" >{{$t('settings.UploadMaxFilesize')}}
                <doc-info
                  :placement="'top'"
                  :title="$t('settings.UploadMaxFilesize')"
                  :chapter="'UploadMaxFilesize'"
                  :inline="true"
                ></doc-info>
            </label>
            <div class="col-sm-5">
                <div>{{configuration.UploadMaxFilesize + " MB"}}</div>
                <vue-slider v-model="configuration.UploadMaxFilesize" 
                  :use-keyboard="true" 
                  :tooltip="'none'"
                  :min="4"
                  :max="2048"
                  :interval="4"
                ></vue-slider>
                <span v-if="errors.UploadMaxFilesize.hasError" class="help-block">{{$t('settings.Must_be_inferior_than_PostMaxSize')}}</span>
            </div>
        </div>
        
        <div class="form-group">
          <label class="col-sm-2 control-label" for="textInput-modal-markup">
            <div v-if="loaders" class="spinner spinner-sm form-spinner-loader adjust-top-loader"></div>
          </label>
          <div class="col-sm-5">
            <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import VueSlider from 'vue-slider-component'
import 'vue-slider-component/theme/default.css'


export default {
  name: "Settings",
  components: {
    VueSlider
  },
  mounted() {
    this.getSettings();

  },
  data() {
    return {
        view: {
        isLoaded: false,
        isRoot: false
      },
      configuration: {
              MaxExecutionTime: 0,
              MemoryLimit: 128,
              PostMaxSize: 8,
              UploadMaxFilesize: 4
      },
      loaders: false,
      errors: this.initErrors()
    };
  },
  methods: {
    initErrors() {
      return {
      MaxExecutionTime: {
          haserror: false,
          message:""
      },
      MemoryLimit: {
          haserror: false,
          message:""
      },
      PostMaxSize: {
          haserror: false,
          message:""
      },
      UploadMaxFilesize: {
          haserror: false,
          message:""
      }
      };
    },
    getSettings() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-httpd/settings/read"],
        {
          action: "configuration"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          // We need numeric values for sliders
          context.configuration.MaxExecutionTime = Number(success.configuration.MaxExecutionTime);
          context.configuration.MemoryLimit = Number(success.configuration.MemoryLimit);
          context.configuration.PostMaxSize = Number(success.configuration.PostMaxSize);
          context.configuration.UploadMaxFilesize = Number(success.configuration.UploadMaxFilesize);

          context.view.isLoaded = true;
        },
        function(error) {
          console.error(error);
        },
        true //sudo
      );
    },
    saveSettings(type) {
      var context = this;
      var settingsObj = {
        action: "edit",
        "configuration":{
              MaxExecutionTime: context.configuration.MaxExecutionTime,
              MemoryLimit: context.configuration.MemoryLimit,
              PostMaxSize: context.configuration.PostMaxSize,
              UploadMaxFilesize: context.configuration.UploadMaxFilesize
        }
      };
      context.loaders = true;
      context.errors = context.initErrors();
      nethserver.exec(
        ["nethserver-httpd/settings/validate"],
        settingsObj,
        null,
        function(success) {
          context.loaders = false;
      
          // notification
          nethserver.notifications.success = context.$i18n.t(
            "settings.settings_updated_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "settings.settings_updated_error"
          );

          // update values
          nethserver.exec(
            ["nethserver-httpd/settings/update"],
            settingsObj,
            function(stream) {
              console.info("nethserver-php-update", stream);
            },
            function(success) {
              context.getSettings();
            },
            function(error, data) {
              console.error(error, data);
            },
            true //sudo
          );
        },
        function(error, data) {
          var errorData = {};
          context.loaders = false;
          context.errors = context.initErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.errors[attr.parameter].hasError = true;
              context.errors[attr.parameter].message = attr.error;
            }
          } catch (e) {
            console.error(e);
          }
      },
        true // sudo
    );
    }
  }
};
</script>

<style>
</style>
