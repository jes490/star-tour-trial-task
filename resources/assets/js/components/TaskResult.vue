<script>
    export default {
        props: ['result'],
        methods: {

            error() {
                return this.result.status === 'Error';
            },

            success() {
                return this.result.status === 'Success';
            },

            ifResults() {
                return this.result.results.length > 0;
            },

            ifConstraints() {
                return this.result.constraints.length > 0;
            }
        }
    }
</script>
