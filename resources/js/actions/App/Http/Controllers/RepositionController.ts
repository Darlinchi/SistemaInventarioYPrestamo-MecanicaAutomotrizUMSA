import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\RepositionController::index
 * @see app/Http/Controllers/RepositionController.php:18
 * @route '/dashboard/repositions'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/repositions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RepositionController::index
 * @see app/Http/Controllers/RepositionController.php:18
 * @route '/dashboard/repositions'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RepositionController::index
 * @see app/Http/Controllers/RepositionController.php:18
 * @route '/dashboard/repositions'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\RepositionController::index
 * @see app/Http/Controllers/RepositionController.php:18
 * @route '/dashboard/repositions'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\RepositionController::index
 * @see app/Http/Controllers/RepositionController.php:18
 * @route '/dashboard/repositions'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\RepositionController::index
 * @see app/Http/Controllers/RepositionController.php:18
 * @route '/dashboard/repositions'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\RepositionController::index
 * @see app/Http/Controllers/RepositionController.php:18
 * @route '/dashboard/repositions'
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
* @see \App\Http\Controllers\RepositionController::create
 * @see app/Http/Controllers/RepositionController.php:28
 * @route '/dashboard/repositions/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/repositions/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RepositionController::create
 * @see app/Http/Controllers/RepositionController.php:28
 * @route '/dashboard/repositions/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RepositionController::create
 * @see app/Http/Controllers/RepositionController.php:28
 * @route '/dashboard/repositions/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\RepositionController::create
 * @see app/Http/Controllers/RepositionController.php:28
 * @route '/dashboard/repositions/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\RepositionController::create
 * @see app/Http/Controllers/RepositionController.php:28
 * @route '/dashboard/repositions/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\RepositionController::create
 * @see app/Http/Controllers/RepositionController.php:28
 * @route '/dashboard/repositions/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\RepositionController::create
 * @see app/Http/Controllers/RepositionController.php:28
 * @route '/dashboard/repositions/create'
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
* @see \App\Http\Controllers\RepositionController::store
 * @see app/Http/Controllers/RepositionController.php:120
 * @route '/dashboard/repositions'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/repositions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RepositionController::store
 * @see app/Http/Controllers/RepositionController.php:120
 * @route '/dashboard/repositions'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RepositionController::store
 * @see app/Http/Controllers/RepositionController.php:120
 * @route '/dashboard/repositions'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\RepositionController::store
 * @see app/Http/Controllers/RepositionController.php:120
 * @route '/dashboard/repositions'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\RepositionController::store
 * @see app/Http/Controllers/RepositionController.php:120
 * @route '/dashboard/repositions'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\RepositionController::show
 * @see app/Http/Controllers/RepositionController.php:154
 * @route '/dashboard/repositions/{reposition}'
 */
export const show = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/repositions/{reposition}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RepositionController::show
 * @see app/Http/Controllers/RepositionController.php:154
 * @route '/dashboard/repositions/{reposition}'
 */
