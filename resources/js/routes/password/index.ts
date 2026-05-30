import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import confirmD7e05f from './confirm'

export const confirm = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirm.url(options),
    method: 'get',
})
confirm.definition = {
    methods: ["get","head"],
    url: '/user/confirm-password',
} satisfies RouteDefinition<["get","head"]>
confirm.url = (options?: RouteQueryOptions) => {
    return confirm.definition.url + queryParams(options)
}
confirm.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirm.url(options),
    method: 'get',
})
confirm.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirm.url(options),
    method: 'head',
})

export const confirmation = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmation.url(options),
    method: 'get',
})
confirmation.definition = {
    methods: ["get","head"],
    url: '/user/confirmed-password-status',
} satisfies RouteDefinition<["get","head"]>
confirmation.url = (options?: RouteQueryOptions) => {
    return confirmation.definition.url + queryParams(options)
}
confirmation.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmation.url(options),
    method: 'get',
})
confirmation.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirmation.url(options),
    method: 'head',
})

export const update = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})
update.definition = {
    methods: ["put"],
    url: '/user/password',
} satisfies RouteDefinition<["put"]>
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

const password = {
    confirm: Object.assign(confirm, confirmD7e05f),
    confirmation: Object.assign(confirmation, confirmation),
    update,
}

export default password
