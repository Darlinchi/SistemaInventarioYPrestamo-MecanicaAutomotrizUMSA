import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
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
const change = {
    update: Object.assign(update, update),
}

export default change