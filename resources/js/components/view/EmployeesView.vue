<template>
  <div>
    <template>
      <v-data-table
          :headers="headers"
          :items="remoteData"
          :loading="isFetching"
          hide-default-footer
          class="elevation-1"
        >
          <template v-slot:top>
            <v-toolbar
              flat
            >
              <v-toolbar-title>თანამშრომლები</v-toolbar-title>
              <v-divider
                class="mx-4"
                inset
                vertical
              ></v-divider>
              <v-spacer></v-spacer>
              <v-dialog
                v-model="dialog"
                max-width="500px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    color="primary"
                    dark
                    class="mb-2"
                    v-bind="attrs"
                    v-on="on"
                  >
                    თანამშრომლის დამატება
                  </v-btn>
                </template>
                <v-card>
                  <v-card-title>
                    <span class="headline">{{ formTitle }}</span>
                  </v-card-title>

                  <v-card-text>
                    <v-container>
                      <v-row>
                        <v-col
                          cols="12"
                          sm="6"
                          md="4"
                        >
                          <v-text-field
                            v-model="editedItem.first_name"
                            label="First Name"
                          ></v-text-field>
                        </v-col>
                        <v-col
                          cols="12"
                          sm="6"
                          md="4"
                        >
                          <v-text-field
                            v-model="editedItem.last_name"
                            label="Last Name"
                          ></v-text-field>
                        </v-col>
                        <v-col
                          cols="12"
                          sm="6"
                          md="4"
                        >
                          <!-- <v-text-field
                            v-model="editedItem.company_id"
                            label="Company"
                          ></v-text-field> -->
                            <v-select
                            v-model="editedItem.company_id"
                            :items="companies.result"
                            item-text="name"
                            item-value="id"
                            label="Select"
                            persistent-hint
                            single-line
                          ></v-select>
                        </v-col>
                        <v-col
                          cols="12"
                          sm="6"
                          md="4"
                        >
                          <v-text-field
                            v-model="editedItem.email"
                            label="Email"
                          ></v-text-field>
                        </v-col>
                        <v-col
                          cols="12"
                          sm="6"
                          md="4"
                        >
                          <v-text-field
                            v-model="editedItem.phone"
                            label="Phone"
                          ></v-text-field>
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-card-text>

                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                      color="blue darken-1"
                      text
                      @click="close"
                    >
                      Cancel
                    </v-btn>
                    <v-btn
                      color="blue darken-1"
                      text
                      @click="save"
                    >
                      Save
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </v-dialog>
              <v-dialog v-model="dialogDelete" max-width="500px">
                <v-card>
                  <v-card-title class="headline">Are you sure you want to delete this item?</v-card-title>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                    <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                    <v-spacer></v-spacer>
                  </v-card-actions>
                </v-card>
              </v-dialog>
            </v-toolbar>
          </template>
          <template v-slot:item.actions="{ item }">
            <v-icon
              small
              class="mr-2"
              @click="editItem(item)"
            >
              mdi-pencil
            </v-icon>
            <v-icon
              small
              @click="deleteItem(item)"
            >
              mdi-delete
            </v-icon>
          </template>
          <template v-slot:no-data>
            <v-btn
              color="primary"
              @click
            >
              Reset
            </v-btn>
          </template>
        </v-data-table>
      <div class="text-xs-center pt-2">
        <v-pagination v-model="pagination.page" :length="pagination.length" @input="onPageChange"></v-pagination>
      </div>
    </template>
  </div>
</template>


<script>

import createRemoteFetchMixin from "../../mixins/fetchMixin";


export default {
    data: () => ({
      link: "/api/employee",
      dialog: false,
      companies:[],
      dialogDelete: false,
      filters: {},
      headers: [
        {
          text: 'სახელი ',
          sortable: false,
          value: 'first_name',
        },
        { text: 'Last Name', value: 'last_name' },
        { text: 'Company', value: 'company.name' },
        { text: 'Email', value: 'email' },
        { text: 'Phone', value: 'phone' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],
      editedIndex: -1,
      editedItem: {
        company_id: null,
        company:[],
        first_name: null,
        last_name: null,
        email: null,
        phone: null,
      },
      defaultItem: {
        name: '',
        calories: 0,
        fat: 0,
        carbs: 0,
        protein: 0,
      },
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
      },

    },

    watch: {
      dialog (val) {
        val || this.close()
      },
      dialogDelete (val) {
        val || this.closeDelete()
      },
    },

    created () {
      this.fetchRemoteDataMixin(this.link);
      this.x_csrf = document.head.querySelector(
        'meta[name="csrf-token"]'
      ).content;
      fetch("/api/companies-list")
        .then(async res => {
            this.companies = await res.json();
            console.log(this.companies.result)
        })
        .catch(err => {
        })
        .finally(() => {
        });

    },

    methods: {
      
      updateEmployee(data) {
        
      axios
        .put("/api/employee/"+data.id, data)
        .then(res => {
          if(res.data.success){
            this.fetchRemoteDataMixin(this.link);
          }
        })
        .catch(function(err) {
          console.log("Error", err);
        });
      },
      createEmployee(data) {
        let formData = new FormData();
        for (let [key, val] of Object.entries(data)) {
          if (Array.isArray(val)) {
            val.forEach(function(e) {
              formData.append(key + "[]", e);
            });
          } else formData.append(key, val);
        }
      axios
        .post("/api/employee", formData)
        .then(res => {
          this.fetchRemoteDataMixin(this.link);
        })
        .catch(function(err) {
          console.log("Error", err);
        });
      },

      editItem (item) {
        this.editedIndex = this.remoteData.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },
      deleteItem(item) {
        const index = this.remoteData.indexOf(item);
        confirm("ნამდვილად გსურს თანამშრომლის წაშლა?") &&
          fetch("/api/employee/" + item.id, {
            method: "DELETE",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-Token": this.x_csrf
            }
          })
            .then(async res => {
              this.fetchRemoteDataMixin(this.link, this.filters);
            })
            .catch(err => {
              this.fetchErr = err;
            })
            .finally(() => {
              this.isFetching = false;
            });
      },
      deleteItemConfirm () {
        this.remoteData.splice(this.editedIndex, 1)
        this.closeDelete()
      },
      onPageChange() {
        this.filters.page = this.pagination.page;
        this.fetchRemoteDataMixin(this.link, this.filters);
      },
      close () {
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      closeDelete () {
        this.dialogDelete = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      save () {
        if (this.editedIndex > -1) {
          this.updateEmployee(this.editedItem);
          Object.assign(this.remoteData[this.editedIndex], this.editedItem);
        } else {
          this.createEmployee(this.editedItem);
        }
        this.close()
      },
    },
  mixins: [createRemoteFetchMixin(() => fetchRemoteDataMixin(this.link))]

};
</script>