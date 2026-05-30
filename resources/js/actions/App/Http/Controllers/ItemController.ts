import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ItemController::generateFicha
 * @see app/Http/Controllers/ItemController.php:60
 * @route '/dashboard/items/{id}/pdf'
 */
export const generateFicha = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateFicha.url(args, options),
    method: 'get',
})

generateFicha.definition = {
    methods: ["get","head"],
    url: '/dashboard/items/{id}/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ItemController::generateFicha
 * @see app/Http/Controllers/ItemController.php:60
 * @route '/dashboard/items/{id}/pdf'
 */
generateFicha.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return generateFicha.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ItemController::generateFicha
 * @see app/Http/Controllers/ItemController.php:60
 * @route '/dashboard/items/{id}/pdf'
 */
generateFicha.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateFicha.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ItemController::generateFicha
 * @see app/Http/Controllers/ItemController.php:60
 * @route '/dashboard/items/{id}/pdf'
 */
generateFicha.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generateFicha.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ItemController::index
 * @see app/Http/Controllers/ItemController.php:20
 * @route '/dashboard/items'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/items',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ItemController::index
 * @see app/Http/Controllers/ItemController.php:20
 * @route '/dashboard/items'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ItemController::index
 * @see app/Http/Controllers/ItemController.php:20
 * @route '/dashboard/items'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ItemController::index
 * @see app/Http/Controllers/ItemController.php:20
 * @route '/dashboard/items'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ItemController::create
 * @see app/Http/Controllers/ItemController.php:103
 * @route '/dashboard/items/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/items/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ItemController::create
 * @see app/Http/Controllers/ItemController.php:103
 * @route '/dashboard/items/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ItemController::create
 * @see app/Http/Controllers/ItemController.php:103
 * @route '/dashboard/items/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ItemController::create
 * @see app/Http/Controllers/ItemController.php:103
 * @route '/dashboard/items/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ItemController::store
 * @see app/Http/Controllers/ItemController.php:112
 * @route '/dashboard/items'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/items',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ItemController::store
 * @see app/Http/Controllers/ItemController.php:112
 * @route '/dashboard/items'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ItemController::store
 * @see app/Http/Controllers/ItemController.php:112
 * @route '/dashboard/items'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ItemController::show
 * @see app/Http/Controllers/ItemController.php:280
 * @route '/dashboard/items/{item}'
 */
export const show = (args: { item: string | number | { id: string | number } } | [item: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/items/{item}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ItemController::show
 * @see app/Http/Controllers/ItemController.php:280
 * @route '/dashboard/items/{item}'
 */
show.url = (args: { item: string | number | { id: string | number } } | [item: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { item: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { item: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    item: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        item: typeof args.item === 'object'
                ? args.item.id
                : args.item,
                }

    return show.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ItemController::show
 * @see app/Http/Controllers/ItemController.php:280
 * @route '/dashboard/items/{item}'
 */
show.get = (args: { item: string | number | { id: string | number } } | [item: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ItemController::show
 * @see app/Http/Controllers/ItemController.php:280
 * @route '/dashboard/items/{item}'
 */
show.head = (args: { item: string | number | { id: string | number } } | [item: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ItemController::edit
 * @see app/Http/Controllers/ItemController.php:305
 * @route '/dashboard/items/{item}/edit'
 */
export const edit = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/dashboard/items/{item}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ItemController::edit
 * @see app/Http/Controllers/ItemController.php:305
 * @route '/dashboard/items/{item}/edit'
 */
edit.url = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { item: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    item: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        item: args.item,
                }

    return edit.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ItemController::edit
 * @see app/Http/Controllers/ItemController.php:305
 * @route '/dashboard/items/{item}/edit'
 */
edit.get = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ItemController::edit
 * @see app/Http/Controllers/ItemController.php:305
 * @route '/dashboard/items/{item}/edit'
 */
edit.head = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ItemController::update
 * @see app/Http/Controllers/ItemController.php:439
 * @route '/dashboard/items/{item}'
 */
const updatefe5c4d7f133a15bc33cc6f8bbb23fe07 = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updatefe5c4d7f133a15bc33cc6f8bbb23fe07.url(args, options),
    method: 'put',
})

updatefe5c4d7f133a15bc33cc6f8bbb23fe07.definition = {
    methods: ["put"],
    url: '/dashboard/items/{item}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ItemController::update
 * @see app/Http/Controllers/ItemController.php:439
 * @route '/dashboard/items/{item}'
 */
updatefe5c4d7f133a15bc33cc6f8bbb23fe07.url = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { item: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    item: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        item: args.item,
                }

    return updatefe5c4d7f133a15bc33cc6f8bbb23fe07.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ItemController::update
 * @see app/Http/Controllers/ItemController.php:439
 * @route '/dashboard/items/{item}'
 */
updatefe5c4d7f133a15bc33cc6f8bbb23fe07.put = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updatefe5c4d7f133a15bc33cc6f8bbb23fe07.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\ItemController::update
 * @see app/Http/Controllers/ItemController.php:439
 * @route '/dashboard/items/{item}'
 */
const updatefe5c4d7f133a15bc33cc6f8bbb23fe07 = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updatefe5c4d7f133a15bc33cc6f8bbb23fe07.url(args, options),
    method: 'patch',
})

updatefe5c4d7f133a15bc33cc6f8bbb23fe07.definition = {
    methods: ["patch"],
    url: '/dashboard/items/{item}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\ItemController::update
 * @see app/Http/Controllers/ItemController.php:439
 * @route '/dashboard/items/{item}'
 */
updatefe5c4d7f133a15bc33cc6f8bbb23fe07.url = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { item: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    item: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        item: args.item,
                }

    return updatefe5c4d7f133a15bc33cc6f8bbb23fe07.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ItemController::update
 * @see app/Http/Controllers/ItemController.php:439
 * @route '/dashboard/items/{item}'
 */
updatefe5c4d7f133a15bc33cc6f8bbb23fe07.patch = (args: { item: string | number } | [item: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updatefe5c4d7f133a15bc33cc6f8bbb23fe07.url(args, options),
    method: 'patch',
})

export const update = {
    '/dashboard/items/{item}': updatefe5c4d7f133a15bc33cc6f8bbb23fe07,
    '/dashboard/items/{item}': updatefe5c4d7f133a15bc33cc6f8bbb23fe07,
}

/**
* @see \App\Http\Controllers\ItemController::destroy
 * @see app/Http/Controllers/ItemController.php:541
 * @route '/dashboard/items/{item}'
 */
export const destroy = (args: { item: string | number | { id: string | number } } | [item: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/items/{item}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ItemController::destroy
 * @see app/Http/Controllers/ItemController.php:541
 * @route '/dashboard/items/{item}'
 */
destroy.url = (args: { item: string | number | { id: string | number } } | [item: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { item: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { item: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    item: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        item: typeof args.item === 'object'
                ? args.item.id
                : args.item,
                }

    return destroy.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ItemController::destroy
 * @see app/Http/Controllers/ItemController.php:541
 * @route '/dashboard/items/{item}'
 */
destroy.delete = (args: { item: string | number | { id: string | number } } | [item: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const ItemController = { generateFicha, index, create, store, show, edit, update, destroy }

export default ItemController