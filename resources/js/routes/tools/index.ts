import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\ToolController::index
 * @see app/Http/Controllers/ToolController.php:17
 * @route '/dashboard/tools'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/tools',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ToolController::index
 * @see app/Http/Controllers/ToolController.php:17
 * @route '/dashboard/tools'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ToolController::index
 * @see app/Http/Controllers/ToolController.php:17
 * @route '/dashboard/tools'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ToolController::index
 * @see app/Http/Controllers/ToolController.php:17
 * @route '/dashboard/tools'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ToolController::create
 * @see app/Http/Controllers/ToolController.php:25
 * @route '/dashboard/tools/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/tools/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ToolController::create
 * @see app/Http/Controllers/ToolController.php:25
 * @route '/dashboard/tools/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ToolController::create
 * @see app/Http/Controllers/ToolController.php:25
 * @route '/dashboard/tools/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ToolController::create
 * @see app/Http/Controllers/ToolController.php:25
 * @route '/dashboard/tools/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ToolController::store
 * @see app/Http/Controllers/ToolController.php:34
 * @route '/dashboard/tools'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/tools',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ToolController::store
 * @see app/Http/Controllers/ToolController.php:34
 * @route '/dashboard/tools'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ToolController::store
 * @see app/Http/Controllers/ToolController.php:34
 * @route '/dashboard/tools'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ToolController::show
 * @see app/Http/Controllers/ToolController.php:98
 * @route '/dashboard/tools/{tool}'
 */
export const show = (args: { tool: number | { id: number } } | [tool: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/tools/{tool}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ToolController::show
 * @see app/Http/Controllers/ToolController.php:98
 * @route '/dashboard/tools/{tool}'
 */
show.url = (args: { tool: number | { id: number } } | [tool: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { tool: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { tool: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    tool: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        tool: typeof args.tool === 'object'
                ? args.tool.id
                : args.tool,
                }

    return show.definition.url
            .replace('{tool}', parsedArgs.tool.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ToolController::show
 * @see app/Http/Controllers/ToolController.php:98
 * @route '/dashboard/tools/{tool}'
 */
show.get = (args: { tool: number | { id: number } } | [tool: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ToolController::show
 * @see app/Http/Controllers/ToolController.php:98
 * @route '/dashboard/tools/{tool}'
 */
show.head = (args: { tool: number | { id: number } } | [tool: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ToolController::edit
 * @see app/Http/Controllers/ToolController.php:106
 * @route '/dashboard/tools/{tool}/edit'
 */
export const edit = (args: { tool: number | { id: number } } | [tool: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/dashboard/tools/{tool}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ToolController::edit
 * @see app/Http/Controllers/ToolController.php:106
 * @route '/dashboard/tools/{tool}/edit'
 */
edit.url = (args: { tool: number | { id: number } } | [tool: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { tool: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { tool: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    tool: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        tool: typeof args.tool === 'object'
                ? args.tool.id
                : args.tool,
                }

    return edit.definition.url
            .replace('{tool}', parsedArgs.tool.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ToolController::edit
 * @see app/Http/Controllers/ToolController.php:106
 * @route '/dashboard/tools/{tool}/edit'
 */
edit.get = (args: { tool: number | { id: number } } | [tool: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ToolController::edit
 * @see app/Http/Controllers/ToolController.php:106
 * @route '/dashboard/tools/{tool}/edit'
 */
edit.head = (args: { tool: number | { id: number } } | [tool: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ToolController::update
 * @see app/Http/Controllers/ToolController.php:121
 * @route '/dashboard/tools/{tool}'
 */
export const update = (args: { tool: number | { id: number } } | [tool: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/dashboard/tools/{tool}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ToolController::update
 * @see app/Http/Controllers/ToolController.php:121
 * @route '/dashboard/tools/{tool}'
 */
update.url = (args: { tool: number | { id: number } } | [tool: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { tool: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { tool: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    tool: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        tool: typeof args.tool === 'object'
                ? args.tool.id
                : args.tool,
                }

    return update.definition.url
            .replace('{tool}', parsedArgs.tool.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ToolController::update
 * @see app/Http/Controllers/ToolController.php:121
 * @route '/dashboard/tools/{tool}'
 */
update.put = (args: { tool: number | { id: number } } | [tool: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ToolController::destroy
 * @see app/Http/Controllers/ToolController.php:182
 * @route '/dashboard/tools/{tool}'
 */
export const destroy = (args: { tool: number | { id: number } } | [tool: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/tools/{tool}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ToolController::destroy
 * @see app/Http/Controllers/ToolController.php:182
 * @route '/dashboard/tools/{tool}'
 */
destroy.url = (args: { tool: number | { id: number } } | [tool: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { tool: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { tool: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    tool: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        tool: typeof args.tool === 'object'
                ? args.tool.id
                : args.tool,
                }

    return destroy.definition.url
            .replace('{tool}', parsedArgs.tool.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ToolController::destroy
 * @see app/Http/Controllers/ToolController.php:182
 * @route '/dashboard/tools/{tool}'
 */
destroy.delete = (args: { tool: number | { id: number } } | [tool: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const tools = {
    index: Object.assign(index, index),
create: Object.assign(create, create),
store: Object.assign(store, store),
show: Object.assign(show, show),
edit: Object.assign(edit, edit),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
}

export default tools