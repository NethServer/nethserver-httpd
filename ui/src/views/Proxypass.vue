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
.spaced {
  margin-top: 1em;
}
</style>

<template>
  <div>
    <h1>{{ $t('proxypass.title') }}</h1>
    <doc-info
      :placement="'top'"
      :title="$t('docs.Reverse_Proxy')"
      :chapter="'web_server'"
      :section="'reverse-proxy'"
      :inline="false"
      :lang="'en'"
    ></doc-info>
    <div v-if="vReadStatus == 'running'" class="spinner spinner-lg view-spinner"></div>
    <div v-else-if="vReadStatus == 'error'">
      <div class="alert alert-danger">
        <span class="pficon pficon-error-circle-o"></span>
        <strong>OOOPS!</strong> An unexpected error has occurred:
        <pre>{{ vReadError }}</pre>
      </div>
    </div>
    <div class="spaced">
      <div v-if="proxypass.length == 0 && vReadStatus === 'success'" class="blank-slate-pf">
        <div class="blank-slate-pf-icon">
          <span class="fa list-view-pf-icon-sm pficon-service"></span>
        </div>
        <h1>{{ $t('proxypass.title') }}</h1>
        <p>{{ $t("proxypass.NoDataToDisplay") }}</p>
        <div class="blank-slate-pf-main-action">
          <button
            class="btn btn-primary btn-lg"
            v-on:click="openModal('modalCreateVhostReverse', createDefaultVhost())"
          >{{ $t('proxypass.create_Proxypass_button') }}</button>
        </div>
      </div>
      <!-- <div v-else> -->
      <div v-else-if="proxypass.length && vReadStatus !== 'running'">
        <h3>{{$t('actions')}}</h3>
        <div class="blank-slate-pf-main-action">
          <button
            class="btn btn-primary btn-lg"
            v-on:click="openModal('modalCreateVhostReverse', createDefaultVhost())"
          >{{ $t('proxypass.create_Proxypass_button') }}</button>
        </div>

        <h3>{{$t('list')}}</h3>

        <proxypass-vhost-list-view
          v-bind:items="proxypass"
          v-bind:certificates="certificates"
          v-on:modal-close="read"
          v-on:item-edit="openModal('modalEditVhostReverse', $event)"
          v-on:item-delete="openModal('modalDeleteVhostReverse', $event)"
        ></proxypass-vhost-list-view>
      </div>
    </div>

    <modal-proxypass-vhost-edit
      id="modalCreateVhostReverse"
      v-on:modal-close="read($event)"
      use-case="create"
      v-bind:proxypass="currentItem"
      v-bind:certificates="certificates"
    ></modal-proxypass-vhost-edit>

    <modal-proxypass-vhost-edit
      id="modalEditVhostReverse"
      v-on:modal-close="read"
      use-case="edit"
      v-bind:proxypass="currentItem"
      v-bind:certificates="certificates"
    ></modal-proxypass-vhost-edit>

    <modal-proxypass-vhost-edit
      id="modalDeleteVhostReverse"
      v-on:modal-close="read"
      use-case="delete"
      v-bind:proxypass="currentItem"
    ></modal-proxypass-vhost-edit>
  </div>
  <!-- end vhost -->
</template>

<script>
import execp from "@/execp";
import ProxypassVhostListView from "@/components/ProxypassVhostListView.vue";
import ModalProxypassVhostEdit from "@/components/ModalProxypassVhostEdit.vue";

export default {
  name: "Proxypass",
  components: {
    ProxypassVhostListView,
    ModalProxypassVhostEdit
  },
  mounted() {
    $("#paths-tab-parent").click();
    this.read();
  },
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    next();
  },
  data() {
    return {
      vReadStatus: "running",
      proxypass: [],
      certificates: [],
      currentItem: {}
    };
  },
  methods: {
    enablePopover() {
      $("[data-toggle=popover]")
        .popovers()
        .on("hidden.bs.popover", function(e) {
          $(e.target).data("bs.popover").inState.click = false;
        });
    },
    createDefaultVhost() {
      return {
        name: "",
        HTTP: "yes",
        HTTPS: "yes",
        Target: "",
        ValidFrom: "",
        CertVerification: "no",
        PreserveHost: "yes",
        SslCertificate: "",
        WebSockets: "disabled"
      };
    },
    openModal(id, item) {
      this.currentItem = item;
      window.jQuery("#" + id).modal();
    },
    read(eventData = {}) {
      this.vReadStatus = "running";
      execp("nethserver-httpd/proxypass/read", { action: "proxypass" })
        .then(result => {
          for (let k in result) {
            if (result.hasOwnProperty(k)) {
              this[k] = result[k];
            }
          }
          this.vReadStatus = "success";
        })
        .catch(error => {
          this.vReadStatus = "error";
          this.vReadError = error;
        });
    }
  }
};
</script>
