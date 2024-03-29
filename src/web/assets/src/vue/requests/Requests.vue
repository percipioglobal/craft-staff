<script setup lang="ts">
import { ref, watch } from 'vue'
import { useQuery } from '@vue/apollo-composable'
import { REQUESTS } from '~/graphql/requests.ts'
import LoadingList from '~/vue/molecules/listitems/listitem--loading.vue'
import NoResultsList from '~/vue/molecules/listitems/listitem--no-results.vue'
import RequestList from '~/vue/organisms/lists/list--requests.vue'

const pagination = ref({
    currentPage: 0,
    hitsPerPage: 30,
    total: 0
})
const filter = ref('all')
const variables = ref({
    limit: pagination.value.hitsPerPage,
    offset: pagination.value.currentPage * pagination.value.hitsPerPage
})
const { result, loading, fetchMore, onResult } = useQuery(REQUESTS, variables.value)

onResult(queryResult => {
    pagination.value.total = queryResult.data.RequestCount
})

const onLoadMore = () => {
    pagination.value.currentPage += 1
    variables.value = {
        limit: pagination.value.hitsPerPage,
        offset: pagination.value.currentPage * pagination.value.hitsPerPage
    }

    fetchMore({
        variables: variables.value,
        updateQuery: (previousResult, { fetchMoreResult }) => {
            // No new feed posts
            if (!fetchMoreResult) return previousResult

            // Concat previous feed with new feed posts
            return {
                ...previousResult,
                Requests: [
                    ...previousResult.Requests,
                    ...fetchMoreResult.Requests
                ],
                RequestCount: fetchMoreResult.RequestCount
            }
        },
    })
}

const setFilter = (filterValue) => {
    if(filterValue !== 'all') {
        variables.value = {
            limit: pagination.value.hitsPerPage,
            offset: pagination.value.currentPage * pagination.value.hitsPerPage,
            status: filterValue
        }
    } else {
        variables.value = {
            limit: pagination.value.hitsPerPage,
            offset: pagination.value.currentPage * pagination.value.hitsPerPage,
        }
    }

    filter.value = filterValue

    fetchMore({
        variables: variables.value,
        updateQuery: (previousResult, { fetchMoreResult }) => {
            return {
                Requests: fetchMoreResult.Requests,
                RequestCount: fetchMoreResult.RequestCount
            }
        },
    })
}
</script>

<template>

    <div class="sm:flex">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Requests</h1>
            <p class="mt-2 text-sm text-gray-700">Select a request to handle the employees request.</p>
        </div>
    </div>
    <div class="mt-4 flex justify-end">
        <span class="relative z-0 inline-flex shadow-sm rounded-md">
            <button
                @click="() => setFilter('all')"
                :class="[
                    'cursor-pointer font-bold relative flex items-center px-4 py-2 rounded-l-md text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500',
                    filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-indigo-400 hover:text-white'
                ]"
            >
                <span class="mb-0">All</span>
            </button>
            <button
                @click="() => setFilter('pending')"
                :class="[
                    'cursor-pointer font-bold -ml-px relative flex items-center px-4 py-2 text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500',
                    filter === 'pending' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-indigo-400 hover:text-white'
                ]"
            >
                <span class="mb-0">Pending</span>
                <span class="rounded-full w-3 h-3 bg-yellow-300 mb-0">&nbsp;</span>
            </button>
            <button
                @click="() => setFilter('approved')"
                :class="[
                    'cursor-pointer font-bold -ml-px relative flex items-center px-4 py-2 text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500',
                    filter === 'approved' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-indigo-400 hover:text-white'
                ]"
            >
                <span class=mb-0>Approved</span>
                <span class="rounded-full w-3 h-3 bg-emerald-300 mb-0">&nbsp;</span>
            </button>
            <button
                @click="() => setFilter('declined')"
                :class="[
                    'cursor-pointer font-bold -ml-px relative flex items-center px-4 py-2 rounded-r-md text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500',
                    filter === 'declined' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-indigo-400 hover:text-white'
                ]"
            >
                <span class=mb-0>Declined</span>
                <span class="rounded-full w-3 h-3 bg-red-300 mb-0">&nbsp;</span>
            </button>
        </span>
    </div>
    <div class="mt-8 flex flex-col w-full">
        <div class="-my-2 overflow-x-auto w-full">
            <div class="inline-block min-w-full overflow-scroll py-2 align-middle">
                <div class="shadow border border-solid border-gray-300 md:rounded-lg">

                    <!-- HEADINGS -->
                    <div class="grid grid-cols-11 border-b border-solid border-gray-300">
                        <div class="col-span-2 py-3.5 pl-4 pr-3 sm:pl-6 text-left text-sm font-semibold text-gray-900">Employee</div>
                        <div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Company</div>
                        <div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Request Type</div>
                        <div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Requested Date</div>
                        <div class="col-span-2 px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Administered By</div>
                        <div class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</div>
                    </div>

                    <NoResultsList v-if="!loading && result?.Requests?.length === 0" />
                    <RequestList v-if="result" :requests="result.Requests" />
                    <LoadingList v-if="loading" />
                </div>
            </div>
        </div>
        <button
            v-if="result?.Requests?.length !== pagination.total && !loading"
            @click="onLoadMore"
            class="cursor-pointer mt-6 inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 disabled:bg-indigo-400 disabled:cursor-not-allowed px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
        >
            <span>Load more ({{ pagination.total - result?.Requests?.length }})</span>
            <svg v-if="loadig" class="animate-spin ml-1 h-3 w-3 text-white mb-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </button>
    </div>

</template>