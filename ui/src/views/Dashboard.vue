<template>
    <div>
        <h2>{{$t('dashboard.title')}}</h2>
        <div v-if=" !view.statsLoaded" class="spinner spinner-lg"></div>
        <div v-if="view.statsLoaded">
            <!-- <h3 >{{$t('dashboard.WorkerApacheStatus')}}</h3> -->
            <!-- <div v-if="Object.keys(status.statistics).length == 0" class="empty-piechart">
                <span class="fa fa-pie-chart"></span>
                <div>{{ $t('dashboard.empty_piechart_label') }}</div>
            </div>
            <div v-else id="apache-pie-chart">{{$t('dashboard.WorkerApacheStatus')}}</div>
            <div class="divider"></div> -->

            <div class="divider"></div>
            <h3>{{$t('dashboard.informations')}}</h3>
            <div class="row ">
                <h4 v-if="live.packages.virtualhost" class="col-xs-6 col-sm-4 col-md-3 col-lg-2">{{$t('dashboard.number_virtualhosts')}}: {{live.statistics.virtualhosts}}</h4>
                <h4 class="col-xs-6 col-sm-4 col-md-3 col-lg-2" >{{$t('dashboard.number_VhostReverse')}}: {{live.statistics.VhostReverse}}</h4>
                <h4 class="col-xs-6 col-sm-4 col-md-3 col-lg-2" >{{$t('dashboard.number_ProxyPass')}}: {{live.statistics.ProxyPass}}</h4>
            </div>
            <div class="divider"></div>
            <h3>{{$t('dashboard.apache_stats')}}</h3>
            <div class="row ">
                <h4 class="col-xs-6 col-sm-4 col-md-3 col-lg-2">{{$t('dashboard.apache_uptime')}}: {{status.Uptime}}</h4>
                <h4 class="col-xs-6 col-sm-4 col-md-3 col-lg-2" >{{$t('dashboard.TotalAccess')}}: {{status.TotalAccess}}</h4>
                <h4 class="col-xs-6 col-sm-4 col-md-3 col-lg-2" >{{$t('dashboard.Total_kbytes')}}: {{status.Total_kbytes}}</h4>
            <!-- </div>
            <div class="row "> -->
                <h4 class="col-xs-6 col-sm-4 col-md-3 col-lg-2">{{$t('dashboard.ReqPerSec')}}: {{status.ReqPerSec}}</h4>
                <h4 class="col-xs-6 col-sm-4 col-md-3 col-lg-2" >{{$t('dashboard.BytesPerSec')}}: {{status.BytesPerSec}}</h4>
                <h4 class="col-xs-6 col-sm-4 col-md-3 col-lg-2" >{{$t('dashboard.BytesPerReq')}}: {{status.BytesPerReq}}</h4>

                <!-- <h4 >{{$t('dashboard.WorkerApacheStatus')}}</h4> -->
                <span v-if="Object.keys(status.statistics).length == 0" class="empty-piechart">
                    <span class="fa fa-pie-chart"></span>
                    <div>{{ $t('dashboard.empty_piechart_label') }}</div>
                </span>
                <span v-else id="apache-pie-chart"></span>
                
            </div>

            <div class="divider"></div>
        <h3>{{$t('dashboard.LampStatus')}}</h3>
        <div class="row  row-status">
            <div
            v-for="(s,i) in services"
            :key="i"
            v-if="live.packages[i]" class="stats-container col-xs-12 col-sm-4 col-md-3 col-lg-2"
            >
            <span v-if="live.packages[i]"
            :class="['card-pf-utilization-card-details-count stats-count', s ? 'pficon pficon-ok' : 'pficon-error-circle-o']"
            data-toggle="tooltip"
            data-placement="top"
            :title="$t('dashboard.status')+': '+ (s ? $t('enabled') : $t('disabled'))"
            ></span>
            <span v-if="live.packages[i]" class="card-pf-utilization-card-details-description stats-description">
                <span class="card-pf-utilization-card-details-line-2 stats-text">{{i}} {{$t('dashboard.version')}}: {{live.versions[i]}}</span>
            </span>
        </div>
        <div class="stats-container" v-if="!services">{{$t('dashboard.no_info_found')}}</div>
        </div>
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
      configuration:{
          IPList: "",
          JailStatus:"",
          JailEnabled:""
      },
      live: {
          packages:{
              virtualhost:""
          },
          statistics: {
              ProxyPass: "",
              VhostReverse: "",
              virtualhosts: ""
          },
          services:{
              httpd:"",
              mysqld:""
          }
      },
      status:{
          Total_kbytes:"",
          Uptime:"",
          TotalAccess:"",
          BytesPerSec:"",
          BytesPerReq:"",
          ReqPerSec:"",
          BusyWorkers:"",
          CPULoad:"",
          TotalWorkers:"",
          IdleWorkers:"",
          statistics:{
            IdleCleanupWorkers: "",
            ClosingWorkers: "",
            DnsLookupWorkers: "",
            ReadingWorkers: "",
            GraceFullyFinishingWorkers: "",
            KeepAliveWorkers: "",
            ReplyingWorkers: "",
            StartingWorkers: "",
            BusyWorkers: "",
            WaitingWorkers: "",
            LoggingWorkers: "",
            IdleWorkers: ""
          }
      },
      loaders: false,
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
            //context.status.Uptime = sucess.Uptime;
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
.pficon-on-running {
    margin-right: 2px;
}
.divider {
    border-top: 1px solid #d1d1d1;
}
.row-status {
  margin-left: -5px;
  margin-right: 0px;
}
.stats-container {
  padding: 20px !important;
  border-width: initial !important;
  border-style: none !important;
  border-color: initial !important;
  border-image: initial !important;
}
.stats-count {
  font-size: 26px;
  font-weight: 300;
  margin-right: 10px;
  float: left;
  line-height: 1;
}
</style>

