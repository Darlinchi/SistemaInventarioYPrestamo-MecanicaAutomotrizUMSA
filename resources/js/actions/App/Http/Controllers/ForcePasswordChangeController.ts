import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ForcePasswordChangeController::show
 * @see app/Http/Controllers/ForcePasswordChangeController.php:12
 * @route '/dashboard/cambiar-contrasena'
 */
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/cambiar-contrasena',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ForcePasswordChangeController::show
 * @see app/Http/Controllers/ForcePasswordChangeController.php:12
 * @route '/dashboard/cambiar-contrasena'
 */
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ForcePasswordChangeController::show
 * @see app/Http/Controllers/ForcePasswordChangeController.php:12
 * @route '/dashboard/cambiar-contrasena'
 */
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ForcePasswordChangeController::show
 * @see app/Http/Controllers/ForcePasswordChangeController.php:12
 * @route '/dashboard/cambiar-contrasena'
 */
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ForcePasswordChangeController::show
 * @see app/Http/Controllers/ForcePasswordChangeController.php:12
 * @route '/dashboard/cambiar-contrasena'
 */
    const showForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ForcePasswordChangeController::show
 * @see app/Http/Controllers/ForcePasswordChangeController.php:12
 * @route '/dashboard/cambiar-contrasena'
 */
        showForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ForcePasswordChangeController::show
 * @see app/Http/Controllers/ForcePasswordChangeController.php:12
 * @route '/dashboard/cambiar-contrasena'
 */
        showForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
/**
* @see \App\Http\Controllers\ForcePasswordChangeController::update
 * @see app/Http/Controllers/ForcePasswordChangeController.php:17
 * @route '/dashboard/cambiar-contrasena'
 */
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/dashboard/cambiar-contrasena',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ForcePasswordChangeController::update
 * @see app/Http/Controllers/ForcePasswordChangeController.php:17
 * @route '/dashboard/cambiar-contrasena'
 */
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ForcePasswordChangeController::update
 * @see app/Http/Controllers/ForcePasswordChangeController.php:17
 * @route '/dashboard/cambiar-contrasena'
 */
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ForcePasswordChangeController::update
 * @see app/Http/Controllers/ForcePasswordChangeController.php:17
 * @route '/dashboard/cambiar-contrasena'
 */
    const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ForcePasswordChangeController::update
 * @see app/Http/Controllers/ForcePasswordChangeController.php:17
 * @route '/dashboard/cambiar-contrasena'
 */
        updateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(options),
            method: 'post',
        })
    
    update.form = updateForm
const ForcePasswordChangeController = { show, update }

export default ForcePasswordChangeController