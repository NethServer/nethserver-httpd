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
                <span :class="[item.status === 'disabled' ? 'pficon pficon-unlocked' : 'pficon pficon-locked','span-right-margin']"></span>
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
            <span :class="[item.status === 'disabled' ? 'pficon pficon-locked':'','span-right-margin']"></span>
            <div class="list-group-item-text">
                <strong :class="[item.status === 'disabled' ? 'gray':'','big-name']">{{ item.Description }}</strong>
            </div>
            <div class="list-group-item-text">
                <strong>
                    {{ item.ServerNames }}
                </strong>
            </div>
            <div class="list-view-pf-additional-info-item">
                <span :class="[(item.PasswordStatus === 'enabled' && item.status === 'enabled') ? 'fa  fa-check green':'fa  fa-times red','span-left-margin']"></span>
            <strong>{{$t('virtualhost.http')}}</strong>
                <span :class="[item.FtpStatus === 'enabled' ? 'fa  fa-check green':'fa  fa-times red','span-left-margin']"></span>
                <strong>{{$t('virtualhost.ftp')}}</strong>
            </div>
          </div>
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
            ["nethserver-httpd/virtualhost/update"],
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
.green {
  color: #3f9c35;
}
.red {
  color: #cc0000;
}
.gray {
  color: #72767b !important;
}
.big-name {
  font-size: 16px;
}
.span-right-margin {
  margin-right: 4px;
}
.span-left-margin {
  margin-left: 5px;
}
</style>
