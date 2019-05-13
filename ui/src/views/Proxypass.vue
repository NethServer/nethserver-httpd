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
      :chapter="'proxy_pass'"
      :section="''"
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

        <ul class="nav nav-tabs nav-tabs-pf">
          <li>
            <a
              @click="initListeners(0)"
              class="nav-link"
              data-toggle="tab"
              href="#paths-tab"
              id="paths-tab-parent"
            >{{$t('proxypass.paths')}}</a>
          </li>
          <li>
            <a
              @click="initListeners(1)"
              class="nav-link"
              data-toggle="tab"
              href="#virtualhosts-tab"
              id="virtualhosts-tab-parent"
            >{{$t('proxypass.virtualhosts')}}</a>
          </li>
        </ul>

        <div class="tab-content">
            <div
            class="tab-pane fade active"
            id="paths-tab"
            role="tabpanel"
            aria-labelledby="paths-tab"
            >
            <div  class="spaced"> 
              <h3>{{$t('actions')}}</h3>
              <button
                class="btn btn-primary btn-lg"
                v-on:click="openModal('modalCreatePath', createDefaultPath())"
              >{{ $t('proxypass.create_Proxypass_button') }}</button>
              <h3 v-if="!proxypass.length">{{$t('proxypass.NoDataToDisplay')}}</h3>
              <h3 v-else>{{$t('list')}}</h3>
              <proxypass-path-list-view
                v-bind:items="proxypass"
                v-on:modal-close="read"
                v-on:item-edit="openModal('modalEditPath', $event)"
                v-on:item-delete="openModal('modalDeletePath', $event)"
              ></proxypass-path-list-view>
            </div>

            <modal-proxypass-path-edit
              id="modalCreatePath"
              v-on:modal-close="read($event)"
              use-case="create"
              v-bind:proxypass="currentItem"
            ></modal-proxypass-path-edit>
            
            <modal-proxypass-path-edit
              id="modalEditPath"
              v-on:modal-close="read"
              use-case="edit"
              v-bind:proxypass="currentItem"
            ></modal-proxypass-path-edit>

            <modal-proxypass-path-edit
              id="modalDeletePath"
              v-on:modal-close="read"
              use-case="delete"
              v-bind:proxypass="currentItem"
            ></modal-proxypass-path-edit> 
            </div>
            <!-- end paths -->

            <div
            class="tab-pane fade active"
            id="virtualhosts-tab"
            role="tabpanel"
            aria-labelledby="virtualhosts-tab"
            >
            <div  class="spaced"> 
              <h3>{{$t('actions')}}</h3>
              <button
                class="btn btn-primary btn-lg"
                v-on:click="openModal('modalCreateVhostReverse', createDefaultVhost())"
              >{{ $t('proxypass.create_Proxypass_button') }}</button>

              <h3 v-if="!vhostreverse.length">{{$t('proxypass.NoDataToDisplay')}}</h3>
              <h3 v-else>{{$t('list')}}</h3>
              <proxypass-vhost-list-view
                v-bind:items="vhostreverse"
                v-bind:certificates="certificates"
                v-on:modal-close="read"
                v-on:item-edit="openModal('modalEditVhostReverse', $event)"
                v-on:item-delete="openModal('modalDeleteVhostReverse', $event)"
              ></proxypass-vhost-list-view>
            </div>

            <modal-proxypass-vhost-edit
              id="modalCreateVhostReverse"
              v-on:modal-close="read($event)"
              use-case="create"
              v-bind:vhostreverse="currentItem"
              v-bind:certificates="certificates"
            ></modal-proxypass-vhost-edit>
            
            <modal-proxypass-vhost-edit
              id="modalEditVhostReverse"
              v-on:modal-close="read"
              use-case="edit"
              v-bind:vhostreverse="currentItem"
              v-bind:certificates="certificates"
            ></modal-proxypass-vhost-edit>

            <modal-proxypass-vhost-edit
              id="modalDeleteVhostReverse"
              v-on:modal-close="read"
              use-case="delete"
              v-bind:vhostreverse="currentItem"
            ></modal-proxypass-vhost-edit> 
            </div><!-- end vhost -->
        </div> <!-- end navbar -->


<!-- </div> -->

</template>

<script>
import execp from "@/execp";
import ProxypassPathListView from "@/components/ProxypassPathListView.vue";
import ModalProxypassPathEdit from "@/components/ModalProxypassPathEdit.vue";
import ProxypassVhostListView from "@/components/ProxypassVhostListView.vue";
import ModalProxypassVhostEdit from "@/components/ModalProxypassVhostEdit.vue";

export default {
  name: "Proxypass",
  components: {
    ProxypassPathListView,
    ModalProxypassPathEdit,
    ProxypassVhostListView,
    ModalProxypassVhostEdit
  },
  mounted() {
      $("#paths-tab-parent").click();
      this.read();
      this.initListeners(0);

  },
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    next();
  },
  data() {
    return {
      vReadStatus: "running",
      vhostreverse:[],
      proxypass:[],
      certificates:[],
      currentItem: {},
    };
  },
  methods: {
    initListeners(index) {
      var context = this;

      setTimeout(function() {
        context.enablePopover();
        $(
          $(".pagination-controls.pull-right>a.page-btn:first-child")[index]
        ).on("click", function() {
          context.enablePopover();
        });
        $($(".pagination-controls.pull-right>a.page-btn:last-child")[index]).on(
          "click",
          function() {
            context.enablePopover();
          }
        );
      }, 500);
    },
    enablePopover() {
      $("[data-toggle=popover]")
        .popovers()
        .on("hidden.bs.popover", function(e) {
          $(e.target).data("bs.popover").inState.click = false;
        });
    },
    createDefaultPath() {
      return {
        name: "",
        HTTP:"yes",
        HTTPS:"yes",
        Target:"",
        ValidFrom:""
      };
    },
    createDefaultVhost() {
      return {
        name: "",
        HTTP:"yes",
        HTTPS:"yes",
        Target:"",
        ValidFrom:"",
        CertVerification:"no",
        PreserveHost: "yes",
        SslCertificate: "",
        CreateHostRecords:"1"
      };
    },
    openModal(id, item) {
      this.currentItem = item;
      window.jQuery("#" + id).modal();
    },
    read(eventData = {}) {
      this.vReadStatus = "running";
      execp("nethserver-httpd/proxypass/read", {"action":"proxypass"})
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
        })
    }
  }
};
</script>
