import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import confirmD7e05f from './confirm'
import change8775ba from './change'
/**
* @see \Laravel\Fortify\Http\Controllers\ConfirmablePasswordController::confirm
 * @see vendor/laravel/fortify/src/Http/Controllers/ConfirmablePasswordController.php:40
 * @route '/user/confirm-password'
 */
export const confirm = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirm.url(options),
    method: 'get',
})

confirm.definition = {
    methods: ["get","head"],
    url: '/user/confirm-password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Fortify\Http\Controllers\ConfirmablePasswordController::confirm
 * @see vendor/laravel/fortify/src/Http/Controllers/ConfirmablePasswordController.php:40
 * @route '/user/confirm-password'
 */
confirm.url = (options?: RouteQueryOptions) => {
    return confirm.definition.url + queryParams(options)
}

/**
* @see \Laravel\Fortify\Http\Controllers\ConfirmablePasswordController::confirm
 * @see vendor/laravel/fortify/src/Http/Controllers/ConfirmablePasswordController.php:40
 * @route '/user/confirm-password'
 */
confirm.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirm.url(options),
    method: 'get',
})
/**
* @see \Laravel\Fortify\Http\Controllers\ConfirmablePasswordController::confirm
 * @see vendor/laravel/fortify/src/Http/Controllers/ConfirmablePasswordController.php:40
 * @route '/user/confirm-password'
 */
confirm.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirm.url(options),
    method: 'head',
})

    /**
* @see \Laravel\Fortify\Http\Controllers\ConfirmablePasswordController::confirm
 * @see vendor/laravel/fortify/src/Http/Controllers/ConfirmablePasswordController.php:40
 * @route '/user/confirm-password'
 */
    const confirmForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: confirm.url(options),
        method: 'get',
    })

            /**
* @see \Laravel\Fortify\Http\Controllers\ConfirmablePasswordController::confirm
 * @see vendor/laravel/fortify/src/Http/Controllers/ConfirmablePasswordController.php:40
 * @route '/user/confirm-password'
 */
        confirmForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: confirm.url(options),
            method: 'get',
        })
            /**
* @see \Laravel\Fortify\Http\Controllers\ConfirmablePasswordController::confirm
 * @see vendor/laravel/fortify/src/Http/Controllers/ConfirmablePasswordController.php:40
 * @route '/user/confirm-password'
 */
        confirmForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: confirm.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    confirm.form = confirmForm
/**
* @see \Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController::confirmation
 * @see vendor/laravel/fortify/src/Http/Controllers/ConfirmedPasswordStatusController.php:17
 * @route '/user/confirmed-password-status'
 */
export const confirmation = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmation.url(options),
    method: 'get',
})

confirmation.definition = {
    methods: ["get","head"],
    url: '/user/confirmed-password-status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController::confirmation
 * @see vendor/laravel/fortify/src/Http/Controllers/ConfirmedPasswordStatusController.php:17
 * @route '/user/confirmed-password-status'
 */
confirmation.url = (options?: RouteQueryOptions) => {
    return confirmation.definition.url + queryParams(options)
}

/**
* @see \Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController::confirmation
 * @see vendor/laravel/fortify/src/Http/Controllers/ConfirmedPasswordStatusController.php:17
 * @route '/user/confirmed-password-status'
 */
confirmation.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmation.url(options),
    method: 'get',
})
/**
* @see \Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController::confirmation
 * @see vendor/laravel/fortify/src/Http/Controllers/ConfirmedPasswordStatusController.php:17
 * @route '/user/confirmed-password-status'
 */
confirmation.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirmation.url(options),
    method: 'head',
})

    /**
* @see \Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController::confirmation
 * @see vendor/laravel/fortify/src/Http/Controllers/ConfirmedPasswordStatusController.php:17
 * @route '/user/confirmed-password-status'
 */
    const confirmationForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: confirmation.url(options),
        method: 'get',
    })

            /**
* @see \Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController::confirmation
 * @see vendor/laravel/fortify/src/Http/Controllers/ConfirmedPasswordStatusController.php:17
 * @route '/user/confirmed-password-status'
 */
        confirmationForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: confirmation.url(options),
            method: 'get',
        })
            /**
* @see \Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController::confirmation
 * @see vendor/laravel/fortify/src/Http/Controllers/ConfirmedPasswordStatusController.php:17
 * @route '/user/confirmed-password-status'
 */
        confirmationForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: confirmation.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    confirmation.form = confirmationForm
/**
* @see \App\Http\Controllers\ForcePasswordChangeController::change
 * @see app/Http/Controllers/ForcePasswordChangeController.php:12
 * @route '/dashboard/cambiar-contrasena'
 */
export const change = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: change.url(options),
    method: 'get',
})

change.definition = {
    methods: ["get","head"],
    url: '/dashboard/cambiar-contrasena',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ForcePasswordChangeController::change
 * @see app/Http/Controllers/ForcePasswordChangeController.php:12
 * @route '/dashboard/cambiar-contrasena'
 */
change.url = (options?: RouteQueryOptions) => {
    return change.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ForcePasswordChangeController::change
 * @see app/Http/Controllers/ForcePasswordChangeController.php:12
 * @route '/dashboard/cambiar-contrasena'
 */
change.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: change.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ForcePasswordChangeController::change
 * @see app/Http/Controllers/ForcePasswordChangeController.php:12
 * @route '/dashboard/cambiar-contrasena'
 */
change.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: change.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ForcePasswordChangeController::change
 * @see app/Http/Controllers/ForcePasswordChangeController.php:12
 * @route '/dashboard/cambiar-contrasena'
 */
    const changeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: change.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ForcePasswordChangeController::change
 * @see app/Http/Controllers/ForcePasswordChangeController.php:12
 * @route '/dashboard/cambiar-contrasena'
 */
        changeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: change.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ForcePasswordChangeController::change
 * @see app/Http/Controllers/ForcePasswordChangeController.php:12
 * @route '/dashboard/cambiar-contrasena'
 */
        changeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: change.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    change.form = changeForm
const password = {
    confirm: Object.assign(confirm, confirmD7e05f),
confirmation: Object.assign(confirmation, confirmation),
change: Object.assign(change, change8775ba),
}

export default password