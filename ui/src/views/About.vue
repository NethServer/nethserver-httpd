<template>
  <div>
    <h2>{{$t('about.title')}}</h2>

    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>
    <div v-if="view.isLoaded">
      <img class="logo" src="logo.png">
      <h2>{{info.name}}</h2>
      <h3>{{info.description}}</h3>

      <div class="list-group-item info-item">
        <span class="fa fa-code m-right-sm"></span>
        <strong>{{info.release.version}}</strong>
      </div>

      <div class="list-group-item info-item">
        <span class="fa fa-globe m-right-sm"></span>
        <a
          target="_blank"
          href="https://github.com/NethServer/nethserver-httpd"
        >{{$t('about.website')}}</a>
      </div>
      <div class="list-group-item info-item">
        <span class="fa fa-bug m-right-sm"></span>
        <a
          target="_blank"
          href="https://github.com/NethServer/dev/issues"
        >{{$t('about.bug_tracker')}}</a>
      </div>

      <div class="list-group-item info-item">
        <span class="fa fa-user m-right-sm"></span>
        <span>{{info.author.name}} | {{info.author.email}}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "About",
  mounted() {
    this.getInfo();
  },
  data() {
    return {
      view: {
        isLoaded: false
      },
      info: {}
    };
  },
  methods: {
    getInfo() {
      var context = this;
      nethserver.exec(
        ["system-apps/read"],
        {
          action: "info",
          name: "nethserver-httpd"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
            context.view.isLoaded = true;
            context.info = success;
          } catch (e) {
            console.error(e);
            context.view.isLoaded = true;
          }
        },
        function(error) {
          console.error(error);
        }
      );
    }
  }
};
</script>

<style>
.m-right-sm {
  margin-right: 5px;
}

.info-item {
  font-size: 14px;
}

.logo {
  float: right;
  width: 65px;
  height: 65px;
}
</style>