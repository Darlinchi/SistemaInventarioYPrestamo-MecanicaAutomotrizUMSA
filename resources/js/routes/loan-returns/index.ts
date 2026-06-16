import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
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
* @see \App\Http\Controllers\LoanReturnController::store
 * @see app/Http/Controllers/LoanReturnController.php:130
 * @route '/dashboard/loan-returns'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LoanReturnController::store
 * @see app/Http/Controllers/LoanReturnController.php:130
 * @route '/dashboard/loan-returns'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
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

    /**
* @see \App\Http\Controllers\LoanReturnController::index
 * @see app/Http/Controllers/LoanReturnController.php:20
 * @route '/dashboard/loan-returns'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\LoanReturnController::index
 * @see app/Http/Controllers/LoanReturnController.php:20
 * @route '/dashboard/loan-returns'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\LoanReturnController::index
 * @see app/Http/Controllers/LoanReturnController.php:20
 * @route '/dashboard/loan-returns'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
const loanReturns = {
    store: Object.assign(store, store),
index: Object.assign(index, index),
}

export default loanReturns