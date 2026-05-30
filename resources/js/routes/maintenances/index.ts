import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import equipment from './equipment'
/**
* @see \App\Http\Controllers\MaintenanceController::report
 * @see app/Http/Controllers/MaintenanceController.php:36
 * @route '/dashboard/maintenances/{id}/report'
 */
export const report = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: report.url(args, options),
    method: 'get',
})

report.definition = {
    methods: ["get","head"],
    url: '/dashboard/maintenances/{id}/report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MaintenanceController::report
 * @see app/Http/Controllers/MaintenanceController.php:36
 * @route '/dashboard/maintenances/{id}/report'
 */
report.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return report.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceController::report
 * @see app/Http/Controllers/MaintenanceController.php:36
 * @route '/dashboard/maintenances/{id}/report'
 */
report.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: report.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MaintenanceController::report
 * @see app/Http/Controllers/MaintenanceController.php:36
 * @route '/dashboard/maintenances/{id}/report'
 */
report.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: report.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\MaintenanceController::index
 * @see app/Http/Controllers/MaintenanceController.php:19
 * @route '/dashboard/maintenances'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/maintenances',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MaintenanceController::index
 * @see app/Http/Controllers/MaintenanceController.php:19
 * @route '/dashboard/maintenances'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceController::index
 * @see app/Http/Controllers/MaintenanceController.php:19
 * @route '/dashboard/maintenances'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MaintenanceController::index
 * @see app/Http/Controllers/MaintenanceController.php:19
 * @route '/dashboard/maintenances'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\MaintenanceController::create
 * @see app/Http/Controllers/MaintenanceController.php:70
 * @route '/dashboard/maintenances/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/maintenances/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MaintenanceController::create
 * @see app/Http/Controllers/MaintenanceController.php:70
 * @route '/dashboard/maintenances/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceController::create
 * @see app/Http/Controllers/MaintenanceController.php:70
 * @route '/dashboard/maintenances/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MaintenanceController::create
 * @see app/Http/Controllers/MaintenanceController.php:70
 * @route '/dashboard/maintenances/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\MaintenanceController::store
 * @see app/Http/Controllers/MaintenanceController.php:81
 * @route '/dashboard/maintenances'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/maintenances',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MaintenanceController::store
 * @see app/Http/Controllers/MaintenanceController.php:81
 * @route '/dashboard/maintenances'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceController::store
 * @see app/Http/Controllers/MaintenanceController.php:81
 * @route '/dashboard/maintenances'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MaintenanceController::show
 * @see app/Http/Controllers/MaintenanceController.php:134
 * @route '/dashboard/maintenances/{maintenance}'
 */
export const show = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/maintenances/{maintenance}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MaintenanceController::show
 * @see app/Http/Controllers/MaintenanceController.php:134
 * @route '/dashboard/maintenances/{maintenance}'
 */
show.url = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { maintenance: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { maintenance: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    maintenance: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        maintenance: typeof args.maintenance === 'object'
                ? args.maintenance.id
                : args.maintenance,
                }

    return show.definition.url
            .replace('{maintenance}', parsedArgs.maintenance.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceController::show
 * @see app/Http/Controllers/MaintenanceController.php:134
 * @route '/dashboard/maintenances/{maintenance}'
 */
show.get = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MaintenanceController::show
 * @see app/Http/Controllers/MaintenanceController.php:134
 * @route '/dashboard/maintenances/{maintenance}'
 */
show.head = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\MaintenanceController::edit
 * @see app/Http/Controllers/MaintenanceController.php:142
 * @route '/dashboard/maintenances/{maintenance}/edit'
 */
export const edit = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/dashboard/maintenances/{maintenance}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MaintenanceController::edit
 * @see app/Http/Controllers/MaintenanceController.php:142
 * @route '/dashboard/maintenances/{maintenance}/edit'
 */
edit.url = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { maintenance: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { maintenance: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    maintenance: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        maintenance: typeof args.maintenance === 'object'
                ? args.maintenance.id
                : args.maintenance,
                }

    return edit.definition.url
            .replace('{maintenance}', parsedArgs.maintenance.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceController::edit
 * @see app/Http/Controllers/MaintenanceController.php:142
 * @route '/dashboard/maintenances/{maintenance}/edit'
 */
edit.get = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MaintenanceController::edit
 * @see app/Http/Controllers/MaintenanceController.php:142
 * @route '/dashboard/maintenances/{maintenance}/edit'
 */
edit.head = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\MaintenanceController::update
 * @see app/Http/Controllers/MaintenanceController.php:150
 * @route '/dashboard/maintenances/{maintenance}'
 */
export const update = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/dashboard/maintenances/{maintenance}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\MaintenanceController::update
 * @see app/Http/Controllers/MaintenanceController.php:150
 * @route '/dashboard/maintenances/{maintenance}'
 */
update.url = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { maintenance: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { maintenance: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    maintenance: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        maintenance: typeof args.maintenance === 'object'
                ? args.maintenance.id
                : args.maintenance,
                }

    return update.definition.url
            .replace('{maintenance}', parsedArgs.maintenance.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceController::update
 * @see app/Http/Controllers/MaintenanceController.php:150
 * @route '/dashboard/maintenances/{maintenance}'
 */
update.put = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\MaintenanceController::destroy
 * @see app/Http/Controllers/MaintenanceController.php:194
 * @route '/dashboard/maintenances/{maintenance}'
 */
export const destroy = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/maintenances/{maintenance}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\MaintenanceController::destroy
 * @see app/Http/Controllers/MaintenanceController.php:194
 * @route '/dashboard/maintenances/{maintenance}'
 */
destroy.url = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { maintenance: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { maintenance: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    maintenance: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        maintenance: typeof args.maintenance === 'object'
                ? args.maintenance.id
                : args.maintenance,
                }

    return destroy.definition.url
            .replace('{maintenance}', parsedArgs.maintenance.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceController::destroy
 * @see app/Http/Controllers/MaintenanceController.php:194
 * @route '/dashboard/maintenances/{maintenance}'
 */
destroy.delete = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const maintenances = {
    report: Object.assign(report, report),
index: Object.assign(index, index),
create: Object.assign(create, create),
store: Object.assign(store, store),
show: Object.assign(show, show),
edit: Object.assign(edit, edit),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
equipment: Object.assign(equipment, equipment),
}

export default maintenances