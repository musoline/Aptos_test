<template>
  <div>
    <template>
      <v-data-table
        :headers="headers"
        :items="remoteData"
        item-key="id"
        :loading="isFetching"
        hide-default-footer
        class="elevation-1"
        >
          <template v-slot:top>
            <v-toolbar
              flat
            >
              <v-toolbar-title>კომპანიები</v-toolbar-title>
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
                    კომპანიის დამატება
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
                            v-model="editedItem.name"
                            label="კომპანიის სახელი"
                          ></v-text-field>
                        </v-col>
                        <v-col
                          cols="12"
                          sm="6"
                          md="4"
                        >
                          <v-text-field
                            v-model="editedItem.email"
                            label="კომპანიის ელ. ფოსტა"
                          ></v-text-field>
                        </v-col>
                        <v-col
                          cols="12"
                          sm="6"
                          md="4"
                        >
                          <v-text-field label="კომპანიის ლოგო" @click='pickFile' v-model='imageName'></v-text-field>
                          <input
                            type="file"
                            style="display: none"
                            ref="image"
                            accept="image/*"
                            @change="onFilePicked"
                          >
                        </v-col>
                        <v-col
                          cols="12"
                          sm="6"
                          md="4"
                        >
                          <v-text-field
                            v-model="editedItem.website"
                            label="კოპანიის ვებგვერდი"
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
          <template v-slot:item.logo="{ item }">
            <v-img
              max-height="100"
              max-width="100"
              :src="'http://127.0.0.1:8000/storage/'+item.logo"
            ></v-img>
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
      link: "/api/company",
      dialog: false,
      dialogDelete: false,
      imageName:'',
      imageUrl:'',
      imageFile:'',
      _method:"PUT",
      file:[],
      filters: {},
      headers: [
        {
          text: 'კომპანიის სახელი',
          align: 'start',
          sortable: false,
          value: 'name',
        },
        { text: 'კომპანიის ელ. ფოსტა', value: 'email' },
        { text: 'კომპანიის ლოგო', value: 'logo' },
        { text: 'კომპანიის ვებსაიტი', value: 'website' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],
      editedIndex: -1,
      editedItem: {
        name: null,
        email: null,
        logo: null,
        website: null,
      },
      defaultItem: {
        name: null,
        email: null,
        logo: null,
        website: null,
      },
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'კომპანია' : 'Edit Item'
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
    },

    methods: {

      pickFile() {
        this.$refs.image.click()
      },
      onFilePicked(e) {
        const files = e.target.files
        this.file = files;
        if (files[0] !== undefined) {
          this.imageName = files[0].name
          if (this.imageName.lastIndexOf('.') <= 0) {
            return
          }
          const fr = new FileReader()
          fr.readAsDataURL(files[0])
          fr.addEventListener('load', () => {
            this.imageFile = files[0]
          })
        } else {
          this.imageName = ''
          this.imageFile = ''
          this.imageUrl = ''
        }
      },
      updateCompany(data) {
        data.logo = this.imageFile;
        data._method = "PUT";
        let formData = new FormData();
        for (let [key, val] of Object.entries(data)) {
          if (Array.isArray(val)) {
            val.forEach(function(e) {
              formData.append(key + "[]", e);
            });
          } else formData.append(key, val);
        }
      axios
        .post("/api/company/"+data.id, formData)
        .then(res => {
          if(res.data.success){
            this.fetchRemoteDataMixin(this.link, this.filters);
          }
        })
        .catch(function(err) {
          console.log("Error", err);
        });
      },
      createCompany(data) {

        data.logo = this.imageFile;

        let formData = new FormData();

        for (let [key, val] of Object.entries(data)) {
          if (Array.isArray(val)) {
            val.forEach(function(e) {
              formData.append(key + "[]", e);
            });
          } else formData.append(key, val);
        }


      axios
        .post("/api/company", formData)
        .then(res => {
          if(res.data.success){
            this.fetchRemoteDataMixin(this.link, this.filters);
          }
        })
        .catch(function(err) {
          console.log("Error", err);
        });
      },
      onPageChange() {
        this.filters.page = this.pagination.page;
        this.fetchRemoteDataMixin(this.link, this.filters);
      },
      editItem (item) {
        this.editedIndex = this.remoteData.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },
      deleteItem(item) {
        const index = this.remoteData.indexOf(item);
        confirm("ნამდვილად გსურს კომპანიის წაშლა?") &&
          fetch("/api/company/" + item.id, {
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
          this.updateCompany(this.editedItem);
          // Object.assign(this.remoteData[this.editedIndex], this.editedItem);s
        } else {
          this.createCompany(this.editedItem);
        }
        this.close()
      },
    },
  mixins: [createRemoteFetchMixin(() => fetchRemoteDataMixin(this.link))]

};
</script>