import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\MaintenanceController::generateReport
 * @see app/Http/Controllers/MaintenanceController.php:36
 * @route '/dashboard/maintenances/{id}/report'
 */
export const generateReport = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateReport.url(args, options),
    method: 'get',
})

generateReport.definition = {
    methods: ["get","head"],
    url: '/dashboard/maintenances/{id}/report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MaintenanceController::generateReport
 * @see app/Http/Controllers/MaintenanceController.php:36
 * @route '/dashboard/maintenances/{id}/report'
 */
generateReport.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return generateReport.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceController::generateReport
 * @see app/Http/Controllers/MaintenanceController.php:36
 * @route '/dashboard/maintenances/{id}/report'
 */
generateReport.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateReport.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MaintenanceController::generateReport
 * @see app/Http/Controllers/MaintenanceController.php:36
 * @route '/dashboard/maintenances/{id}/report'
 */
generateReport.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generateReport.url(args, options),
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
const update0dd669d0d1cdec70f3820a151c1df5f5 = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update0dd669d0d1cdec70f3820a151c1df5f5.url(args, options),
    method: 'put',
})

update0dd669d0d1cdec70f3820a151c1df5f5.definition = {
    methods: ["put"],
    url: '/dashboard/maintenances/{maintenance}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\MaintenanceController::update
 * @see app/Http/Controllers/MaintenanceController.php:150
 * @route '/dashboard/maintenances/{maintenance}'
 */
update0dd669d0d1cdec70f3820a151c1df5f5.url = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update0dd669d0d1cdec70f3820a151c1df5f5.definition.url
            .replace('{maintenance}', parsedArgs.maintenance.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceController::update
 * @see app/Http/Controllers/MaintenanceController.php:150
 * @route '/dashboard/maintenances/{maintenance}'
 */
update0dd669d0d1cdec70f3820a151c1df5f5.put = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update0dd669d0d1cdec70f3820a151c1df5f5.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\MaintenanceController::update
 * @see app/Http/Controllers/MaintenanceController.php:150
 * @route '/dashboard/maintenances/{maintenance}'
 */
const update0dd669d0d1cdec70f3820a151c1df5f5 = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update0dd669d0d1cdec70f3820a151c1df5f5.url(args, options),
    method: 'patch',
})

update0dd669d0d1cdec70f3820a151c1df5f5.definition = {
    methods: ["patch"],
    url: '/dashboard/maintenances/{maintenance}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\MaintenanceController::update
 * @see app/Http/Controllers/MaintenanceController.php:150
 * @route '/dashboard/maintenances/{maintenance}'
 */
update0dd669d0d1cdec70f3820a151c1df5f5.url = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update0dd669d0d1cdec70f3820a151c1df5f5.definition.url
            .replace('{maintenance}', parsedArgs.maintenance.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceController::update
 * @see app/Http/Controllers/MaintenanceController.php:150
 * @route '/dashboard/maintenances/{maintenance}'
 */
update0dd669d0d1cdec70f3820a151c1df5f5.patch = (args: { maintenance: number | { id: number } } | [maintenance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update0dd669d0d1cdec70f3820a151c1df5f5.url(args, options),
    method: 'patch',
})

export const update = {
    '/dashboard/maintenances/{maintenance}': update0dd669d0d1cdec70f3820a151c1df5f5,
    '/dashboard/maintenances/{maintenance}': update0dd669d0d1cdec70f3820a151c1df5f5,
}

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

/**
* @see \App\Http\Controllers\MaintenanceController::equipmentHistoryPdf
 * @see app/Http/Controllers/MaintenanceController.php:51
 * @route '/dashboard/maintenances/equipment/{equipment}/history-pdf'
 */
export const equipmentHistoryPdf = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: equipmentHistoryPdf.url(args, options),
    method: 'get',
})

equipmentHistoryPdf.definition = {
    methods: ["get","head"],
    url: '/dashboard/maintenances/equipment/{equipment}/history-pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MaintenanceController::equipmentHistoryPdf
 * @see app/Http/Controllers/MaintenanceController.php:51
 * @route '/dashboard/maintenances/equipment/{equipment}/history-pdf'
 */
equipmentHistoryPdf.url = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { equipment: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { equipment: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    equipment: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        equipment: typeof args.equipment === 'object'
                ? args.equipment.id
                : args.equipment,
                }

    return equipmentHistoryPdf.definition.url
            .replace('{equipment}', parsedArgs.equipment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceController::equipmentHistoryPdf
 * @see app/Http/Controllers/MaintenanceController.php:51
 * @route '/dashboard/maintenances/equipment/{equipment}/history-pdf'
 */
equipmentHistoryPdf.get = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: equipmentHistoryPdf.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MaintenanceController::equipmentHistoryPdf
 * @see app/Http/Controllers/MaintenanceController.php:51
 * @route '/dashboard/maintenances/equipment/{equipment}/history-pdf'
 */
equipmentHistoryPdf.head = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: equipmentHistoryPdf.url(args, options),
    method: 'head',
})
const MaintenanceController = { generateReport, index, create, store, show, edit, update, destroy, equipmentHistoryPdf }

export default MaintenanceController