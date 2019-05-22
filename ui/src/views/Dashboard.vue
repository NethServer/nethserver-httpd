<template>
    <div>
        <h2>{{$t('dashboard.title')}}</h2>
        <div v-if=" !view.statsLoaded" class="spinner spinner-lg"></div>
        <div v-if="view.statsLoaded">
            <div class="row row-eq-height row-stat divider row-status container-fluid">
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h3 class="panel-title">
                        {{ $t('dashboard.apache_worker_status') }}
                      </h3>
                    </div>
                    <div class="panel-body">
                        <span v-if="Object.keys(status.statistics).length == 0" class="empty-piechart">
                            <span class="fa fa-pie-chart"></span>
                            <div>{{ $t('dashboard.empty_piechart_label') }}</div>
                        </span>
                        <span v-else id="apache-pie-chart"></span>
                    </div>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                      <div class="panel panel-default">
                        <div class="panel-heading">
                          <h3 class="panel-title">
                            {{ $t('dashboard.apache_stats') }}
                          </h3>
                        </div>
                        <div class="panel-body">
                            <span class="card-pf-utilization-card-details-count stats-description-small col-xs-6">{{$t('dashboard.apache_uptime')}}: </span>
                            <span
                              class="card-pf-utilization-card-details-description stats-description-small col-xs-6"
                            >
                              <span
                                class="card-pf-utilization-card-details-line-2 stats-text-small"
                              >{{status.Uptime}}</span>
                            </span>
                            
                            <span class="card-pf-utilization-card-details-count stats-description-small col-xs-6">{{$t('dashboard.TotalAccess')}}: </span>
                            <span
                              class="card-pf-utilization-card-details-description stats-description-small col-xs-6"
                            >
                              <span
                                class="card-pf-utilization-card-details-line-2 stats-text-small"
                              >{{status.TotalAccess}}</span>
                            </span>

                            <span class="card-pf-utilization-card-details-count stats-description-small col-xs-6">{{$t('dashboard.Total_kbytes')}}: </span>
                            <span
                              class="card-pf-utilization-card-details-description stats-description-small col-xs-6"
                            >
                              <span
                                class="card-pf-utilization-card-details-line-2 stats-text-small"
                              >{{status.Total_kbytes}}</span>
                            </span>

                            <span class="card-pf-utilization-card-details-count stats-description-small col-xs-6">{{$t('dashboard.ReqPerSec')}}: </span>
                            <span
                              class="card-pf-utilization-card-details-description stats-description-small col-xs-6"
                            >
                              <span
                                class="card-pf-utilization-card-details-line-2 stats-text-small"
                              >{{status.ReqPerSec}}</span>
                            </span>

                            <span class="card-pf-utilization-card-details-count stats-description-small col-xs-6">{{$t('dashboard.BytesPerSec')}}: </span>
                            <span
                              class="card-pf-utilization-card-details-description stats-description-small col-xs-6"
                            >
                              <span
                                class="card-pf-utilization-card-details-line-2 stats-text-small"
                              >{{status.BytesPerSec}}</span>
                            </span>

                            <span class="card-pf-utilization-card-details-count stats-description-small col-xs-6">{{$t('dashboard.BytesPerReq')}}: </span>
                            <span
                              class="card-pf-utilization-card-details-description stats-description-small col-xs-6"
                            >
                              <span
                                class="card-pf-utilization-card-details-line-2 stats-text-small"
                              >{{status.BytesPerReq}}</span>
                            </span>
                    </div>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h3 class="panel-title">
                        {{ $t('dashboard.informations') }}
                      </h3>
                    </div>
                    <div class="panel-body">
                        <span v-if="live.packages.virtualhost" class="card-pf-utilization-card-details-count stats-description-small col-xs-6">{{$t('dashboard.number_virtualhosts')}}: </span>
                        <span v-if="live.packages.virtualhost"
                          class="card-pf-utilization-card-details-description stats-description-small col-xs-6"
                        >
                          <span
                            class="card-pf-utilization-card-details-line-2 stats-text-small"
                          >{{live.statistics.virtualhosts}}</span>
                        </span>
                        
                        
                        <span class="card-pf-utilization-card-details-count stats-description-small col-xs-6">{{$t('dashboard.number_VhostReverse')}}: </span>
                        <span
                          class="card-pf-utilization-card-details-description stats-description-small col-xs-6"
                        >
                          <span
                            class="card-pf-utilization-card-details-line-2 stats-text-small"
                          >{{live.statistics.VhostReverse}}</span>
                        </span>
                        
                        <span class="card-pf-utilization-card-details-count stats-description-small col-xs-6">{{$t('dashboard.number_ProxyPass')}}: </span>
                        <span
                          class="card-pf-utilization-card-details-description stats-description-small col-xs-6"
                        >
                          <span
                            class="card-pf-utilization-card-details-line-2 stats-text-small"
                          >{{live.statistics.ProxyPass}}</span>
                        </span>
                    </div>
                  </div>
                </div>
            </div><!--  end first panel -->

            <div class="row row-eq-height row-stat divider row-status container-fluid">
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h3 class="panel-title">
                        {{ $t('dashboard.default_rpm_version') }}
                      </h3>
                    </div>
                    <div class="panel-body">
                        <span v-for="(item, key) in live.versions.default">
                          <span
                            class="card-pf-utilization-card-details-count stats-description-small col-xs-6"
                          >{{key}}</span>
                          <span
                            class="card-pf-utilization-card-details-description stats-description-small col-xs-6"
                          >
                            <span
                              class="card-pf-utilization-card-details-line-2 stats-text-small"
                            >{{ item }}</span>
                            </span>
                        </span>
                    </div>

                  </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h3 class="panel-title">
                        {{ $t('dashboard.PHP_scl') }}
                      </h3>
                    </div>
                    <div class="panel-body">
                        <span v-for="(item, key) in live.versions.php_SCL">
                          <span
                            class="card-pf-utilization-card-details-count stats-description-small col-xs-6"
                          >{{key}}</span>
                          <span
                            class="card-pf-utilization-card-details-description stats-description-small col-xs-6"
                          >
                            <span
                              class="card-pf-utilization-card-details-line-2 stats-text-small"
                            >{{ item }}</span>
                            </span>
                        </span>
                    </div>
                  </div>
                </div>
            
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h3 class="panel-title">
                        {{ $t('dashboard.databases_scl') }}
                      </h3>
                    </div>
                    <div class="panel-body">
                        <span v-for="(item, key) in live.versions.database_SCL">
                          <span
                            class="card-pf-utilization-card-details-count stats-description-small col-xs-6"
                          >{{key}}</span>
                          <span
                            class="card-pf-utilization-card-details-description stats-description-small col-xs-6"
                          >
                            <span
                              class="card-pf-utilization-card-details-line-2 stats-text-small"
                            >{{ item }}</span>
                            </span>
                        </span>
                    </div>
                  </div>
                </div>
            </div><!--  end second panel -->
        </div>
    </div>
