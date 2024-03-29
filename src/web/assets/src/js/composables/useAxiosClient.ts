import axios from 'axios'
import { usePayRunStore } from '~/stores/payrun'
import { useFetchStore } from '~/stores/fetch'

const ENDPOINT = window.api.cpUrl ?? 'https://localhost:8003'


export const fetchPayRuns = (employerId: string, taxYear: string) => {

    const store = usePayRunStore()
    store.loadingFetched = true

    const url = taxYear ? `${ENDPOINT}/admin/staff-management/pay-runs/fetch-pay-runs/${employerId}/${taxYear}` : `${ENDPOINT}/admin/staff-management/pay-runs/fetch-pay-runs/${employerId}`

    axios({
        method: 'get',
        url: url,
    })
    .then(() => {
        store.loadingFetched = false
    })

}

export const fetchPayRun = (payRunId: string) => {

    const store = usePayRunStore()
    store.loadingFetched = true

    axios({
        method: 'get',
        url: `${ENDPOINT}/admin/staff-management/pay-runs/fetch-pay-run/${payRunId}`,
    })
    .then(() => {
        store.loadingFetched = false
    })

}

export const getPayRunLogs = (payRunId: string) => {

    const store = usePayRunStore()

    axios({
        method: 'get',
        url: `${ENDPOINT}/admin/staff-management/pay-runs/get-logs/${payRunId}`,
    })
        .then((response) => {
            store.logs = response?.data?.logs ? response.data.logs : []
            // store.loadingFetched = false
        })

}

export const fetchDataEmployers = () => {
    const store = useFetchStore()

    store.fetchingEmployers = true

    axios({
        method: 'get',
        url: `${ENDPOINT}/staff-management/fetch/employers`,
    })
        .then((response) => {
            store.fetchingEmployers = false
        })
}

export const fetchDataEmployees = () => {
    const store = useFetchStore()

    store.fetchingEmployees = true

    axios({
        method: 'get',
        url: `${ENDPOINT}/staff-management/fetch/employees`,
    })
        .then((response) => {
            store.fetchingEmployees = false
        })
}

export const fetchDataPayRuns = () => {
    const store = useFetchStore()

    store.fetchingPayRun = true

    axios({
        method: 'get',
        url: `${ENDPOINT}/staff-management/fetch/pay-runs`,
    })
        .then((response) => {
            store.fetchingPayRun = false
        })
}

export const fetchDataPayRunEntries = () => {
    const store = useFetchStore()

    store.fetchingPayRunEntries = true

    axios({
        method: 'get',
        url: `${ENDPOINT}/staff-management/fetch/pay-run-entries`,
    })
        .then((response) => {
            store.fetchingPayRunEntries = false
        })
}

export const getQueue = () => {

    const store = usePayRunStore()

    axios({
        method: 'get',
        url: `${ENDPOINT}/admin/staff-management/pay-runs/queue`,
    })
        .then((response) => {
            store.queue = response?.data?.total ? response.data.total : 0
        })

}

export const getToken = async (): Promise<string|null> => {

    const token = {
        value: null,
    }

    await axios({
        method: 'get',
        url: `${ENDPOINT}/admin/staff-management/settings/get-gql-token`,
    })
    .then( (response) => {
        token.value = response?.data?.token ? response.data.token : null
    })

    return token.value || null

}