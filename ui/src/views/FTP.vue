<!--
/*
 * Copyright (C) 2019 Nethesis S.r.l.
 * http://www.nethesis.it - nethserver@nethesis.it
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License,
 * or any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see COPYING.
 */
-->

<template>
  <div>
    <h2>{{ $t('ftp.title') }}</h2>
    <doc-info
      :placement="'top'"
      :title="$t('docs.ftp')"
      :chapter="'ftp'"
      :section="''"
      :inline="false"
    ></doc-info>

    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>
    <div v-show="!view.menu.installed && view.isLoaded">
      <div class="blank-slate-pf" id>
        <div class="blank-slate-pf-icon">
          <span class="pficon pficon pficon-add-circle-o"></span>
        </div>
        <h1>{{$t('package_required')}}</h1>
        <p>{{$t('package_required_desc')}}.</p>
        <pre>{{view.menu.packages.join(' ')}}</pre>
        <div class="blank-slate-pf-main-action">
          <button
            :disabled="view.isInstalling"
            @click="installPackages()"
            class="btn btn-primary btn-lg"
          >{{view.menu.packages.length == 1 ? $t('install_package') : $t('install_packages')}}</button>
          <div v-if="view.isInstalling" class="spinner spinner-sm"></div>
        </div>
      </div>
    </div>

    <div v-show="view.menu.installed && view.isLoaded">
      <h3 v-if="configuration.status == 'enabled'">{{ $t('ftp.configuration') }}</h3>

      <div v-if="configuration.status == 'disabled' && accounts.length > 0" class="blank-slate-pf">
        <h1>{{$t('ftp.ftp_is_disabled')}}</h1>
        <p>{{$t('ftp.ftp_is_disabled_desc')}}.</p>
        <div class="blank-slate-pf-main-action">
          <button
            @click="saveConfiguration(true)"
            class="btn btn-primary btn-lg"
          >{{$t('ftp.enable_ftp')}}</button>
        </div>
      </div>
      <div v-if="configuration.status == 'enabled'" class="panel panel-default">
        <div class="panel-heading">
          <toggle-button
            class="min-toggle right"
            :width="40"
            :height="20"
            :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
            :value="configuration.status == 'enabled'"
            :sync="true"
            @change="saveConfiguration(true)"
          />
          <span class="panel-title">
            {{$t('ftp.enabled')}}
            <span
              :class="['fa', configuration.status == 'enabled' ? 'fa-check green' : 'fa-times red']"
            ></span>
          </span>
        </div>
      </div>

      <h3 v-if="accounts.length > 0">{{$t('ftp.actions')}}</h3>
      <div v-if="accounts.length > 0" class="btn-group">
        <button
          @click="openCreateAccount()"
          class="btn btn-primary btn-lg"
        >{{$t('ftp.create_user')}}</button>
      </div>

      <h3 v-if="accounts.length > 0">{{$t('ftp.account_list')}}</h3>
      <div v-if="accounts.length > 0" class="row">
        <form role="form" class="search-pf has-button search col-sm-6 no-padding-left">
          <div class="form-group has-clear">
            <div class="search-pf-input-group">
              <label class="sr-only">Search</label>
              <input
                v-focus
                type="search"
                v-model="searchString"
                class="form-control input-lg"
                :placeholder="$t('search')+'...'"
              >
            </div>
          </div>
        </form>
      </div>

      <div v-if="accounts.length == 0" class="blank-slate-pf">
        <div class="blank-slate-pf-icon">
          <span class="fa fa-users"></span>
        </div>
        <h1>{{$t('ftp.user_not_found')}}</h1>
        <p>{{$t('ftp.user_not_found_desc')}}.</p>
        <div class="blank-slate-pf-main-action">
          <button
            @click="openCreateAccount()"
            class="btn btn-primary btn-lg"
          >{{$t('ftp.create_user')}}</button>
        </div>
      </div>

      <div
        v-if="accounts.length > 0"
        class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-10"
      >
        <div
          v-for="(m, mk) in filteredAccounts"
          v-bind:key="mk"
          :class="['list-group-item', m.status == 'disabled' ? 'gray' : '']"
        >
          <div class="list-view-pf-actions">
            <button
              @click="m.status == 'disabled' ? toggleStatusAccount(m) : openEditAccount(m)"
              :class="['btn btn-default', m.status == 'disabled' ? 'btn-primary' : '']"
            >
              <span
                :class="['fa', m.status == 'disabled' ? 'fa-check' : 'fa-pencil', 'span-right-margin']"
              ></span>
              {{m.status == 'disabled' ? $t('enable') : $t('edit')}}
            </button>
            <div class="dropup pull-right dropdown-kebab-pf">
              <button
                class="btn btn-link dropdown-toggle"
                type="button"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="true"
              >
                <span class="fa fa-ellipsis-v"></span>
              </button>
              <ul class="dropdown-menu dropdown-menu-right">
                <li>
                  <a @click="toggleStatusAccount(m)">
                    <span
                      :class="['fa', m.status == 'enabled' ? 'fa-lock' : 'fa-check', 'span-right-margin']"
                    ></span>
                    {{m.status == 'enabled' ? $t('disable') : $t('enable')}}
                  </a>
                </li>
                <li role="presentation" class="divider"></li>
                <li>
                  <a @click="openDeleteAccount(m)">
                    <span class="fa fa-times span-right-margin"></span>
                    {{$t('delete')}}
                  </a>
                </li>
              </ul>
            </div>
          </div>

          <div class="list-view-pf-main-info small-list">
            <div class="list-view-pf-left">
              <span :class="['fa', 'list-view-pf-icon-sm', 'fa fa-user']"></span>
            </div>
            <div class="list-view-pf-body">
              <div class="list-view-pf-description">
                <div class="list-group-item-heading">{{m.name}}</div>
                <div class="list-group-item-text">{{m.Description}}</div>
              </div>
              <div class="list-view-pf-additional-info rules-info"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="createAccountModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{currentAccount.isEdit ? $t('ftp.edit_account') + ' '+ currentAccount.name : $t('ftp.add_account')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveAccount(currentAccount)">
            <div class="modal-body">
              <div :class="['form-group', currentAccount.errors.name.hasError ? 'has-error' : '']">
                <label
                  :class="['col-sm-3', 'control-label']"
                  for="textInput-modal-markup"
                >{{$t('ftp.username')}}</label>
                <div :class="'col-sm-9'">
                  <input required type="text" v-model="currentAccount.name" class="form-control">
                  <span
                    v-if="currentAccount.errors.name.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentAccount.errors.name.message)}}</span>
                </div>
              </div>
              <div
                :class="['form-group', currentAccount.errors.Password.hasError ? 'has-error' : '']"
              >
                <label
                  :class="['col-sm-3', 'control-label']"
                  for="textInput-modal-markup"
                >{{$t('ftp.password')}}</label>
                <div :class="'col-sm-9'">
                  <input
                    required
                    type="text"
                    v-model="currentAccount.Password"
                    class="form-control"
                  >
                  <span
                    v-if="currentAccount.errors.Password.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentAccount.errors.Password.message)}}</span>
                </div>
              </div>

              <div
                :class="['form-group', currentAccount.errors.Description.hasError ? 'has-error' : '']"
              >
                <label
                  :class="['col-sm-3', 'control-label']"
                  for="textInput-modal-markup"
                >{{$t('ftp.description')}}</label>
                <div :class="'col-sm-9'">
                  <input type="text" v-model="currentAccount.Description" class="form-control">
                  <span
                    v-if="currentAccount.errors.Description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentAccount.errors.Description.message)}}</span>
                </div>
              </div>

              <legend class="fields-section-header-pf" aria-expanded="true">
                <span
                  :class="['fa fa-angle-right field-section-toggle-pf', currentAccount.advanced ? 'fa-angle-down' : '']"
                ></span>
                <a class="field-section-toggle-pf" @click="toggleAdvanced()">{{$t('advanced_mode')}}</a>
              </legend>

              <div
                v-if="currentAccount.advanced"
                :class="['form-group', currentAccount.errors.Chroot.hasError ? 'has-error' : '']"
              >
                <label
                  :class="['col-sm-3', 'control-label']"
                  for="textInput-modal-markup"
                >{{$t('ftp.chroot')}}</label>
                <div :class="'col-sm-9'">
                  <toggle-button
                    class="min-toggle"
                    :width="40"
                    :height="20"
                    :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
                    :value="currentAccount.Chroot"
                    :sync="true"
                    @change="currentAccount.Chroot = !currentAccount.Chroot"
                  />
                  <span
                    v-if="currentAccount.errors.Chroot.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentAccount.errors.Chroot.message)}}</span>
                </div>
              </div>
              <div
                v-if="currentAccount.Chroot && currentAccount.advanced"
                :class="['form-group', currentAccount.errors.ChrootDir.hasError ? 'has-error' : '']"
              >
                <label
                  :class="['col-sm-3', 'control-label']"
                  for="textInput-modal-markup"
                >{{$t('ftp.chroot_dir')}}</label>
                <div :class="'col-sm-9'">
                  <input
                    placeholder="/var/lib/nethserver/vhost/<name>"
                    type="text"
                    v-model="currentAccount.ChrootDir"
                    class="form-control"
                  >
                  <span
                    v-if="currentAccount.errors.ChrootDir.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentAccount.errors.ChrootDir.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="currentAccount.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button
                class="btn btn-primary"
                type="submit"
              >{{currentAccount.isEdit ? $t('edit') : $t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="deleteAccountModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('ftp.delete_account')}} {{toDeleteAccount.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteAccount(toDeleteAccount)">
            <div class="modal-body">
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('are_you_sure')}}?</label>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-danger" type="submit">{{$t('delete')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
var nethserver = window.nethserver;
var console = window.console;

