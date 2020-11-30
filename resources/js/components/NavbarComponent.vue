<template>
  <div>
    <!-- <v-toolbar flat app>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      <v-toolbar-title>შეკვეთების სამართავი პანელი</v-toolbar-title>
    </v-toolbar>-->

    <v-app-bar app>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
      <v-toolbar-title>CRM</v-toolbar-title>
      <v-spacer></v-spacer>
      <a href="/logout">logout</a>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" app overflow>
      <v-list dense>
        <v-list-item link route :to="'/'" @click.prevent>
          <v-list-item-action>
            <v-icon>mdi-home</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>მომხმარებლები</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item link route :to="'/employees'" @click.prevent>
          <v-list-item-action>
            <v-icon>mdi-account</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>თანამშრომლები</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item link route :to="'/companies'" @click.prevent>
          <v-list-item-action>
            <v-icon>mdi-cart</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>კოპანიები</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <!-- <v-app-bar color="deep-purple accent-4" dark>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
    </v-app-bar>-->
  </div>
</template>


<script>
export default {
  data: () => ({
    drawer: true,
    group: null
  }),
  watch: {
    group() {
      this.drawer = false;
    }
  }
};
</script>