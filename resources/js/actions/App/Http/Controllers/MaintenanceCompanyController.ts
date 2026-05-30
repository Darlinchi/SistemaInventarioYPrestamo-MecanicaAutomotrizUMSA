import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\MaintenanceCompanyController::index
 * @see app/Http/Controllers/MaintenanceCompanyController.php:16
 * @route '/dashboard/maintenanceCompanies'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/maintenanceCompanies',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::index
 * @see app/Http/Controllers/MaintenanceCompanyController.php:16
 * @route '/dashboard/maintenanceCompanies'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::index
 * @see app/Http/Controllers/MaintenanceCompanyController.php:16
 * @route '/dashboard/maintenanceCompanies'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MaintenanceCompanyController::index
 * @see app/Http/Controllers/MaintenanceCompanyController.php:16
 * @route '/dashboard/maintenanceCompanies'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::create
 * @see app/Http/Controllers/MaintenanceCompanyController.php:39
 * @route '/dashboard/maintenanceCompanies/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/maintenanceCompanies/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::create
 * @see app/Http/Controllers/MaintenanceCompanyController.php:39
 * @route '/dashboard/maintenanceCompanies/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::create
 * @see app/Http/Controllers/MaintenanceCompanyController.php:39
 * @route '/dashboard/maintenanceCompanies/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MaintenanceCompanyController::create
 * @see app/Http/Controllers/MaintenanceCompanyController.php:39
 * @route '/dashboard/maintenanceCompanies/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::store
 * @see app/Http/Controllers/MaintenanceCompanyController.php:47
 * @route '/dashboard/maintenanceCompanies'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/maintenanceCompanies',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::store
 * @see app/Http/Controllers/MaintenanceCompanyController.php:47
 * @route '/dashboard/maintenanceCompanies'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::store
 * @see app/Http/Controllers/MaintenanceCompanyController.php:47
 * @route '/dashboard/maintenanceCompanies'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::show
 * @see app/Http/Controllers/MaintenanceCompanyController.php:80
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}'
 */
export const show = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/maintenanceCompanies/{maintenanceCompany}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::show
 * @see app/Http/Controllers/MaintenanceCompanyController.php:80
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}'
 */
show.url = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { maintenanceCompany: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { maintenanceCompany: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    maintenanceCompany: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        maintenanceCompany: typeof args.maintenanceCompany === 'object'
                ? args.maintenanceCompany.id
                : args.maintenanceCompany,
                }

    return show.definition.url
            .replace('{maintenanceCompany}', parsedArgs.maintenanceCompany.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::show
 * @see app/Http/Controllers/MaintenanceCompanyController.php:80
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}'
 */
show.get = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MaintenanceCompanyController::show
 * @see app/Http/Controllers/MaintenanceCompanyController.php:80
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}'
 */
show.head = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::edit
 * @see app/Http/Controllers/MaintenanceCompanyController.php:88
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}/edit'
 */
export const edit = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/dashboard/maintenanceCompanies/{maintenanceCompany}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::edit
 * @see app/Http/Controllers/MaintenanceCompanyController.php:88
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}/edit'
 */
edit.url = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { maintenanceCompany: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { maintenanceCompany: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    maintenanceCompany: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        maintenanceCompany: typeof args.maintenanceCompany === 'object'
                ? args.maintenanceCompany.id
                : args.maintenanceCompany,
                }

    return edit.definition.url
            .replace('{maintenanceCompany}', parsedArgs.maintenanceCompany.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::edit
 * @see app/Http/Controllers/MaintenanceCompanyController.php:88
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}/edit'
 */
edit.get = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\MaintenanceCompanyController::edit
 * @see app/Http/Controllers/MaintenanceCompanyController.php:88
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}/edit'
 */
edit.head = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::update
 * @see app/Http/Controllers/MaintenanceCompanyController.php:99
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}'
 */
