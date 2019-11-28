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
        <button class="btn btn-default" v-on:click="$emit('item-edit', item)">
          <span class="fa fa-pencil"></span>
          {{ $t('proxypass.item_edit_button')}}
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
              <a href="#" v-on:click="$emit('item-delete', item)">
                <span class="fa fa-times span-right-margin"></span>
                {{ $t('proxypass.item_delete_button') }}
              </a>
            </li>
          </ul>
        </div>
      </div>

      <div class="list-view-pf-main-info small-list">
        <div class="list-view-pf-left">
          <span class="list-view-pf-icon-sm pficon pficon-route"></span>
        </div>
        <div class="list-view-pf-body">
          <div class="list-view-pf-description">
            <div class="list-group-item-heading">
              <strong>{{ item.name }}</strong>
            </div>
            <div class="list-group-item-text">{{ item.Target }}</div>
            <div class="list-group-item-text">
              <div>{{ item.Description }}</div>
            </div>
          </div>
          <div class="list-view-pf-additional-info rules-info">
            <div class="list-view-pf-additional-info-item" v-if="item.HTTP==='no'">
              <span class="span-left-margin fa fa-check green"></span>
              <strong>{{$t('proxypass.HttpdForced')}}</strong>
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
  methods: {}
};
</script>

<style scoped>
.list-group-item-heading {
  width: calc(60% - 20px) !important;
}
.list-group-item-text {
  width: calc(40% - 40px) !important;
}
.big-name {
  font-size: 16px;
}
.green {
  color: #3f9c35;
}
.red {
  color: #cc0000;
}
.span-right-margin {
  margin-right: 4px;
}
.span-left-margin {
  margin-left: 5px;
}
</style>
