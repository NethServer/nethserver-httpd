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
    <div v-bind:id="id" data-backdrop="static" class="modal modal-vhost-edit" tabindex="-1" role="dialog" v-bind:aria-labelledby="id + 'Label'" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" aria-label="Close">
                        <span class="pficon pficon-close"></span>
                    </button>
                    <h4 class="modal-title" v-bind:id="id + 'Label'">
                        <span v-if="useCase == 'delete'"      >{{ $t('proxypass.delete_title', this.proxypass) }}</span>
                        <span v-else-if="useCase == 'create'" >{{ $t('proxypass.create_title', this.proxypass) }}</span>
                        <span v-else                          >{{ $t('proxypass.edit_title', this.proxypass) }}</span>
                    </h4>
                </div>

                <div v-if="useCase == 'delete'" class="modal-body">
                    <div class="alert alert-warning alert-dismissable">
                        <span class="pficon pficon-warning-triangle-o"></span>
                        <strong>{{$t('warning')}}. </strong>
                        <i18n path="proxypass.delete_confirm_message" tag="span">
                            <b>{{ this.proxypass.name }}</b>
                        </i18n>
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
                    <form class="form-horizontal">
                        <div v-bind:class="['form-group', vErrors.name ? 'has-error' : '']">
                            <label class="col-sm-3 control-label" v-bind:for="id + '-ni'">{{ $t('proxypass.name') }}</label>
                            <div class="col-sm-9">
                                <input :disabled="useCase != 'create'" :placeholder="$t('proxypass.name_help')" type="text" v-model="name" v-bind:id="id + '-ni'" class="form-control">
                                <span v-if="vErrors.name" class="help-block">{{ vErrors.name }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" v-bind:for="id + '-di'">{{ $t('proxypass.description_label') }}</label>
                            <div class="col-sm-9">
                                <input type="text" v-model="Description" v-bind:id="id + '-di'" class="form-control">
                            </div>
                        </div>
                        <!-- ValidFrom -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label"  v-bind:for="id + '-di'">{{ $t('proxypass.ValidFromCIDR') }}</label>
                            <div class="col-sm-9">
                                <input type="text" v-model="ValidFrom" :placeholder="$t('proxypass.CIDR_Comma_Sperated_List')" v-bind:id="id + '-ValidFrom'" class="form-control">
                                <span v-if="vErrors.ValidFrom" class="help-block">{{ vErrors.ValidFrom }}</span>
                            </div>
                        </div>
                        
                        
                        <!-- SSL forced -->

                        <div
                        v-bind:class="['form-group', vErrors.HTTP ? 'has-error' : '']"
                        >
                        <label
                        class="col-sm-3 control-label"
                        v-bind:for="id + '-HTTP'"
                        >{{$t('proxypass.ForceHTTPS')}}
                        </label>
                        <div class="col-sm-9">
                            <input type="checkbox"  true-value="no" false-value="yes" v-model="HTTP" class="form-control">
                            <span v-if="vErrors.HTTP" class="help-block">{{ vErrors.HTTP }}</span>
                        </div>
                        </div>
                        
                        <!-- Target -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label" v-bind:for="id + '-Target'">{{ $t('proxypass.Target') }}</label>
                            <div class="col-sm-9">
                                <input type="text" v-model="Target" v-bind:id="id + '-Target'" class="form-control">
                                <span v-if="vErrors.Target" class="help-block">{{ vErrors.Target }}</span>
                            </div>
                        </div>
                        
                    </form>
                </div>
                <div class="modal-footer">
                    <div v-if="loader" class="spinner spinner-sm form-spinner-loader"></div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ $t('modal.cancel_button') }}</button>
                    <button v-if="useCase == 'delete'" v-on:click="$emit('modal-save')" type="button" class="btn btn-danger" >{{ $t('delete') }}</button>
                    <button v-else-if="useCase == 'create'" v-on:click="$emit('modal-save')" type="button" class="btn btn-primary">{{ $t('save') }}</button>
                    <button v-else v-on:click="$emit('modal-save')" type="button" class="btn btn-primary">{{ $t('edit') }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import execp from '@/execp'

var attrs = [
    'name',
    'Description',
    'Target',
    'HTTP',
    "HTTPS",
    "ValidFrom"
];

export default {
    name: "ModalVhostEdit",
    props: {
        'id': String,
        'useCase': String,
        'proxypass': Object
    },
    watch: {
        proxypass: function(newval) {
            this.vErrors = {}
            for(let i in attrs) {
                this[attrs[i]] = newval[attrs[i]] || "";
            }
        },
    },
    data() {
        var obj = {
            vErrors: {},
            loader: false
        }
        for(let i in attrs) {
            obj[attrs[i]] = ""
        }
        return obj
    },
    mounted: function() {
        this.$on('modal-save', (eventData) => {
            this.loader = true
            var inputData = {
                action: this.$props['useCase'],
                proxypass: {}
            }
            for(let i in attrs) {
                inputData.proxypass[attrs[i]] = this[attrs[i]]
            }
            this.vErrors = {}
            execp("nethserver-httpd/proxypass/validate", inputData)
            .catch((validationError) => {
                let err = {}
                for(let i in validationError.attributes) {
                    let attr = validationError.attributes[i]
                    err[attr.parameter] = this.$t('validation.' + attr.error)
                }
                this.vErrors = err
                this.loader = false
                return Promise.reject(validationError) // still unresolved
            })
            .then((validationResult) => {
                this.loader = false
                window.jQuery(this.$el).modal('hide') // on successful resolution close the dialog

                nethserver.notifications.success = this.$t(
                    "proxypass.ProxypassPath_" +
                    (this.useCase == 'create' ? "created" : "updated") +
                    "_ok"
                );
                nethserver.notifications.error = this.$t(
                    "proxypass.ProxypassPath_" +
                    (this.useCase == 'create' ? "created" : "updated") +
                    "_error"
                );

                return execp("nethserver-httpd/proxypass/update", inputData, true) // start another async call
            })
            .finally(() => {
                // stop the spinner
                this.loader = false
            })
            .then(() => {
                this.$emit('modal-close', eventData)
            })
        })
    },
    methods: {
    },
}
</script>
