export default fetchFunction => ({
    data() {
        return {
            remoteData: [],
            isFetching: false,
            fetchErr: "",
            pagination: {
                page: 1,
                rowsPerPage: -1,
                next: ""
            },
        };
    },
    methods: {
        fetchRemoteDataMixin(link, params) {
            this.isFetching = true;
            if (params) {
                var parameters = new URLSearchParams();
                Object.keys(this.filters).forEach(key => {
                    parameters.append(key, this.filters[key]);
                });
            }
            let LocalLink = link + ((params) ? '?' + parameters.toString() : '');
            fetch(LocalLink)
                .then(async res => {
                    this.remoteData = await res.json();
                    if (this.remoteData.success) {
                        this.pagination.page = this.remoteData.result.current_page;
                        this.pagination.length = this.remoteData.result.last_page;
                        this.remoteData = this.remoteData.result.data;
                        if (this.pagination.page > this.pagination.length) {
                            this.filters.page = this.pagination.length;
                            this.fetchRemoteDataMixin(link, params);
                        }
                    } else {
                        console.log("Can't Fetch Data Please Check Routes");
                    }
                })
                .catch(err => {
                    this.fetchErr = err;
                })
                .finally(() => {
                    this.isFetching = false;
                });
        }
    }
});