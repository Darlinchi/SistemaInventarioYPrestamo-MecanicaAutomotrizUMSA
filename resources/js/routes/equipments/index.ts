import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
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
* @see \App\Http\Controllers\EquipmentController::index
 * @see app/Http/Controllers/EquipmentController.php:17
 * @route '/dashboard/equipments'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\EquipmentController::index
 * @see app/Http/Controllers/EquipmentController.php:17
 * @route '/dashboard/equipments'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\EquipmentController::index
 * @see app/Http/Controllers/EquipmentController.php:17
 * @route '/dashboard/equipments'
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
* @see \App\Http\Controllers\EquipmentController::create
 * @see app/Http/Controllers/EquipmentController.php:25
 * @route '/dashboard/equipments/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\EquipmentController::create
 * @see app/Http/Controllers/EquipmentController.php:25
 * @route '/dashboard/equipments/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\EquipmentController::create
 * @see app/Http/Controllers/EquipmentController.php:25
 * @route '/dashboard/equipments/create'
 */
        createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    create.form = createForm
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
* @see \App\Http\Controllers\EquipmentController::store
 * @see app/Http/Controllers/EquipmentController.php:34
 * @route '/dashboard/equipments'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\EquipmentController::store
 * @see app/Http/Controllers/EquipmentController.php:34
 * @route '/dashboard/equipments'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
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
* @see \App\Http\Controllers\EquipmentController::show
 * @see app/Http/Controllers/EquipmentController.php:126
 * @route '/dashboard/equipments/{equipment}'
 */
    const showForm = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\EquipmentController::show
 * @see app/Http/Controllers/EquipmentController.php:126
 * @route '/dashboard/equipments/{equipment}'
 */
        showForm.get = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\EquipmentController::show
 * @see app/Http/Controllers/EquipmentController.php:126
 * @route '/dashboard/equipments/{equipment}'
 */
        showForm.head = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
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
* @see \App\Http\Controllers\EquipmentController::edit
 * @see app/Http/Controllers/EquipmentController.php:163
 * @route '/dashboard/equipments/{equipment}/edit'
 */
    const editForm = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\EquipmentController::edit
 * @see app/Http/Controllers/EquipmentController.php:163
 * @route '/dashboard/equipments/{equipment}/edit'
 */
        editForm.get = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\EquipmentController::edit
 * @see app/Http/Controllers/EquipmentController.php:163
 * @route '/dashboard/equipments/{equipment}/edit'
 */
        editForm.head = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    edit.form = editForm
/**
* @see \App\Http\Controllers\EquipmentController::update
 * @see app/Http/Controllers/EquipmentController.php:182
 * @route '/dashboard/equipments/{equipment}'
 */
export const update = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/dashboard/equipments/{equipment}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\EquipmentController::update
 * @see app/Http/Controllers/EquipmentController.php:182
 * @route '/dashboard/equipments/{equipment}'
 */
update.url = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{equipment}', parsedArgs.equipment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EquipmentController::update
 * @see app/Http/Controllers/EquipmentController.php:182
 * @route '/dashboard/equipments/{equipment}'
 */
update.put = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\EquipmentController::update
 * @see app/Http/Controllers/EquipmentController.php:182
 * @route '/dashboard/equipments/{equipment}'
 */
    const updateForm = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\EquipmentController::update
 * @see app/Http/Controllers/EquipmentController.php:182
 * @route '/dashboard/equipments/{equipment}'
 */
        updateForm.put = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
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

    /**
* @see \App\Http\Controllers\EquipmentController::destroy
 * @see app/Http/Controllers/EquipmentController.php:313
 * @route '/dashboard/equipments/{equipment}'
 */
    const destroyForm = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\EquipmentController::destroy
 * @see app/Http/Controllers/EquipmentController.php:313
 * @route '/dashboard/equipments/{equipment}'
 */
        destroyForm.delete = (args: { equipment: number | { id: number } } | [equipment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const equipments = {
    index: Object.assign(index, index),
create: Object.assign(create, create),
store: Object.assign(store, store),
show: Object.assign(show, show),
edit: Object.assign(edit, edit),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
}

export default equipments