show.url = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { reposition: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { reposition: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    reposition: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        reposition: typeof args.reposition === 'object'
                ? args.reposition.id
                : args.reposition,
                }

    return show.definition.url
            .replace('{reposition}', parsedArgs.reposition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RepositionController::show
 * @see app/Http/Controllers/RepositionController.php:154
 * @route '/dashboard/repositions/{reposition}'
 */
show.get = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\RepositionController::show
 * @see app/Http/Controllers/RepositionController.php:154
 * @route '/dashboard/repositions/{reposition}'
 */
show.head = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\RepositionController::show
 * @see app/Http/Controllers/RepositionController.php:154
 * @route '/dashboard/repositions/{reposition}'
 */
    const showForm = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\RepositionController::show
 * @see app/Http/Controllers/RepositionController.php:154
 * @route '/dashboard/repositions/{reposition}'
 */
        showForm.get = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\RepositionController::show
 * @see app/Http/Controllers/RepositionController.php:154
 * @route '/dashboard/repositions/{reposition}'
 */
        showForm.head = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\RepositionController::edit
 * @see app/Http/Controllers/RepositionController.php:162
 * @route '/dashboard/repositions/{reposition}/edit'
 */
export const edit = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/dashboard/repositions/{reposition}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RepositionController::edit
 * @see app/Http/Controllers/RepositionController.php:162
 * @route '/dashboard/repositions/{reposition}/edit'
 */
edit.url = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { reposition: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { reposition: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    reposition: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        reposition: typeof args.reposition === 'object'
                ? args.reposition.id
                : args.reposition,
                }

    return edit.definition.url
            .replace('{reposition}', parsedArgs.reposition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RepositionController::edit
 * @see app/Http/Controllers/RepositionController.php:162
 * @route '/dashboard/repositions/{reposition}/edit'
 */
edit.get = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\RepositionController::edit
 * @see app/Http/Controllers/RepositionController.php:162
 * @route '/dashboard/repositions/{reposition}/edit'
 */
edit.head = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\RepositionController::edit
 * @see app/Http/Controllers/RepositionController.php:162
 * @route '/dashboard/repositions/{reposition}/edit'
 */
    const editForm = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\RepositionController::edit
 * @see app/Http/Controllers/RepositionController.php:162
 * @route '/dashboard/repositions/{reposition}/edit'
 */
        editForm.get = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\RepositionController::edit
 * @see app/Http/Controllers/RepositionController.php:162
 * @route '/dashboard/repositions/{reposition}/edit'
 */
        editForm.head = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\RepositionController::update
 * @see app/Http/Controllers/RepositionController.php:180
 * @route '/dashboard/repositions/{reposition}'
 */
export const update = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/dashboard/repositions/{reposition}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\RepositionController::update
 * @see app/Http/Controllers/RepositionController.php:180
 * @route '/dashboard/repositions/{reposition}'
 */
update.url = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { reposition: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { reposition: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    reposition: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        reposition: typeof args.reposition === 'object'
                ? args.reposition.id
                : args.reposition,
                }

    return update.definition.url
            .replace('{reposition}', parsedArgs.reposition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RepositionController::update
 * @see app/Http/Controllers/RepositionController.php:180
 * @route '/dashboard/repositions/{reposition}'
 */
update.put = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\RepositionController::update
 * @see app/Http/Controllers/RepositionController.php:180
 * @route '/dashboard/repositions/{reposition}'
 */
update.patch = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\RepositionController::update
 * @see app/Http/Controllers/RepositionController.php:180
 * @route '/dashboard/repositions/{reposition}'
 */
    const updateForm = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\RepositionController::update
 * @see app/Http/Controllers/RepositionController.php:180
 * @route '/dashboard/repositions/{reposition}'
 */
        updateForm.put = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\RepositionController::update
 * @see app/Http/Controllers/RepositionController.php:180
 * @route '/dashboard/repositions/{reposition}'
 */
        updateForm.patch = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\RepositionController::destroy
 * @see app/Http/Controllers/RepositionController.php:321
 * @route '/dashboard/repositions/{reposition}'
 */
export const destroy = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/repositions/{reposition}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\RepositionController::destroy
 * @see app/Http/Controllers/RepositionController.php:321
 * @route '/dashboard/repositions/{reposition}'
 */
destroy.url = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { reposition: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { reposition: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    reposition: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        reposition: typeof args.reposition === 'object'
                ? args.reposition.id
                : args.reposition,
                }

    return destroy.definition.url
            .replace('{reposition}', parsedArgs.reposition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RepositionController::destroy
 * @see app/Http/Controllers/RepositionController.php:321
 * @route '/dashboard/repositions/{reposition}'
 */
destroy.delete = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\RepositionController::destroy
 * @see app/Http/Controllers/RepositionController.php:321
 * @route '/dashboard/repositions/{reposition}'
 */
    const destroyForm = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\RepositionController::destroy
 * @see app/Http/Controllers/RepositionController.php:321
 * @route '/dashboard/repositions/{reposition}'
 */
        destroyForm.delete = (args: { reposition: number | { id: number } } | [reposition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const RepositionController = { index, create, store, show, edit, update, destroy }

export default RepositionController