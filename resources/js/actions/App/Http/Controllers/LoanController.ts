import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\LoanController::generateReport
 * @see app/Http/Controllers/LoanController.php:45
 * @route '/dashboard/loans/{id}/report'
 */
export const generateReport = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateReport.url(args, options),
    method: 'get',
})

generateReport.definition = {
    methods: ["get","head"],
    url: '/dashboard/loans/{id}/report',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LoanController::generateReport
 * @see app/Http/Controllers/LoanController.php:45
 * @route '/dashboard/loans/{id}/report'
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
* @see \App\Http\Controllers\LoanController::generateReport
 * @see app/Http/Controllers/LoanController.php:45
 * @route '/dashboard/loans/{id}/report'
 */
generateReport.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generateReport.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\LoanController::generateReport
 * @see app/Http/Controllers/LoanController.php:45
 * @route '/dashboard/loans/{id}/report'
 */
generateReport.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generateReport.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LoanController::index
 * @see app/Http/Controllers/LoanController.php:22
 * @route '/dashboard/loans'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/loans',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LoanController::index
 * @see app/Http/Controllers/LoanController.php:22
 * @route '/dashboard/loans'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanController::index
 * @see app/Http/Controllers/LoanController.php:22
 * @route '/dashboard/loans'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\LoanController::index
 * @see app/Http/Controllers/LoanController.php:22
 * @route '/dashboard/loans'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LoanController::create
 * @see app/Http/Controllers/LoanController.php:69
 * @route '/dashboard/loans/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/loans/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LoanController::create
 * @see app/Http/Controllers/LoanController.php:69
 * @route '/dashboard/loans/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanController::create
 * @see app/Http/Controllers/LoanController.php:69
 * @route '/dashboard/loans/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\LoanController::create
 * @see app/Http/Controllers/LoanController.php:69
 * @route '/dashboard/loans/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LoanController::store
 * @see app/Http/Controllers/LoanController.php:176
 * @route '/dashboard/loans'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/loans',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LoanController::store
 * @see app/Http/Controllers/LoanController.php:176
 * @route '/dashboard/loans'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanController::store
 * @see app/Http/Controllers/LoanController.php:176
 * @route '/dashboard/loans'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LoanController::show
 * @see app/Http/Controllers/LoanController.php:377
 * @route '/dashboard/loans/{loan}'
 */
export const show = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/loans/{loan}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LoanController::show
 * @see app/Http/Controllers/LoanController.php:377
 * @route '/dashboard/loans/{loan}'
 */
show.url = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { loan: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { loan: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    loan: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        loan: typeof args.loan === 'object'
                ? args.loan.id
                : args.loan,
                }

    return show.definition.url
            .replace('{loan}', parsedArgs.loan.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanController::show
 * @see app/Http/Controllers/LoanController.php:377
 * @route '/dashboard/loans/{loan}'
 */
show.get = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\LoanController::show
 * @see app/Http/Controllers/LoanController.php:377
 * @route '/dashboard/loans/{loan}'
 */
show.head = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LoanController::edit
 * @see app/Http/Controllers/LoanController.php:385
 * @route '/dashboard/loans/{loan}/edit'
 */
export const edit = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/dashboard/loans/{loan}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LoanController::edit
 * @see app/Http/Controllers/LoanController.php:385
 * @route '/dashboard/loans/{loan}/edit'
 */
edit.url = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { loan: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { loan: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    loan: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        loan: typeof args.loan === 'object'
                ? args.loan.id
                : args.loan,
                }

    return edit.definition.url
            .replace('{loan}', parsedArgs.loan.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanController::edit
 * @see app/Http/Controllers/LoanController.php:385
 * @route '/dashboard/loans/{loan}/edit'
 */
edit.get = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\LoanController::edit
 * @see app/Http/Controllers/LoanController.php:385
 * @route '/dashboard/loans/{loan}/edit'
 */
edit.head = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LoanController::update
 * @see app/Http/Controllers/LoanController.php:449
 * @route '/dashboard/loans/{loan}'
 */
const updated43a1d613bcde53261d4af351b03b461 = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updated43a1d613bcde53261d4af351b03b461.url(args, options),
    method: 'put',
})

updated43a1d613bcde53261d4af351b03b461.definition = {
    methods: ["put"],
    url: '/dashboard/loans/{loan}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\LoanController::update
 * @see app/Http/Controllers/LoanController.php:449
 * @route '/dashboard/loans/{loan}'
 */
updated43a1d613bcde53261d4af351b03b461.url = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { loan: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { loan: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    loan: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        loan: typeof args.loan === 'object'
                ? args.loan.id
                : args.loan,
                }

    return updated43a1d613bcde53261d4af351b03b461.definition.url
            .replace('{loan}', parsedArgs.loan.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanController::update
 * @see app/Http/Controllers/LoanController.php:449
 * @route '/dashboard/loans/{loan}'
 */
updated43a1d613bcde53261d4af351b03b461.put = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updated43a1d613bcde53261d4af351b03b461.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\LoanController::update
 * @see app/Http/Controllers/LoanController.php:449
 * @route '/dashboard/loans/{loan}'
 */
const updated43a1d613bcde53261d4af351b03b461 = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updated43a1d613bcde53261d4af351b03b461.url(args, options),
    method: 'patch',
})

updated43a1d613bcde53261d4af351b03b461.definition = {
    methods: ["patch"],
    url: '/dashboard/loans/{loan}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\LoanController::update
 * @see app/Http/Controllers/LoanController.php:449
 * @route '/dashboard/loans/{loan}'
 */
updated43a1d613bcde53261d4af351b03b461.url = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { loan: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { loan: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    loan: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        loan: typeof args.loan === 'object'
                ? args.loan.id
                : args.loan,
                }

    return updated43a1d613bcde53261d4af351b03b461.definition.url
            .replace('{loan}', parsedArgs.loan.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanController::update
 * @see app/Http/Controllers/LoanController.php:449
 * @route '/dashboard/loans/{loan}'
 */
updated43a1d613bcde53261d4af351b03b461.patch = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updated43a1d613bcde53261d4af351b03b461.url(args, options),
    method: 'patch',
})

export const update = {
    '/dashboard/loans/{loan}': updated43a1d613bcde53261d4af351b03b461,
    '/dashboard/loans/{loan}': updated43a1d613bcde53261d4af351b03b461,
}

/**
* @see \App\Http\Controllers\LoanController::destroy
 * @see app/Http/Controllers/LoanController.php:509
 * @route '/dashboard/loans/{loan}'
 */
export const destroy = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/loans/{loan}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\LoanController::destroy
 * @see app/Http/Controllers/LoanController.php:509
 * @route '/dashboard/loans/{loan}'
 */
destroy.url = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { loan: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { loan: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    loan: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        loan: typeof args.loan === 'object'
                ? args.loan.id
                : args.loan,
                }

    return destroy.definition.url
            .replace('{loan}', parsedArgs.loan.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanController::destroy
 * @see app/Http/Controllers/LoanController.php:509
 * @route '/dashboard/loans/{loan}'
 */
destroy.delete = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\LoanController::returnLoan
 * @see app/Http/Controllers/LoanController.php:309
 * @route '/dashboard/loans/{loan}/return'
 */
export const returnLoan = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: returnLoan.url(args, options),
    method: 'post',
})

returnLoan.definition = {
    methods: ["post"],
    url: '/dashboard/loans/{loan}/return',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LoanController::returnLoan
 * @see app/Http/Controllers/LoanController.php:309
 * @route '/dashboard/loans/{loan}/return'
 */
returnLoan.url = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { loan: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { loan: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    loan: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        loan: typeof args.loan === 'object'
                ? args.loan.id
                : args.loan,
                }

    return returnLoan.definition.url
            .replace('{loan}', parsedArgs.loan.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanController::returnLoan
 * @see app/Http/Controllers/LoanController.php:309
 * @route '/dashboard/loans/{loan}/return'
 */
returnLoan.post = (args: { loan: number | { id: number } } | [loan: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: returnLoan.url(args, options),
    method: 'post',
})
const LoanController = { generateReport, index, create, store, show, edit, update, destroy, returnLoan }

export default LoanController