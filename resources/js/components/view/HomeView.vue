<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="remoteData"
      :options.sync="pagination"
      hide-default-footer
      sort-by="calories"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar flat color="white">
          <v-toolbar-title>მომხმარებლები</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog">
            <template v-slot:activator="{ on }">
              <v-btn color="primary" dark class="mb-2" v-on="on">თანამშრომლის დამატება</v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" sm="6" md="4">
                      <v-text-field v-model="editedItem.name" label="სახელი"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="4">
                      <v-text-field v-model="editedItem.email" label="ელ ფოსტა"></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="4">
                      <v-text-field v-model="editedItem.password" label="პაროლი"></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="close">გაუქმება</v-btn>
                <v-btn color="blue darken-1" text @click="save">შენახვა</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:item.action="{ item }">
        <v-icon small class="mr-2" color="green" @click="editItem(item)">mdi-account-edit</v-icon>
        <v-icon small @click="deleteItem(item)" color="red">mdi-delete-forever</v-icon>
      </template>
      <template v-slot:no-data>
        <v-btn color="primary" @click>Reset</v-btn>
      </template>
    </v-data-table>
    <div class="text-xs-center pt-2">
      <v-pagination
        v-model="pagination.page"
        :length="pagination.length"
        @input="onPageChange"
        total-visible="10"
      ></v-pagination>
    </div>
  </div>
</template>

<script>
import createRemoteFetchMixin from "../../mixins/fetchMixin";
export default {
  data: () => ({
    link: "/api/user",
    dialog: false,
    filters: {},
    x_csrf: "",
    headers: [
      {
        text: "სახელი",
        align: "left",
        sortable: false,
        value: "name"
      },
      { text: "მეილი", value: "email", sortable: false },
      { text: "Actions", value: "action", sortable: false }
    ],
    editedIndex: -1,
    editedItem: {
      name: "",
      email: "",
      password: ""
    },
    defaultItem: {
      name: "",
      email: "",
      password: ""
    }
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1
        ? "თანამშრომლის დამატება"
        : "ინფორმაციის შეცვლა";
    }
  },

  watch: {
    dialog(val) {
      val || this.close();
    }
  },

  created() {
    this.fetchRemoteDataMixin(this.link);
    this.x_csrf = document.head.querySelector(
      'meta[name="csrf-token"]'
    ).content;
  },
  methods: {
    onPageChange() {
      this.filters.page = this.pagination.page;
      this.fetchRemoteDataMixin(this.link, this.filters);
    },
    editItem(item) {
      this.editedIndex = this.remoteData.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },
    deleteItem(item) {
      const index = this.remoteData.indexOf(item);
      console.log(item);
      confirm("ნამდვილად გსურს ანგარიშის წაშლა?") &&
        fetch("/api/user/" + item.id, {
          method: "DELETE",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": this.x_csrf
          }
        })
          .then(async res => {
            console.log(res);
          })
          .catch(err => {
            this.fetchErr = err;
          })
          .finally(() => {
            this.isFetching = false;
          });
    },
    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },
    createUser(data) {
      fetch("/api/user", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-Token": this.x_csrf
        },
        body: JSON.stringify(data)
      })
        .then(async res => {
          console.log(res);
        })
        .catch(err => {
          this.fetchErr = err;
        })
        .finally(() => {
          this.isFetching = false;
        });
    },
    updateUser(data) {
      fetch("/api/user/" + data.id, {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-Token": this.x_csrf
        },
        body: JSON.stringify(data)
      })
        .then(async res => {
          let remoteData = await res.json();
          console.log(remoteData);
          // Object.assign(this.remoteData[this.editedIndex], this.editedItem);
        })
        .catch(err => {
          this.fetchErr = err;
        })
        .finally(() => {
          this.isFetching = false;
        });
    },
    save() {
      if (this.editedIndex > -1) {
        this.updateUser(this.editedItem);
        // Object.assign(this.remoteData[this.editedIndex], this.editedItem);
      } else {
        this.createUser(this.editedItem);
      }
      this.close();
    }
  },
  mixins: [createRemoteFetchMixin(() => fetchRemoteDataMixin(this.link))]
};
</script>