</template>

<script>
import generatePieChart from "@/piechart";
export default {
  name: "Dashboard",
  mounted() {
    this.getLive();
    this.getApacheStatus();
},
    updated() {
    var $ = window.jQuery;
    $('[data-toggle="tooltip"]').tooltip();
    if (!this.apachePieChart) {
      this.apachePieChart = generatePieChart("#apache-pie-chart", {
          names: {
              AvailableWorkers: this.$t("dashboard.AvailableWorkers"),
              IdleWorkers: this.$t("dashboard.IdleWorkers"),
              LoggingWorkers: this.$t("dashboard.LoggingWorkers"),
              BusyWorkers: this.$t("dashboard.BusyWorkers"),
              StartingWorkers: this.$t("dashboard.StartingWorkers"),
              ReplyingWorkers: this.$t("dashboard.ReplyingWorkers"),
              KeepAliveWorkers: this.$t("dashboard.KeepAliveWorkers"),
              GraceFullyFinishingWorkers: this.$t("dashboard.GraceFullyFinishingWorkers"),
              DnsLookupWorkers: this.$t("dashboard.DnsLookupWorkers"),
              ClosingWorkers: this.$t("dashboard.ClosingWorkers"),
              IdleCleanupWorkers: this.$t("dashboard.IdleCleanupWorkers"),
              ReadingWorkers: this.$t("dashboard.ReadingWorkers")
          },
          colors: {
              AvailableWorkers: $.pfPaletteColors.green,
              IdleWorkers: $.pfPaletteColors.green,
              LoggingWorkers: $.pfPaletteColors.blue,
              BusyWorkers: $.pfPaletteColors.red,
              StartingWorkers: $.pfPaletteColors.red,
              ReplyingWorkers: $.pfPaletteColors.red,
              KeepAliveWorkers: $.pfPaletteColors.orange,
              GraceFullyFinishingWorkers: $.pfPaletteColors.orange,
              DnsLookupWorkers: $.pfPaletteColors.blue,
              ClosingWorkers: $.pfPaletteColors.orange,
              IdleCleanupWorkers: $.pfPaletteColors.orange,
              ReadingWorkers: $.pfPaletteColors.red
        },
         columns: []
       });
    }
    this.apachePieChart.load({
   json: this.status.statistics
   });
  },
  data() {
    return {
      view: {
        isLoaded: false,
        statsLoaded: false
      },
      live: {
          packages:{},
          statistics: {},
          services:{},
          versions:{}
      },
      status:{},
    };
  },
  methods: {
      getLive() {
        var context = this;
      
        context.view.statsLoaded = false;
        nethserver.exec(
          ["nethserver-httpd/dashboard/read"],
          {
            action: "live"
          },
          null,
          function(success) {
            try {
              success = JSON.parse(success);
            } catch (e) {
              console.error(e);
            }
            context.live = success;
                      context.services =
            success.services && Object.keys(success.services).length > 0
              ? success.services
              : false;
                      context.versions =
            success.versions && Object.keys(success.versions).length > 0
              ? success.versions
              : false;

             context.view.statsLoaded = true;
          },
          function(error) {
            console.error(error);
          },
          true //sudo
        );
    },
      getApacheStatus() {
        var context = this;
      
        context.view.isLoaded = false;
        nethserver.exec(
          ["nethserver-httpd/dashboard/read"],
          {
            action: "apacheStatus"
          },
          null,
          function(success) {
            try {
              success = JSON.parse(success);
            } catch (e) {
              console.error(e);
            }
            context.status = success;
            context.status.statistics = success.statistics;

            if (Number(success.Uptime) < 3600) {
                context.status.Uptime = parseFloat((Number(success.Uptime) / 60).toFixed(0)) + ' ' + context.$t("dashboard.minute");
            } else if (Number(success.Uptime) < 86400) {
                context.status.Uptime = parseFloat((Number(success.Uptime) / 3600).toFixed(0)) + ' ' + context.$t("dashboard.hour");
            } else if (success.Uptime > 86400) {
                context.status.Uptime = parseFloat((Number(success.Uptime) / 86400).toFixed(0)) + ' ' + context.$t("dashboard.day");
            }

            if (Number(success.Total_kbytes) < 1000) {
                context.status.Total_kbytes = parseFloat((Number(success.Total_kbytes) / 1000).toFixed(2)) + ' ' + context.$t("dashboard.MB");
            } else if (Number(success.Total_kbytes) < 1000000) {
                context.status.Total_kbytes = parseFloat((Number(success.Total_kbytes) / 1000000).toFixed(2)) + ' ' + context.$t("dashboard.GB");
            } else if (success.Total_kbytes > 1000000000) {
                context.status.Total_kbytes = parseFloat((Number(success.Total_kbytes) / 1000000000).toFixed(2)) + ' ' + context.$t("dashboard.TB");
            }

            context.view.isLoaded = true;
          },
          function(error) {
            console.error(error);
          },
          true //sudo
        );
      }
  },
};
</script>

<style>
.empty-piechart {
  margin-top: 2em;
  text-align: center;
  color: #9c9c9c;
}
.empty-piechart .fa {
  font-size: 92px;
  color: #bbbbbb;
}

.divider {
    border-top: 1px solid #d1d1d1;
}

/* lamp-status */
.row-status {
  margin-left: -5px;
  margin-right: 0px;
}

/* panel */

.stats-description-small {
  float: left;
  overflow: hidden;
  text-overflow: ellipsis;
  font-size: 16px;
  font-weight: 300;
  line-height: 2;
}

.stats-text-small {
  line-height: 0.5;
}
.panel-body {
  flex-grow: 1;
}
.row-eq-height {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  flex-flow: row wrap;
}
.row-eq-height > div {
  margin: 20px 0;
}
.row-eq-height .panel {
  min-height: 100%;
  display: flex;
  flex-direction: column;
}
.panel-body {
  flex-grow: 1;
}
</style>

