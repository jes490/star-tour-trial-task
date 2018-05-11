@extends('layouts.app')

@section('content')
    <task-parser inline-template v-cloak>
        <div class="section parser-wrapper">
            <!-- PARSER INPUT -->
            <div class="columns">
                <div class="column">
                    <h2 class="title has-text-weight-bold">
                        Task Parser
                    </h2>
                    <div class="field">
                        <div class="control">
                            <textarea-autosize class="input" type="text" placeholder="Enter text" v-model="text"></textarea-autosize>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <button style="width: 200px;" class="button is-dark" :class="{ 'is-loading': loading }" :disabled="loading || !text" v-on:click.prevent="post">Parse</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PARSER OUTPUT -->
            <div v-if="results.length > 0" class="columns" v-for="result in results">
                <div class="column">
                    @include('components.result')
                </div>
            </div>
            <div v-if="!loading && completed && results.length === 0">
                <p class="title"><strong>No URLs have been found.</strong></p>
            </div>
        </div>

    </task-parser>
@endsection
