<style lang="scss" scoped>
    .card {
        margin: 10px;

        hr {
            margin: 0;
        }
    }
</style>
<script>
    const toastSettings = {
        layout: 2,
        position: 'topRight',
    }

    export default {
        data() {
            return {
                text: '',
                loading: false,
                results: [],
            }
        },
        methods: {
            async post() {
                try {
                    this.loading = true;
                    this.results = [];
                    let results = await axios.post('/api/parse', { text: this.text });
                    this.loading = false;
                    toast.success(Object.assign({ title: 'Success!', message: 'Parsing complete!'}, toastSettings));
                    this.results = results.data.data;
                    //console.dir(parsed);
                }
                catch (error) {
                    this.loading = false;
                    toast.error(Object.assign({ title: 'Error!', message: error.message }, toastSettings));
                    //console.dir(error);
                }
            },
        }
    }
</script>
