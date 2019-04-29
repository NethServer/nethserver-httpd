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

<template>
  <div class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-10">
    <div v-bind:key="item.id" v-for="item in items" class="list-group-item">
      <div class="list-view-pf-actions">
        <button :disabled="item.status === 'disabled'" class="btn btn-default" v-on:click="$emit('item-edit', item)">
          <span class="fa fa-pencil"></span>
          {{ $t('virtualhost.item_edit_button')}}
        </button>
        <div  class="dropdown pull-right dropdown-kebab-pf">
          <button
            class="btn btn-link dropdown-toggle"
            type="button"
            v-bind:id="item.id + '-ddm'"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            :disabled="item.name === 'default'"
          >
            <span class="fa fa-ellipsis-v"></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-right" v-bind:aria-labelledby="item.id + '-ddm'">
            <li>
              <a @click="toggleLock(item)">
                <span class="pficon pficon-locked span-right-margin"></span>
                {{ item.status === 'disabled' ? $t('virtualhost.item_enable_button') : $t('virtualhost.item_disable_button') }}
              </a>
            </li>
            <li role="separator" class="divider"></li>
            <li>
              <a href="#" v-on:click="$emit('item-delete', item)">
                <span class="fa pficon-delete span-right-margin"></span>
                {{ $t('virtualhost.item_delete_button') }}
              </a>
            </li>
          </ul>
        </div>
      </div>

      <div class="list-view-pf-main-info small-list">
        <div class="list-view-pf-left">
          <span
            class="fa list-view-pf-icon-sm pficon-folder-open"
          ></span>
        </div>
        <div class="list-view-pf-body">
          <div class="list-view-pf-description">
            <div class="list-group-item-heading">{{ item.name }}</div>
            <div class="list-group-item-text">{{ item.Description }}</div>
          </div>
          
          
          
          <!-- <div class="list-view-pf-additional-info rules-info">
            <div class="list-view-pf-additional-info-item">
              <a
                tabindex="0"
                role="button"
                data-toggle="popover"
                data-html="true"
                data-placement="top"
                :title="$t('virtualhost.status')"
                :id="'popover-'+item.name | sanitize"
                @click="checkStatus(item)"
              >{{$t('virtualhost.check_status')}}</a>
            </div>
            <div v-if="item.OpenDkimStatus == 'enabled'" class="list-view-pf-additional-info-item">
              <span class="fa fa-key"></span>
              <strong>DKIM</strong>
              <span
                :class="['fa', item.OpenDkimStatus == 'enabled' ? 'fa-check green' : 'fa-times red']"
              ></span>
            </div>
            <div
              v-if="item.DisclaimerStatus == 'enabled'"
              class="list-view-pf-additional-info-item"
            >
              <span class="fa fa-list"></span>
              <strong>{{$t('virtualhost.disclaimers')}}</strong>
              <span
                :class="['fa', item.DisclaimerStatus == 'enabled' ? 'fa-check green' : 'fa-times red']"
              ></span>
            </div>
            <div
              v-if="item.AlwaysBccAddress && item.AlwaysBccAddress.length > 0"
              class="list-view-pf-additional-info-item"
            >
              <span class="fa fa-share"></span>
              <strong>{{$t('virtualhost.always_bcc')}}:</strong>
              <span class="span-left-margin">{{item.AlwaysBccAddress}}</span>
            </div>
          </div> -->
          
          
          
        </div>
      </div>
    </div>
    <!-- end item -->
  </div>
  <!-- end list -->
</template>

