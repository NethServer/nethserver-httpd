<!--
#
# Copyright (C) 2019 Nethesis S.r.l.
# http://www.nethesis.it - nethserver@nethesis.it
#
# This script is part of NethServer.
#
# NethServer is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License,
# or any later version.
#
# NethServer is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with NethServer.  If not, see COPYING.
#
-->

<style scoped>
.pd-indent {
  margin-left: 8px;
}
textarea {
  width: 100%;
  min-height: 7em;
  font-family: monospace;
}
select {
  padding: 0;
}
</style>

<template>
  <div
    v-bind:id="id"
    data-backdrop="static"
    class="modal modal-vhost-edit"
    tabindex="-1"
    role="dialog"
    v-bind:aria-labelledby="id + 'Label'"
    aria-hidden="true"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button
            type="button"
            class="close"
            data-dismiss="modal"
            aria-hidden="true"
            aria-label="Close"
          >
            <span class="pficon pficon-close"></span>
          </button>
          <h4 class="modal-title" v-bind:id="id + 'Label'">
            <span v-if="useCase == 'delete'">{{ $t('virtualhost.delete_title', {name: this.FirstServerName})}}</span>
            <span v-else-if="useCase == 'create'">{{ $t('virtualhost.create_title')}}</span>
            <span v-else>{{ $t('virtualhost.edit_title', {name: this.FirstServerName})}}</span>
          </h4>
        </div>

        <div v-if="useCase == 'delete'" class="modal-body">
          <div class="alert alert-info alert-dismissable">
            <span class="pficon pficon-info"></span>
            <strong>{{$t('info')}}:</strong>
            {{$t('virtualhost.dnsHostRecord_do_not_be_removed')}}.
          </div>
          <form class="form-horizontal">
            <div class="form-group">
              <label
                class="col-sm-3 control-label"
                for="textInput-modal-markup"
              >{{$t('are_you_sure')}}?</label>
            </div>
          </form>
        </div>

        <div v-else class="modal-body">
          <!-- hosts record creation -->
          <div v-if="useCase == 'create'">
            <div class="alert alert-info alert-dismissable">
              <span class="pficon pficon-info"></span>
              <strong>{{$t('info')}}:</strong>
              {{$t('virtualhost.automatic_HostRecord_creation')}}.
            </div>
          </div>
          <form class="form-horizontal">
            <!-- FQDN -->
            <div
              v-if="name !== 'default'"
              v-bind:class="['form-group', vErrors.ServerNames ? 'has-error' : '']"
            >
              <label
                class="col-sm-3 control-label"
                for="textInput-modal-markup"
              >{{$t('virtualhost.ServerNames')}}</label>
              <div class="col-sm-9">
                <textarea
                  v-bind:id="id + '-ServerName'"
                  v-model="ServerNames"
                  class="form-control"
                  :placeholder="$t('virtualhost.FQDN_help')"
                ></textarea>
                <span v-if="vErrors.ServerNames" class="help-block">{{vErrors.ServerNames}}</span>
              </div>
            </div>

            <div class="form-group">
              <label
                class="col-sm-3 control-label"
                v-bind:for="id + '-di'"
              >{{ $t('virtualhost.description_label') }}</label>
              <div class="col-sm-9">
                <input
                  type="text"
                  v-model="Description"
                  v-bind:id="id + '-di'"
                  class="form-control"
                >
                <span v-if="vErrors.Description" class="help-block">{{vErrors.Description}}</span>
              </div>
            </div>

            <!-- advanced menu -->
            <legend class="fields-section-header-pf" aria-expanded="true">
              <span
                :class="['fa fa-angle-right field-section-toggle-pf', advanced ? 'fa-angle-down' : '']"
              ></span>
              <a
                class="field-section-toggle-pf"
                @click="toggleAdvancedMode()"
              >{{$t('proxypass.advanced_mode')}}</a>
            </legend>

            <!-- trusted network -->
            <div v-if="name !== 'default' && advanced">
              <div v-bind:class="['form-group', vErrors.Access ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  v-bind:for="id + '-access'"
                >{{$t('virtualhost.Trusted_Network_Only')}}</label>
                <div class="col-sm-9">
                  <input
                    type="checkbox"
                    true-value="private"
                    false-value="public"
                    v-model="Access"
                    v-bind:id="id + '-access'"
                    class="form-control"
                  >
                  <span v-if="vErrors.Access" class="help-block">{{ vErrors.Access }}</span>
                </div>
              </div>
            </div>

            <!-- Httpd Access -->
            <div v-if="name !== 'default' && advanced ">
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('virtualhost.Apache_Authentication')}}</label>
                <div class="col-sm-9">
                  <toggle-button
                    class="min-toggle"
                    :width="40"
                    :height="20"
                    :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
                    :value="PasswordStatus == 'enabled'"
                    :sync="true"
                    @change="PasswordStatus == 'enabled' ? PasswordStatus = 'disabled' : PasswordStatus = 'enabled'"
                  />
                </div>
              </div>

              <div v-if="PasswordStatus == 'enabled' && advanced">
                <div v-bind:class="['form-group', vErrors.name ? 'has-error' : '']">
                  <label
                    class="col-sm-3 control-label"
                    v-bind:for="id + '-un'"
                  >{{ $t('virtualhost.UserName') }}</label>
                  <div class="col-sm-9">
                    <input
                      disabled
                      type="text"
                      v-model="name"
                      v-bind:id="id + '-un'"
                      class="form-control"
                    >
                    <span v-if="vErrors.name" class="help-block">{{ vErrors.name }}</span>
                  </div>
                </div>
              </div>

              <div
                v-if="PasswordStatus == 'enabled' && advanced"
                :class="['form-group', vErrors.PasswordValue ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('virtualhost.PasswordValue')}}</label>
                <div class="col-sm-6">
                  <input
                    :type="togglePass ? 'text' : 'password'"
                    v-model="PasswordValue"
                    name="PasswordValue"
                    class="form-control"
                  >
                  <span v-if="vErrors.PasswordValue" class="help-block">{{ vErrors.PasswordValue }}</span>
                </div>
                <div class="col-sm-3">
                  <button class="btn btn-primary" type="button" @click="togglePassHidden()">
                    <span :class="['fa', togglePass ? 'fa-eye' : 'fa-eye-slash']"></span>
                  </button>
                </div>
              </div>
            </div>

            <!-- SSL forced -->
            <div v-if="name !== 'default' && advanced">
              <div v-bind:class="['form-group', vErrors.ForceSslStatus ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  v-bind:for="id + '-ssl'"
                >{{$t('virtualhost.Force_HTTPS')}}</label>
                <div class="col-sm-9">
                  <input
                    type="checkbox"
                    true-value="enabled"
                    false-value="disabled"
                    v-model="ForceSslStatus"
                    v-bind:id="id + '-ssl'"
                    class="form-control"
                  >
                  <span
                    v-if="vErrors.ForceSslStatus"
                    class="help-block"
                  >{{ vErrors.ForceSslStatus }}</span>
                </div>
              </div>

              <!-- root directory file listing  -->
              <div v-bind:class="['form-group', vErrors.Indexes ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  v-bind:for="id + '-indexes'"
                >{{$t('virtualhost.Root_file_listing')}}</label>
                <div class="col-sm-9">
                  <input
                    type="checkbox"
                    true-value="enabled"
                    false-value="disabled"
                    v-model="Indexes"
                    v-bind:id="id + '-indexes'"
                    class="form-control"
                  >
                  <span v-if="vErrors.Indexes" class="help-block">{{ vErrors.Indexes }}</span>
                </div>
              </div>

              <!-- Certificate -->
              <div v-bind:class="['form-group', vErrors.Indexes ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  v-bind:for="id + '-certs'"
                >{{$t('virtualhost.SSL/TLS_certs')}}</label>
                <div class="col-sm-9">
                  <select
                    type="text"
                    v-model="SslCertificate"
                    class="combobox form-control col-sm-9"
                  >
                    <option value>{{$t('virtualhost.default_certificate')}}</option>
                    <option v-for="(cert,k) in certificates" :key="k" :value="cert">{{cert}}</option>
                  </select>
                  <span
                    v-if="vErrors.SslCertificate"
                    class="help-block"
                  >{{ vErrors.SslCertificate }}</span>
                </div>
              </div>
            </div>

            <!-- FTP access -->
            <div v-if="advanced" class="form-group">
              <label
                class="col-sm-3 control-label"
                for="textInput-modal-markup"
              >{{$t('virtualhost.Ftp_Authentication')}}</label>
              <div class="col-sm-9">
                <toggle-button
                  class="min-toggle"
                  :width="40"
                  :height="20"
                  :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
                  :value="FtpStatus == 'enabled'"
                  :sync="true"
                  @change="FtpStatus == 'enabled' ? FtpStatus = 'disabled' : FtpStatus = 'enabled'"
                />
              </div>
            </div>

            <div v-if="FtpStatus == 'enabled' && advanced">
              <div v-if="!vsftpd" class="alert alert-warning alert-dismissable">
                <span class="pficon pficon-warning-triangle-o"></span>
                <strong>{{$t('info')}}:</strong>
                {{$t('virtualhost.vsftpd_is_not_running')}}.
              </div>
              <div v-bind:class="['form-group', vErrors.name ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  v-bind:for="id + '-fun'"
                >{{ $t('virtualhost.UserName') }}</label>
                <div class="col-sm-9">
                  <input
                    disabled
                    type="text"
                    v-model="name"
                    v-bind:id="id + '-fun'"
                    class="form-control"
                  >
                  <span v-if="vErrors.name" class="help-block">{{ vErrors.name }}</span>
                </div>
              </div>
            </div>

            <div
              v-if="FtpStatus == 'enabled' && advanced"
              :class="['form-group', vErrors.FtpPassword ? 'has-error' : '']"
            >
              <label
                class="col-sm-3 control-label"
                for="textInput-modal-markup"
              >{{$t('virtualhost.FtpPassword')}}</label>
              <div class="col-sm-6">
                <input
                  :type="togglePassFtp ? 'text' : 'password'"
                  v-model="FtpPassword"
                  name="FtpPassword"
                  class="form-control"
                >
                <span v-if="vErrors.FtpPassword" class="help-block">{{ vErrors.FtpPassword }}</span>
              </div>
              <div class="col-sm-3">
                <button class="btn btn-primary" type="button" @click="togglePassHiddenFtp()">
                  <span :class="['fa', togglePassFtp ? 'fa-eye' : 'fa-eye-slash']"></span>
                </button>
              </div>
            </div>
            <!-- phpsettings menu -->
            <legend v-if="name !== 'default' && advanced" class="fields-section-header-pf" aria-expanded="true">
              <span
                :class="['fa fa-angle-right field-section-toggle-pf', phpsettings ? 'fa-angle-down' : '']"
              ></span>
              <a
                class="field-section-toggle-pf"
                @click="togglePhpSettingsMenu()"
              >{{$t('virtualhost.phpsettings_menu')}}</a>
            </legend>

            <div v-if="name !== 'default' && phpsettings  && advanced">
              
              <div v-if="!rhPhpScl.php72 && PhpRhVersion === 'php72'" class="alert alert-warning alert-dismissable">
                <span class="pficon pficon-warning-triangle-o"></span>
                <strong>{{$t('virtualhost.info_phpscl')}}:</strong>
                {{$t('virtualhost.php72_not_installed')}}.
              </div>
              <div v-if="!rhPhpScl.php71 && PhpRhVersion === 'php71'" class="alert alert-warning alert-dismissable">
                <span class="pficon pficon-warning-triangle-o"></span>
                <strong>{{$t('virtualhost.info_phpscl')}}:</strong>
                {{$t('virtualhost.php71_not_installed')}}.
              </div>

              <!-- php version -->
              <div v-bind:class="['form-group', vErrors.PhpRhVersion ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  v-bind:for="id + '-certs'"
                >{{$t('virtualhost.php_version')}}</label>
                <div class="col-sm-9">
                  <select
                    type="text"
                    v-model="PhpRhVersion"
                    class="combobox form-control col-sm-9"
                  >
                    <option value="default">{{$t('virtualhost.default_php_version')}}</option>
                    <option value="php71">{{$t('virtualhost.php71_version')}}</option>
                    <option value="php72">{{$t('virtualhost.php72_version')}}</option>
                  </select>
                  <span
                    v-if="vErrors.PhpRhVersion"
                    class="help-block"
                  >{{ vErrors.PhpRhVersion }}</span>
                </div>
              </div>

              <!-- Cutomise php for default php version -->
              <div v-bind:class="['form-group', vErrors.phpCustomSettings ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  v-bind:for="id + '-defaultPhpSettings'"
                >{{$t('virtualhost.phpCustomSettings')}}</label>
                <div class="col-sm-9">
                    <toggle-button
                      class="min-toggle"
                      :width="40"
                      :height="20"
                      :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
                      :value="phpCustomSettings == 'enabled'"
                      :sync="true"
                      @change="phpCustomSettings == 'enabled' ? phpCustomSettings = 'disabled' : phpCustomSettings = 'enabled'"
                    />
                  <span
                    v-if="vErrors.phpCustomSettings"
                    class="help-block"
                  >{{ vErrors.phpCustomSettings }}</span>
                </div>
              </div>

              <!-- slider -->
              <div v-if="phpCustomSettings === 'enabled'">
                <div :class="['form-group', vErrors.MaxExecutionTime ? 'has-error' : '']">
                    <label class="col-sm-3 control-label">{{$t('virtualhost.MaxExecutionTime')}}
                        <doc-info
                          :placement="'top'"
                          :title="$t('virtualhost.MaxExecutionTime')"
                          :chapter="'MaxExecutionTime'"
                          :inline="true"
                        ></doc-info>
                    </label>
                    <div class="col-sm-9">
                        <div>{{ $t('virtualhost.MaxExecutionTime_'+MaxExecutionTime) }}</div>
                        <vue-slider v-model="MaxExecutionTime" :data="SliderMaxExecutionTime" :use-keyboard="true" :tooltip="'none'"></vue-slider>
                        <span v-if="vErrors.MaxExecutionTime" class="help-block">{{$t('virtualhost.Not_valid_MaxExecutionTime')}}</span>
                    </div>
                </div>
                <div  :class="['form-group', vErrors.MemoryLimit ? 'has-error' : '']">
                    <label class="col-sm-3 control-label">{{$t('virtualhost.MemoryLimit')}}
                        <doc-info
                          :placement="'top'"
                          :title="$t('virtualhost.MemoryLimit')"
                          :chapter="'MemoryLimit'"
                          :inline="true"
                        ></doc-info>
                    </label>
                    <div class="col-sm-9">
                        <div>{{ $t('virtualhost.MemoryLimit_'+MemoryLimit) }}</div>
                        <vue-slider v-model="MemoryLimit"  :data="SliderMemoryLimit" :tooltip="'none'"></vue-slider>
                        <span v-if="vErrors.MemoryLimit" class="help-block">{{$t('virtualhost.Not_valid_MemoryLimit')}}</span>
                    </div>
                </div>
                <div :class="['form-group', vErrors.PostMaxSize ? 'has-error' : '']">
                    <label class="col-sm-3 control-label" >{{$t('virtualhost.PostMaxSize')}}
                        <doc-info
                          :placement="'top'"
                          :title="$t('virtualhost.PostMaxSize')"
                          :chapter="'PostMaxSize'"
                          :inline="true"
                        ></doc-info>
                    </label>
                    <div class="col-sm-9">
                        <div>{{ $t('virtualhost.PostMaxSize_'+PostMaxSize) }}</div>
                        <vue-slider v-model="PostMaxSize"  :data="SliderPostMaxSize" :tooltip="'none'"></vue-slider>
                        <span v-if="vErrors.PostMaxSize" class="help-block">{{$t('virtualhost.Must_be_inferior_than_MemoryLimit')}}</span>
                    </div>
                </div>
                <div :class="['form-group', vErrors.UploadMaxFilesize ? 'has-error' : '']">
                    <label class="col-sm-3 control-label" >{{$t('virtualhost.UploadMaxFilesize')}}
                        <doc-info
                          :placement="'top'"
                          :title="$t('virtualhost.UploadMaxFilesize')"
                          :chapter="'UploadMaxFilesize'"
                          :inline="true"
                        ></doc-info>
                    </label>
                    <div class="col-sm-9">
                        <div>{{$t('virtualhost.UploadMaxFilesize_'+UploadMaxFilesize)}}</div>
                        <vue-slider v-model="UploadMaxFilesize" :data="SliderUploadMaxFilesize" :use-keyboard="true" :tooltip="'none'"></vue-slider>
                        <span v-if="vErrors.UploadMaxFilesize" class="help-block">{{$t('virtualhost.Must_be_inferior_than_PostMaxSize')}}</span>
                    </div>
                </div>
              </div>
          </div>
          </form>
        </div>
        <div class="modal-footer">
          <div v-if="loader" class="spinner spinner-sm form-spinner-loader"></div>
          <button
            type="button"
            class="btn btn-default"
            data-dismiss="modal"
          >{{ $t('modal.cancel_button') }}</button>
          <button
            v-if="useCase == 'delete'"
            v-on:click="$emit('modal-save')"
            type="button"
            class="btn btn-danger"
          >{{ $t('delete') }}</button>
          <button
            v-else-if="useCase == 'create'"
            v-on:click="$emit('modal-save')"
            type="button"
            class="btn btn-primary"
          >{{ $t('create') }}</button>
          <button
            v-else
            v-on:click="$emit('modal-save')"
            type="button"
            class="btn btn-primary"
          >{{ $t('edit') }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import execp from "@/execp";
import VueSlider from 'vue-slider-component'
import 'vue-slider-component/theme/default.css'

var attrs = [
  "name",
  "Description",
  "ServerNames",
  "Access",
  "PasswordStatus",
  "PasswordValue",
  "ForceSslStatus",
  "Indexes",
  "FtpStatus",
  "FtpPassword",
  "SslCertificate",
  "status",
  "PhpRhVersion",
  "MaxExecutionTime",
  "MemoryLimit",
  "PostMaxSize",
  "UploadMaxFilesize",
  "phpCustomSettings"
];

export default {
  name: "ModalVhostEdit",
  components: {
    VueSlider
  },
  props: {
    id: String,
    useCase: String,
    virtualhost: Object,
    certificates: Array,
    vsftpd: Number,
    advanced: false,
    togglePass: "password",
    togglePassFtp: "password",
    phpsettings: false,
    rhPhpScl: Object

  },
  watch: {
    virtualhost: function(newval) {
      this.vErrors = {};
      for (let i in attrs) {
        this[attrs[i]] = newval[attrs[i]] || "";
      }
      // split servername array (index2)
      if (this.ServerNames) {
        this.ServerNames = newval.ServerNames.filter( v => v ).join("\n") || "";
        if(this.ServerNames[0]) {
            this.FirstServerName = newval.ServerNames[0];
        }
      }
    }
  },
  data() {
    var obj = {
      vErrors: {},
      loader: false,
      ServerNames: "",
      FirstServerName: "",
      PhpRhVersion: "default",
      SliderMaxExecutionTime:['30','60','120','180','240','300','360','420','480','540','600','0'],
      SliderMemoryLimit: ['128','192','256','320','384','448','512','576','640','704','768','832','896','960','1024','1536','2048'],
      SliderPostMaxSize: ['8','32','64','128','192','256','320','384','448','512','576','640','704','768','832','896','960','1024','1536','2048'],
      SliderUploadMaxFilesize: ['2','8','16','32','64','128','192','256','320','384','448','512','576','640','704','768','832','896','960','1024','1536','2048'],
    };
    for (let i in attrs) {
      obj[attrs[i]] = "";
    }
    return obj;
  },
  mounted: function() {
    this.$on("modal-save", eventData => {
      this.loader = true;
      var inputData = {
        action: this.$props["useCase"],
        virtualhost: {}
      };
      for (let i in attrs) {
        inputData.virtualhost[attrs[i]] = this[attrs[i]];
      }
      // split ServerName(index2)
      inputData.virtualhost.ServerNames = this.ServerNames.split("\n").filter(v => v);

      this.vErrors = {};
      execp("nethserver-httpd/virtualhost/validate", inputData)
        .catch(validationError => {
          let err = {};
          for (let i in validationError.attributes) {
            let attr = validationError.attributes[i];
            err[attr.parameter] = this.$t("validation." + attr.error);
          }
          this.vErrors = err;
          this.loader = false;
          return Promise.reject(validationError); // still unresolved
        })
        .then(validationResult => {
          this.loader = false;
          window.jQuery(this.$el).modal("hide"); // on successful resolution close the dialog

          nethserver.notifications.success = this.$t(
            "virtualhost.vhost_" +
              (this.useCase == "create" ? "created" : "updated") +
              "_ok"
          );
          nethserver.notifications.error = this.$t(
            "virtualhost.vhost_" +
              (this.useCase == "create" ? "created" : "updated") +
              "_error"
          );

          return execp("nethserver-httpd/virtualhost/update", inputData, true); // start another async call
        })
        .finally(() => {
          // stop the spinner
          this.loader = false;
        })
        .then(() => {
          this.$emit("modal-close", eventData);
        });
    });
  },
  methods: {
    toggleAdvancedMode() {
      this.advanced = !this.advanced;
      this.$forceUpdate();
    },
    togglePhpSettingsMenu() {
      this.phpsettings = !this.phpsettings;
      this.$forceUpdate();
    },
    togglePassHidden() {
      this.togglePass = !this.togglePass;
      this.$forceUpdate();
    },
    togglePassHiddenFtp() {
      this.togglePassFtp = !this.togglePassFtp;
      this.$forceUpdate();
    }
  }
};
</script>
