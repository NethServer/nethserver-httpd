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
    <div v-bind:key="item.name" v-for="item in items" class="list-group-item">
      <div class="list-view-pf-actions">
        <button
          v-if="item.status === 'enabled' || item.name === 'default'"
          class="btn btn-default"
          v-on:click="$emit('item-edit', item)"
        >
          <span class="fa fa-pencil"></span>
          {{ $t('virtualhost.item_edit_button')}}
        </button>
        <button
          v-if="item.status === 'disabled'"
          class="btn btn-default btn-primary"
          v-on:click="toggleLock(item)"
        >
          <span class="fa fa-check"></span>
          {{ $t('virtualhost.item_enable_button')}}
        </button>
        <div class="dropup pull-right dropdown-kebab-pf">
          <button
            class="btn btn-link dropdown-toggle"
            type="button"
            v-bind:id="item.name + '-ddm'"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            :disabled="item.name === 'default'"
          >
            <span class="fa fa-ellipsis-v"></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-right" v-bind:aria-labelledby="item.name + '-ddm'">
            <li>
              <a @click="toggleLock(item)">
                <span
                  :class="[item.status === 'disabled' ? 'fa fa-check' : 'pficon pficon-locked','span-right-margin']"
                ></span>
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
            :class="[item.status === 'disabled' ? 'gray':'','fa list-view-pf-icon-sm fa-folder-open']"
          ></span>
        </div>
        <div class="list-view-pf-body">
          <div class="list-view-pf-description">
            <span
              :class="[item.status === 'disabled' ? 'pficon pficon-locked gray':'','span-right-margin']"
            ></span>
            <div class="list-group-item-heading">
              <div
                v-for="(value,index) in item.ServerNames"
                :key="index"
                :class="{'big-name' : index === 0}"
              >
                <span :class="[item.status === 'disabled' ? 'gray':'']">{{value}}</span>
              </div>
            </div>
            <div class="list-group-item-text">
              <a  v-if="item.name !== 'default'"
                tabindex="0"
                href="#"
                @click="getWebRoot(item)"
                :id="'popover-'+item.name | sanitize"
                class="alert-link"
                data-placement="top"
                data-toggle="popover"
                data-html="true"
                :title="$t('virtualhost.path_copied')"
                data-content
                v-clipboard:copy="'/var/lib/nethserver/vhost/'+item.name+'/'"
              >
                <span class="pficon pficon-folder-close"></span>
              </a>
              <a v-else
                tabindex="0"
                href="#"
                @click="getWebRoot(item)"
                :id="'popover-'+item.name | sanitize"
                class="alert-link"
                data-placement="top"
                data-toggle="popover"
                data-html="true"
                :title="$t('virtualhost.path_copied')"
                data-content
                v-clipboard:copy="'/var/www/html/'"
              >
                <span class="pficon pficon-folder-close"></span>
              </a>
              <span :class="[item.status === 'disabled' ? 'gray span-left-margin':'span-left-margin']">{{ item.Description }}</span>
            </div>
          <div class="list-view-pf-additional-info rules-info">
            <div
              class="list-view-pf-additional-info-item"
              v-if="(item.PasswordStatus === 'enabled' && item.status === 'enabled')"
            >
              <span class="span-left-margin fa fa-check green"></span>
              <strong>{{$t('virtualhost.http')}}</strong>
            </div>
            <div class="list-view-pf-additional-info-item" v-if="item.FtpStatus === 'enabled'">
              <span class="span-left-margin fa fa-check green"></span>
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
      nethserver.notifications.success = context.$t(
        "virtualhost.virtualhost_" +
          (item.status == "enabled" ? "disabled" : "enabled") +
          "_ok"
      );
      nethserver.notifications.error = context.$t(
        "virtualhost.virtualhost_" +
          (item.status == "enabled" ? "disabled" : "enabled") +
          "_Failed"
      );
      nethserver.exec(
        ["nethserver-httpd/virtualhost/update"],
        {
          action: "toggle-lock",
          virtualhost: { name: item.name }
        },
        function(stream) {
          console.info("vhost-toggle-lock", stream);
        },
        function(success) {
          //update the value of button
          item.status === "disabled"
            ? (item.status = "enabled")
            : (item.status = "disabled");
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    getWebRoot(item) {

      var popover = $(
        "#" + this.$options.filters.sanitize("popover-" + item.name)
      ).data("bs.popover");

      //close others popover except this
      $('[data-toggle=popover]').on('click', function (e) {
          $('[data-toggle=popover]').not(this).popover('hide');
      });
      
      var path = '/var/lib/nethserver/vhost/'+item.name+'/';
      var text = "";

      if (item.name !== 'default') {
        text =
        '<div>' + 
        '<span>'+ path + '</span>'+"</div>";
      } else {
        text =
        '<div>' + 
        '<span>/var/www/html/</span>'+"</div>";
      }
      popover.options.content = text;
      popover.show();
    }
  }
};
</script>

<style>
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
  margin-right: 5px;
}
.span-left-margin {
  margin-left: 5px;
}
</style>
