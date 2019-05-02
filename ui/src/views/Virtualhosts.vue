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
    <h1>{{ $t('virtualhost.title') }}</h1>
    <doc-info
      :placement="'top'"
      :title="$t('docs.virtualhost')"
      :chapter="'virtual_hosts'"
      :section="''"
      :inline="false"
      :lang="'en'"
    ></doc-info>

 <div v-if="vReadStatus == 'running'" class="spinner spinner-lg view-spinner"></div>
        <div v-else-if="vReadStatus == 'error'  && view.menu.installed">
      <div class="alert alert-danger">
        <span class="pficon pficon-error-circle-o"></span>
        <strong>OOOPS!</strong> An unexpected error has occurred:
        <pre>{{ vReadError }}</pre>
      </div>
        </div>

    <!-- <div v-show="!view.isLoaded" class="spinner spinner-lg"></div> -->
     <div v-if="!view.menu.installed && view.isLoaded">
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
    
   

    <div v-else-if="view.menu.installed && view.isLoaded">
        
    <div  class="spaced"> 
        
      <h3>{{$t('actions')}}</h3>
      <button
        class="btn btn-primary btn-lg"
        v-on:click="openModal('modalCreateVhost', createDefaultVhost())"
      >{{ $t('virtualhost.create_virtualhost_button') }}</button>

      <h3>{{$t('list')}}</h3>
      <vhosts-list-view
        v-bind:items="virtualhost"
        v-bind:certificates="certificates"
        v-on:modal-close="read"
        v-on:item-edit="openModal('modalEditVhost', $event)"
        v-on:item-delete="openModal('modalDeleteVhost', $event)"
        v-on:item-dkim="openModal('modalEditDkim', $event)"
      ></vhosts-list-view>
    </div>

<modal-vhost-edit
      id="modalCreateVhost"
      v-on:modal-close="read($event)"
      use-case="create"
      v-bind:virtualhost="currentItem"
              v-bind:certificates="certificates"

    ></modal-vhost-edit>
    
<modal-vhost-edit
      id="modalEditVhost"
      v-on:modal-close="read"
      use-case="edit"
      v-bind:virtualhost="currentItem"
              v-bind:certificates="certificates"
    ></modal-vhost-edit>
    <modal-vhost-edit
      id="modalDeleteVhost"
      v-on:modal-close="read"
      use-case="delete"
      v-bind:virtualhost="currentItem"
    ></modal-vhost-edit> 
    
    
    
    <!-- <modal-dkim-edit
      id="modalEditDkim"
      v-on:modal-close="read"
      v-bind:domain="currentItem"
      v-bind:dkimRawData="dkimRawData"
      v-bind:dkimTxtRecord="dkimTxtRecord"
    ></modal-dkim-edit> -->
  </div>
</div>
</template>

<script>
import execp from "@/execp";
import VhostsListView from "@/components/VhostsListView.vue";
import ModalVhostEdit from "@/components/ModalVhostEdit.vue";
// import ModalDkimEdit from "@/components/ModalDkimEdit.vue";

export default {
  name: "Virtualhosts",
  components: {
    VhostsListView,
    ModalVhostEdit,
    // ModalDkimEdit
  },
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
          vm.view.isLoaded = true;
        },
        function(error) {
          console.error(error);
        },
        true // sudo
      );
    });
  },
  mounted() {
      this.read();
  },
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    next();
  },
  data() {
    return {
    view: {
        isLoaded: false,
        isInstalling: false,
        menu: {
            installed: false,
            packages: []
        }
    },
      vReadStatus: "running",
      virtualhost: [],
      certificates:[],
      // isDisclaimerAvailable: false,
      // isServerAvailable: false,
      currentItem: {},
      // dkimRawData: "",
      // dkimTxtRecord: "",
      // defaultRecipientMailbox: {}
    };
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
           window.location.reload();
          // document.location.reload(true);
        },
        function(error) {
          console.error(error);
        }
      );
    },
    createDefaultVhost() {
      return {
        // unknownRecipientMailbox: this.defaultRecipientMailbox,
        name: "",
        Access: "private",
        PasswordStatus:"disabled",
        FtpPassword:"",
        ForceSslStatus:"disabled",
        Indexes:"disabled",
        FtpStatus:"enabled",
        FtpPassword:"",
        certificates: "",
        // isPrimaryDomain: false,
        // TransportType: this.isServerAvailable ? "LocalDelivery" : "Relay",
        // AlwaysBccStatus: "disabled",
        // DisclaimerStatus: "disabled",
        // OpenDkimStatus: "disabled",
        // AlwaysBccAddress: "",
        // UnknownRecipientsActionType: "bounce",
        Description: "",
        status: "enabled",
        CreateHostRecords:"1"
      };
    },
    openModal(id, item) {
      this.currentItem = item;
      window.jQuery("#" + id).modal();
    },
    read(eventData = {}) {
      this.vReadStatus = "running";
      execp("nethserver-httpd/virtualhost/read", {"action":"virtualhost"})
        .then(result => {
          for (let k in result) {
            if (result.hasOwnProperty(k)) {
              this[k] = result[k];
            }
          }
          this.vReadStatus = "success";

          // setTimeout(function() {
          //   $("[data-toggle=popover]")
          //     .popovers()
          //     .on("hidden.bs.popover", function(e) {
          //       $(e.target).data("bs.popover").inState.click = false;
          //     });
          // }, 250);
        })
        .catch(error => {
          this.vReadStatus = "error";
          this.vReadError = error;
        })
        // .then(() => {
        //   if (eventData.nextAction == "open-dkim-modal") {
        //     for (let i in this.domains) {
        //       if (this.domains[i].name == eventData.id) {
        //         this.openModal("modalEditDkim", this.domains[i]);
        //       }
        //     }
        //   }
        // });
    }
  }
};
</script>