export default {
  name: "Configuration",
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-httpd/feature/read"],
        {
          name: vm.$route.path.substr(1)
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }

          vm.view.menu = success;
        },
        function(error) {
          console.error(error);
        },
        false
      );
    });
  },
  mounted() {
    this.getConfiguration();
    this.getAccounts();
  },
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    next();
  },
  data() {
    return {
      view: {
        isLoaded: false,
        menu: {
          installed: false,
          packages: []
        }
      },
      searchString: "",
      configuration: {
        status: "disabled"
      },
      accounts: [],
      currentAccount: this.initAccount(),
      toDeleteAccount: { name: "" }
    };
  },
  computed: {
    filteredAccounts() {
      var returnObj = [];
      for (var r in this.accounts) {
        var cat = JSON.stringify(this.accounts[r]);
        if (cat.toLowerCase().includes(this.searchString.toLowerCase())) {
          returnObj.push(this.accounts[r]);
        }
      }

      return returnObj;
    }
  },
  methods: {
    installPackages() {
      this.view.isInstalling = true;
      // notification
      nethserver.notifications.success = this.$i18n.t("packages_installed_ok");
      nethserver.notifications.error = this.$i18n.t("packages_installed_error");

      nethserver.exec(
        ["nethserver-httpd/feature/update"],
        {
          name: this.$route.path.substr(1)
        },
        function(stream) {
          console.info("install-package", stream);
        },
        function(success) {
          // reload page
          window.parent.location.reload();
        },
        function(error) {
          console.error(error);
        }
      );
    },
    initAccount() {
      return {
        name: "",
        Password: "",
        Chroot: "",
        ChrootDir: "",
        Description: "",
        errors: this.initAccountErrors(),
        isLoading: false,
        isEdit: false,
        advanced: false
      };
    },
    initAccountErrors() {
      return {
        name: {
          hasError: false,
          message: ""
        },
        Password: {
          hasError: false,
          message: ""
        },
        Chroot: {
          hasError: false,
          message: ""
        },
        ChrootDir: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
    },
    toggleAdvanced() {
      this.currentAccount.advanced = !this.currentAccount.advanced;
      this.$forceUpdate();
    },
    getAccounts() {
      var context = this;

      nethserver.exec(
        ["nethserver-httpd/ftp/read"],
        {
          action: "users"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.accounts = success.users;
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getConfiguration() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-httpd/ftp/read"],
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
          context.configuration = success.configuration;

          context.view.isLoaded = true;
        },
        function(error) {
          console.error(error);
          context.view.isLoaded = true;
        }
      );
    },
    saveConfiguration(toggle) {
      var context = this;

      if (toggle) {
        context.configuration.status =
          context.configuration.status == "enabled" ? "disabled" : "enabled";
      }

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "ftp.configuration_updated_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "ftp.configuration_updated_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-httpd/ftp/update"],
        {
          action: "configuration",
          status: context.configuration.status
        },
        function(stream) {
          console.info("configuration", stream);
        },
        function(success) {
          // get all
          context.getConfiguration();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    openCreateAccount() {
      this.currentAccount = this.initAccount();
      this.currentAccount.Chroot = this.currentAccount.Chroot == "enabled";
      $("#createAccountModal").modal("show");
    },
    openEditAccount(account) {
      this.currentAccount = JSON.parse(JSON.stringify(account));
      this.currentAccount.Chroot = this.currentAccount.Chroot == "enabled";
      this.currentAccount.errors = this.initAccountErrors();

      this.currentAccount.isEdit = true;
      this.currentAccount.isLoading = false;
      this.currentAccount.advanced = false;
      $("#createAccountModal").modal("show");
    },
    saveAccount(account) {
      var context = this;

      var accountObj = {
        action: account.isEdit ? "update" : "create",
        Chroot: account.Chroot ? "enabled" : "disabled",
        ChrootDir: account.ChrootDir,
        Password: account.Password,
        Description: account.Description,
        name: account.name,
        status: "enabled"
      };

      context.currentAccount.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-httpd/ftp/validate"],
        accountObj,
        null,
        function(success) {
          context.currentAccount.isLoading = false;
          $("#createAccountModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "ftp.account_" +
              (context.currentAccount.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "ftp.account_" +
              (context.currentAccount.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (account.isEdit) {
            nethserver.exec(
              ["nethserver-httpd/ftp/update"],
              accountObj,
              function(stream) {
                console.info("account-edit", stream);
              },
              function(success) {
                // get all
                context.getAccounts();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-httpd/ftp/create"],
              accountObj,
              function(stream) {
                console.info("account-create", stream);
              },
              function(success) {
                // get all
                context.getAccounts();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
        },
        function(error, data) {
          var errorData = {};
          context.currentAccount.isLoading = false;
          context.currentAccount.errors = context.initAccountErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.currentAccount.errors[attr.parameter].hasError = true;
              context.currentAccount.errors[attr.parameter].message =
                attr.error;
              context.$forceUpdate();
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    toggleStatusAccount(account) {
      var context = this;
      // notification
      nethserver.notifications.success = context.$i18n.t(
        "ftp.account_updated_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "ftp.account_updated_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-httpd/ftp/update"],
        {
          action: account.status == "enabled" ? "disable" : "enable",
          name: account.name
        },
        function(stream) {
          console.info("update-status", stream);
        },
        function(success) {
          // get all
          context.getAccounts();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    openDeleteAccount(account) {
      this.toDeleteAccount = JSON.parse(JSON.stringify(account));
      $("#deleteAccountModal").modal("show");
    },
    deleteAccount(account) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "ftp.account_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "ftp.account_deleted_error"
      );

      $("#deleteAccountModal").modal("hide");
      nethserver.exec(
        ["nethserver-httpd/ftp/delete"],
        {
          name: account.name
        },
        function(stream) {
          console.info("account-delete", stream);
        },
        function(success) {
          // get all
          context.getAccounts();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    }
  }
};
</script>

<style>
.list-group-item-heading {
  width: calc(50% - 20px) !important;
}
.list-group-item-text {
  width: calc(50% - 40px) !important;
}
.list-view-pf-description {
  flex: 1 0 70% !important;
}
.list-view-pf-actions {
  z-index: 2;
}
.remove-cat {
  margin-top: 6px;
  color: dimgrey;
}
.remove-cat:hover {
  cursor: pointer;
  color: #39a5dc;
}

.adjust-mg-bottom {
  margin-bottom: 4px;
}
.adjust-divider {
  margin-top: 15px;
}
.adjust-filter-cat {
  margin-top: 5px;
}

.right {
  float: right;
}

.green {
  color: #3f9c35;
}
.red {
  color: #cc0000;
}
.gray {
  color: gray;
}

.no-padding-left {
  padding-left: 0px;
}
</style>