<script>
export default {
  name: "VhostsListView",
  props: {
    items: Array
  },
  data() {
    return {};
  },
  methods: {
      
      toggleLock(item) {

         var context = this;
        nethserver.notifications.success = context.$t("virtualhost.virtualhost_" +
            (item.status == 'enabled' ? "disabled" : "enabled") +
            "_ok"
        );
        nethserver.notifications.error = context.$t("virtualhost.virtualhost_" +
            (item.status == 'enabled' ? "disabled" : "enabled") +
            "_Failed"
        );
          nethserver.exec(
            ["nethserver-httpd/update"],
            {
              action: "toggle-lock",
              virtualhost:{"name":item.name}
            },
            function(stream) {
              console.info("vhost-toggle-lock", stream);
            },
            function(success) {
                //update the value of button
                item.status === 'disabled' ? item.status = 'enabled' : item.status = 'disabled';
            },
            function(error, data) {
                console.error(error, data);
            }
          );
        },
    // smartIcon: function(item) {
    //   if (item.TransportType == "LocalDelivery") {
    //     return item.isPrimaryDomain ? "fa-inbox" : "fa-at";
    //   } else {
    //     return "fa-paper-plane-o";
    //   }
    // },
    // smartDescription: function(item) {
    //   var parts = [];
    //   if (item.Description) {
    //     parts.push(item.Description);
    //   }
    //   if (item.OpenDkimStatus == "enabled") {
    //     parts.push(this.$t("virtualhost.item_dkim"));
    //   }
    //   if (item.DisclaimerStatus == "enabled") {
    //     parts.push(this.$t("virtualhost.item_disclaimer"));
    //   }
    //   if (item.AlwaysBccStatus == "enabled") {
    //     parts.push(this.$t("virtualhost.item_bcc", item));
    //   }
    //   if (item.UnknownRecipientsActionType == "deliver") {
    //     parts.push(this.$t("virtualhost.item_unknown_recipients"));
    //   }
    //   return parts.join(", ");
    // },
    // checkStatus(domain) {
    //   console.log(domain);
    //   var popover = $(
    //     "#" + this.$options.filters.sanitize("popover-" + domain.name)
    //   ).data("bs.popover");
    // 
    //   if (!domain.status && popover) {
    //     popover.options.content = '<div class="spinner spinner-sm"></div>';
    //     popover.show();

    //     var context = this;
    //     nethserver.exec(
    //       ["nethserver-mail/domains/read"],
    //       { action: "network-checks", domain: domain.name },
    //       null,
    //       function(success) {
    //         try {
    //           success = JSON.parse(success);
    //         } catch (e) {
    //           console.error(e);
    //         }
    // 
    //         var text = "";
    //         text +=
    //           '<div class="row"><b class="col-sm-4">' +
    //           context.$i18n.t("domains.port_25") +
    //           "</b>" +
    //           (success["port-25"].response == "ok"
    //             ? '<span class="fa fa-check green"></span>'
    //             : '<span class="fa fa-times red"></span>') +
    //           '<span class="gray">(' +
    //           context.$i18n.t('domain.'+success["port-25"].response) +
    //           ")</span>" +
    //           "</div>";
    //         text +=
    //           '<div class="row"><b class="col-sm-4">' +
    //           context.$i18n.t("domains.dkim") +
    //           "</b>" +
    //           (success["dkim-record"].response == "ok"
    //             ? '<span class="fa fa-check green"></span>'
    //             : '<span class="fa fa-times red"></span>') +
    //           '<span class="gray">(' +
    //           context.$i18n.t('domain.'+success["dkim-record"].response) +
    //           ")</span>" +
    //           "</div>";
    //         text +=
    //           '<div class="row"><b class="col-sm-4">' +
    //           context.$i18n.t("domains.mx_record") +
    //           "</b>" +
    //           (success["mx-record"].response == "ok"
    //             ? '<span class="fa fa-check green"></span>'
    //             : '<span class="fa fa-times red"></span>') +
    //           '<span class="gray">(' +
    //           context.$i18n.t('domain.'+success["mx-record"].response) +
    //           ")</span>" +
    //           "</div>";
    //         text +=
    //           '<div class="row"><b class="col-sm-4">' +
    //           context.$i18n.t("domains.ip_reverse") +
    //           "</b>" +
    //           (success["iprev-check"].response == "ok"
    //             ? '<span class="fa fa-check green"></span>'
    //             : '<span class="fa fa-times red"></span>') +
    //           '<span class="gray">(' +
    //           context.$i18n.t('domain.'+success["iprev-check"].response) +
    //           ")</span>" +
    //           "</div>";
    // 
    //         popover.options.content = text;
    // 
    //         domain.status = true;
    //         popover.show();
    //       },
    //       function(error) {
    //         console.error(error);
    //       }
    //     );
    //   }
     //}
   }
 };
</script>

<style scoped>
.list-group-item-heading {
  width: calc(60% - 20px) !important;
}
.list-group-item-text {
  width: calc(40% - 40px) !important;
}
</style>
