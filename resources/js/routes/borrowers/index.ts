import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import subjectTeacher from './subject-teacher'
import subjectAssistant from './subject-assistant'
/**
* @see \App\Http\Controllers\BorrowerController::index
 * @see app/Http/Controllers/BorrowerController.php:19
 * @route '/dashboard/borrowers'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/borrowers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BorrowerController::index
 * @see app/Http/Controllers/BorrowerController.php:19
 * @route '/dashboard/borrowers'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BorrowerController::index
 * @see app/Http/Controllers/BorrowerController.php:19
 * @route '/dashboard/borrowers'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BorrowerController::index
 * @see app/Http/Controllers/BorrowerController.php:19
 * @route '/dashboard/borrowers'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BorrowerController::importMethod
 * @see app/Http/Controllers/BorrowerController.php:248
 * @route '/dashboard/borrowers/import'
 */
export const importMethod = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})

importMethod.definition = {
    methods: ["post"],
    url: '/dashboard/borrowers/import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BorrowerController::importMethod
 * @see app/Http/Controllers/BorrowerController.php:248
 * @route '/dashboard/borrowers/import'
 */
importMethod.url = (options?: RouteQueryOptions) => {
    return importMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BorrowerController::importMethod
 * @see app/Http/Controllers/BorrowerController.php:248
 * @route '/dashboard/borrowers/import'
 */
importMethod.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BorrowerController::create
 * @see app/Http/Controllers/BorrowerController.php:36
 * @route '/dashboard/borrowers/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/borrowers/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BorrowerController::create
 * @see app/Http/Controllers/BorrowerController.php:36
 * @route '/dashboard/borrowers/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BorrowerController::create
 * @see app/Http/Controllers/BorrowerController.php:36
 * @route '/dashboard/borrowers/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BorrowerController::create
 * @see app/Http/Controllers/BorrowerController.php:36
 * @route '/dashboard/borrowers/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BorrowerController::store
 * @see app/Http/Controllers/BorrowerController.php:44
 * @route '/dashboard/borrowers'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/borrowers',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BorrowerController::store
 * @see app/Http/Controllers/BorrowerController.php:44
 * @route '/dashboard/borrowers'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BorrowerController::store
 * @see app/Http/Controllers/BorrowerController.php:44
 * @route '/dashboard/borrowers'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BorrowerController::toggle
 * @see app/Http/Controllers/BorrowerController.php:129
 * @route '/dashboard/borrowers/{borrower}/toggle'
 */
export const toggle = (args: { borrower: number | { id: number } } | [borrower: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

toggle.definition = {
    methods: ["post"],
    url: '/dashboard/borrowers/{borrower}/toggle',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BorrowerController::toggle
 * @see app/Http/Controllers/BorrowerController.php:129
 * @route '/dashboard/borrowers/{borrower}/toggle'
 */
toggle.url = (args: { borrower: number | { id: number } } | [borrower: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { borrower: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { borrower: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    borrower: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        borrower: typeof args.borrower === 'object'
                ? args.borrower.id
                : args.borrower,
                }

    return toggle.definition.url
            .replace('{borrower}', parsedArgs.borrower.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BorrowerController::toggle
 * @see app/Http/Controllers/BorrowerController.php:129
 * @route '/dashboard/borrowers/{borrower}/toggle'
 */
toggle.post = (args: { borrower: number | { id: number } } | [borrower: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BorrowerController::show
 * @see app/Http/Controllers/BorrowerController.php:0
 * @route '/dashboard/borrowers/{borrower}'
 */
export const show = (args: { borrower: string | number } | [borrower: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/borrowers/{borrower}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BorrowerController::show
 * @see app/Http/Controllers/BorrowerController.php:0
 * @route '/dashboard/borrowers/{borrower}'
 */
show.url = (args: { borrower: string | number } | [borrower: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { borrower: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    borrower: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        borrower: args.borrower,
                }

    return show.definition.url
            .replace('{borrower}', parsedArgs.borrower.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BorrowerController::show
 * @see app/Http/Controllers/BorrowerController.php:0
 * @route '/dashboard/borrowers/{borrower}'
 */
show.get = (args: { borrower: string | number } | [borrower: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BorrowerController::show
 * @see app/Http/Controllers/BorrowerController.php:0
 * @route '/dashboard/borrowers/{borrower}'
 */
show.head = (args: { borrower: string | number } | [borrower: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BorrowerController::edit
 * @see app/Http/Controllers/BorrowerController.php:140
 * @route '/dashboard/borrowers/{borrower}/edit'
 */
export const edit = (args: { borrower: number | { id: number } } | [borrower: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/dashboard/borrowers/{borrower}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BorrowerController::edit
 * @see app/Http/Controllers/BorrowerController.php:140
 * @route '/dashboard/borrowers/{borrower}/edit'
 */
edit.url = (args: { borrower: number | { id: number } } | [borrower: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { borrower: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { borrower: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    borrower: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        borrower: typeof args.borrower === 'object'
                ? args.borrower.id
                : args.borrower,
                }

    return edit.definition.url
            .replace('{borrower}', parsedArgs.borrower.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BorrowerController::edit
 * @see app/Http/Controllers/BorrowerController.php:140
 * @route '/dashboard/borrowers/{borrower}/edit'
 */
edit.get = (args: { borrower: number | { id: number } } | [borrower: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BorrowerController::edit
 * @see app/Http/Controllers/BorrowerController.php:140
 * @route '/dashboard/borrowers/{borrower}/edit'
 */
edit.head = (args: { borrower: number | { id: number } } | [borrower: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BorrowerController::update
 * @see app/Http/Controllers/BorrowerController.php:156
 * @route '/dashboard/borrowers/{borrower}'
 */
export const update = (args: { borrower: number | { id: number } } | [borrower: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/dashboard/borrowers/{borrower}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\BorrowerController::update
 * @see app/Http/Controllers/BorrowerController.php:156
 * @route '/dashboard/borrowers/{borrower}'
 */
update.url = (args: { borrower: number | { id: number } } | [borrower: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { borrower: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { borrower: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    borrower: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        borrower: typeof args.borrower === 'object'
                ? args.borrower.id
                : args.borrower,
                }

    return update.definition.url
            .replace('{borrower}', parsedArgs.borrower.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BorrowerController::update
 * @see app/Http/Controllers/BorrowerController.php:156
 * @route '/dashboard/borrowers/{borrower}'
 */
update.put = (args: { borrower: number | { id: number } } | [borrower: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\BorrowerController::destroy
 * @see app/Http/Controllers/BorrowerController.php:0
 * @route '/dashboard/borrowers/{borrower}'
 */
export const destroy = (args: { borrower: string | number } | [borrower: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/borrowers/{borrower}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\BorrowerController::destroy
 * @see app/Http/Controllers/BorrowerController.php:0
 * @route '/dashboard/borrowers/{borrower}'
 */
destroy.url = (args: { borrower: string | number } | [borrower: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { borrower: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    borrower: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        borrower: args.borrower,
                }

    return destroy.definition.url
            .replace('{borrower}', parsedArgs.borrower.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BorrowerController::destroy
 * @see app/Http/Controllers/BorrowerController.php:0
 * @route '/dashboard/borrowers/{borrower}'
 */
destroy.delete = (args: { borrower: string | number } | [borrower: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const borrowers = {
    index: Object.assign(index, index),
import: Object.assign(importMethod, importMethod),
create: Object.assign(create, create),
store: Object.assign(store, store),
toggle: Object.assign(toggle, toggle),
subjectTeacher: Object.assign(subjectTeacher, subjectTeacher),
subjectAssistant: Object.assign(subjectAssistant, subjectAssistant),
show: Object.assign(show, show),
edit: Object.assign(edit, edit),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
}

export default borrowers