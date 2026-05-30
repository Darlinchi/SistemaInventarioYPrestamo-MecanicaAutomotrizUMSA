import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\EquipmentController::index
 * @see app/Http/Controllers/EquipmentController.php:17
 * @route '/dashboard/equipments'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/equipments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EquipmentController::index
 * @see app/Http/Controllers/EquipmentController.php:17
 * @route '/dashboard/equipments'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EquipmentController::index
 * @see app/Http/Controllers/EquipmentController.php:17
 * @route '/dashboard/equipments'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\EquipmentController::index
 * @see app/Http/Controllers/EquipmentController.php:17
 * @route '/dashboard/equipments'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EquipmentController::create
 * @see app/Http/Controllers/EquipmentController.php:25
 * @route '/dashboard/equipments/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/equipments/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EquipmentController::create
 * @see app/Http/Controllers/EquipmentController.php:25
 * @route '/dashboard/equipments/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EquipmentController::create
 * @see app/Http/Controllers/EquipmentController.php:25
 * @route '/dashboard/equipments/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\EquipmentController::create
 * @see app/Http/Controllers/EquipmentController.php:25
 * @route '/dashboard/equipments/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EquipmentController::store
 * @see app/Http/Controllers/EquipmentController.php:34
 * @route '/dashboard/equipments'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/equipments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\EquipmentController::store
 * @see app/Http/Controllers/EquipmentController.php:34
 * @route '/dashboard/equipments'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EquipmentController::store
 * @see app/Http/Controllers/EquipmentController.php:34
 * @route '/dashboard/equipments'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\EquipmentController::show
 * @see app/Http/Controllers/EquipmentController.php:126
 * @route '/dashboard/equipments/{equipment}'
 */
export const show = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/equipments/{equipment}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EquipmentController::show
 * @see app/Http/Controllers/EquipmentController.php:126
 * @route '/dashboard/equipments/{equipment}'
 */
show.url = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{equipment}', parsedArgs.equipment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EquipmentController::show
 * @see app/Http/Controllers/EquipmentController.php:126
 * @route '/dashboard/equipments/{equipment}'
 */
show.get = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\EquipmentController::show
 * @see app/Http/Controllers/EquipmentController.php:126
 * @route '/dashboard/equipments/{equipment}'
 */
show.head = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EquipmentController::edit
 * @see app/Http/Controllers/EquipmentController.php:163
 * @route '/dashboard/equipments/{equipment}/edit'
 */
export const edit = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/dashboard/equipments/{equipment}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EquipmentController::edit
 * @see app/Http/Controllers/EquipmentController.php:163
 * @route '/dashboard/equipments/{equipment}/edit'
 */
edit.url = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return edit.definition.url
            .replace('{equipment}', parsedArgs.equipment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EquipmentController::edit
 * @see app/Http/Controllers/EquipmentController.php:163
 * @route '/dashboard/equipments/{equipment}/edit'
 */
edit.get = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\EquipmentController::edit
 * @see app/Http/Controllers/EquipmentController.php:163
 * @route '/dashboard/equipments/{equipment}/edit'
 */
edit.head = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EquipmentController::update
 * @see app/Http/Controllers/EquipmentController.php:182
 * @route '/dashboard/equipments/{equipment}'
 */
const updatef02380ce70cc8e8dc1e73fc707552ed2 = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updatef02380ce70cc8e8dc1e73fc707552ed2.url(args, options),
    method: 'put',
})

updatef02380ce70cc8e8dc1e73fc707552ed2.definition = {
    methods: ["put"],
    url: '/dashboard/equipments/{equipment}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\EquipmentController::update
 * @see app/Http/Controllers/EquipmentController.php:182
 * @route '/dashboard/equipments/{equipment}'
 */
updatef02380ce70cc8e8dc1e73fc707552ed2.url = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updatef02380ce70cc8e8dc1e73fc707552ed2.definition.url
            .replace('{equipment}', parsedArgs.equipment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EquipmentController::update
 * @see app/Http/Controllers/EquipmentController.php:182
 * @route '/dashboard/equipments/{equipment}'
 */
updatef02380ce70cc8e8dc1e73fc707552ed2.put = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updatef02380ce70cc8e8dc1e73fc707552ed2.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\EquipmentController::update
 * @see app/Http/Controllers/EquipmentController.php:182
 * @route '/dashboard/equipments/{equipment}'
 */
const updatef02380ce70cc8e8dc1e73fc707552ed2 = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updatef02380ce70cc8e8dc1e73fc707552ed2.url(args, options),
    method: 'patch',
})

updatef02380ce70cc8e8dc1e73fc707552ed2.definition = {
    methods: ["patch"],
    url: '/dashboard/equipments/{equipment}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\EquipmentController::update
 * @see app/Http/Controllers/EquipmentController.php:182
 * @route '/dashboard/equipments/{equipment}'
 */
updatef02380ce70cc8e8dc1e73fc707552ed2.url = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updatef02380ce70cc8e8dc1e73fc707552ed2.definition.url
            .replace('{equipment}', parsedArgs.equipment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EquipmentController::update
 * @see app/Http/Controllers/EquipmentController.php:182
 * @route '/dashboard/equipments/{equipment}'
 */
updatef02380ce70cc8e8dc1e73fc707552ed2.patch = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updatef02380ce70cc8e8dc1e73fc707552ed2.url(args, options),
    method: 'patch',
})

export const update = {
    '/dashboard/equipments/{equipment}': updatef02380ce70cc8e8dc1e73fc707552ed2,
    '/dashboard/equipments/{equipment}': updatef02380ce70cc8e8dc1e73fc707552ed2,
}

/**
* @see \App\Http\Controllers\EquipmentController::destroy
 * @see app/Http/Controllers/EquipmentController.php:313
 * @route '/dashboard/equipments/{equipment}'
 */
export const destroy = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/equipments/{equipment}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\EquipmentController::destroy
 * @see app/Http/Controllers/EquipmentController.php:313
 * @route '/dashboard/equipments/{equipment}'
 */
destroy.url = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{equipment}', parsedArgs.equipment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EquipmentController::destroy
 * @see app/Http/Controllers/EquipmentController.php:313
 * @route '/dashboard/equipments/{equipment}'
 */
destroy.delete = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const EquipmentController = { index, create, store, show, edit, update, destroy }

export default EquipmentController