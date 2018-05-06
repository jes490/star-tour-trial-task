<task-result :result="result" inline-template>
    <div class="card">

        <!-- URL HEADER -->
        <header class="card-header">
            <a class="card-header-title" :class="{ 'has-text-danger': error(), 'has-text-success': !error() }" :href="result.url" target="_blank">
                @{{ result.url }}
            </a>
        </header>

        <!-- RESULT BODY -->
        <div class="card-content">
            <!-- RESULTS -->
            <div class="content" v-if="success()">
                <p><strong>Results:</strong></p>
                <p class="content" v-if="success() && !ifResults()">
                    No results.
                </p>
                <ul v-for="result in result.results">
                    <li>@{{ result.result }}</li>
                </ul>
            </div>
            <!-- ERRORS -->
            <div class="content" v-if="error()">
                <p><strong>Errors:</strong></p>
                <ul>
                    <li v-for="err in result.errors" style="margin-bottom: 20px;">
                        <p><strong>Message:</strong> @{{ err.message }}</p>
                        <p v-if="err.url"><strong>From: </strong> @{{ err.url }}</p>
                        <p v-if="err.HTTPCode"><strong>HTTPCode: </strong>@{{ err.HTTPCode }}</p>
                    </li>
                </ul>
            </div>
        </div>

        <!-- CONSTRAINTS -->
        <div class="card-content" v-if="ifConstraints">
            <hr>
            <div class="content">
                <p><strong>Constraints:</strong></p>
                <ul>
                    <li v-for="constraint in result.constraints">
                        <em class="has-text-info">@{{ constraint.constraint }}</em>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</task-result>