const updatebcfb6b61ef6e51305465cec2cfd70e4b = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updatebcfb6b61ef6e51305465cec2cfd70e4b.url(args, options),
    method: 'put',
})

updatebcfb6b61ef6e51305465cec2cfd70e4b.definition = {
    methods: ["put"],
    url: '/dashboard/maintenanceCompanies/{maintenanceCompany}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::update
 * @see app/Http/Controllers/MaintenanceCompanyController.php:99
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}'
 */
updatebcfb6b61ef6e51305465cec2cfd70e4b.url = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { maintenanceCompany: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { maintenanceCompany: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    maintenanceCompany: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        maintenanceCompany: typeof args.maintenanceCompany === 'object'
                ? args.maintenanceCompany.id
                : args.maintenanceCompany,
                }

    return updatebcfb6b61ef6e51305465cec2cfd70e4b.definition.url
            .replace('{maintenanceCompany}', parsedArgs.maintenanceCompany.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::update
 * @see app/Http/Controllers/MaintenanceCompanyController.php:99
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}'
 */
updatebcfb6b61ef6e51305465cec2cfd70e4b.put = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updatebcfb6b61ef6e51305465cec2cfd70e4b.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\MaintenanceCompanyController::update
 * @see app/Http/Controllers/MaintenanceCompanyController.php:99
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}'
 */
const updatebcfb6b61ef6e51305465cec2cfd70e4b = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updatebcfb6b61ef6e51305465cec2cfd70e4b.url(args, options),
    method: 'patch',
})

updatebcfb6b61ef6e51305465cec2cfd70e4b.definition = {
    methods: ["patch"],
    url: '/dashboard/maintenanceCompanies/{maintenanceCompany}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::update
 * @see app/Http/Controllers/MaintenanceCompanyController.php:99
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}'
 */
updatebcfb6b61ef6e51305465cec2cfd70e4b.url = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { maintenanceCompany: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { maintenanceCompany: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    maintenanceCompany: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        maintenanceCompany: typeof args.maintenanceCompany === 'object'
                ? args.maintenanceCompany.id
                : args.maintenanceCompany,
                }

    return updatebcfb6b61ef6e51305465cec2cfd70e4b.definition.url
            .replace('{maintenanceCompany}', parsedArgs.maintenanceCompany.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::update
 * @see app/Http/Controllers/MaintenanceCompanyController.php:99
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}'
 */
updatebcfb6b61ef6e51305465cec2cfd70e4b.patch = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updatebcfb6b61ef6e51305465cec2cfd70e4b.url(args, options),
    method: 'patch',
})

export const update = {
    '/dashboard/maintenanceCompanies/{maintenanceCompany}': updatebcfb6b61ef6e51305465cec2cfd70e4b,
    '/dashboard/maintenanceCompanies/{maintenanceCompany}': updatebcfb6b61ef6e51305465cec2cfd70e4b,
}

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::destroy
 * @see app/Http/Controllers/MaintenanceCompanyController.php:124
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}'
 */
export const destroy = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/maintenanceCompanies/{maintenanceCompany}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::destroy
 * @see app/Http/Controllers/MaintenanceCompanyController.php:124
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}'
 */
destroy.url = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { maintenanceCompany: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { maintenanceCompany: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    maintenanceCompany: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        maintenanceCompany: typeof args.maintenanceCompany === 'object'
                ? args.maintenanceCompany.id
                : args.maintenanceCompany,
                }

    return destroy.definition.url
            .replace('{maintenanceCompany}', parsedArgs.maintenanceCompany.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MaintenanceCompanyController::destroy
 * @see app/Http/Controllers/MaintenanceCompanyController.php:124
 * @route '/dashboard/maintenanceCompanies/{maintenanceCompany}'
 */
destroy.delete = (args: { maintenanceCompany: number | { id: number } } | [maintenanceCompany: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const MaintenanceCompanyController = { index, create, store, show, edit, update, destroy }

export default MaintenanceCompanyController