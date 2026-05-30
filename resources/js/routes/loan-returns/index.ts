import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\LoanReturnController::store
 * @see app/Http/Controllers/LoanReturnController.php:130
 * @route '/dashboard/loan-returns'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/loan-returns',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LoanReturnController::store
 * @see app/Http/Controllers/LoanReturnController.php:130
 * @route '/dashboard/loan-returns'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanReturnController::store
 * @see app/Http/Controllers/LoanReturnController.php:130
 * @route '/dashboard/loan-returns'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LoanReturnController::index
 * @see app/Http/Controllers/LoanReturnController.php:20
 * @route '/dashboard/loan-returns'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/loan-returns',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LoanReturnController::index
 * @see app/Http/Controllers/LoanReturnController.php:20
 * @route '/dashboard/loan-returns'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanReturnController::index
 * @see app/Http/Controllers/LoanReturnController.php:20
 * @route '/dashboard/loan-returns'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\LoanReturnController::index
 * @see app/Http/Controllers/LoanReturnController.php:20
 * @route '/dashboard/loan-returns'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})
const loanReturns = {
    store: Object.assign(store, store),
index: Object.assign(index, index),
}

export default loanReturns