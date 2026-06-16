import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ReportController::index
 * @see app/Http/Controllers/ReportController.php:19
 * @route '/dashboard/reports'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/reports',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::index
 * @see app/Http/Controllers/ReportController.php:19
 * @route '/dashboard/reports'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::index
 * @see app/Http/Controllers/ReportController.php:19
 * @route '/dashboard/reports'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ReportController::index
 * @see app/Http/Controllers/ReportController.php:19
 * @route '/dashboard/reports'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ReportController::index
 * @see app/Http/Controllers/ReportController.php:19
 * @route '/dashboard/reports'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ReportController::index
 * @see app/Http/Controllers/ReportController.php:19
 * @route '/dashboard/reports'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ReportController::index
 * @see app/Http/Controllers/ReportController.php:19
 * @route '/dashboard/reports'
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
* @see \App\Http\Controllers\ReportController::create
 * @see app/Http/Controllers/ReportController.php:381
 * @route '/dashboard/reports/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/reports/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::create
 * @see app/Http/Controllers/ReportController.php:381
 * @route '/dashboard/reports/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::create
 * @see app/Http/Controllers/ReportController.php:381
 * @route '/dashboard/reports/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ReportController::create
 * @see app/Http/Controllers/ReportController.php:381
 * @route '/dashboard/reports/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ReportController::create
 * @see app/Http/Controllers/ReportController.php:381
 * @route '/dashboard/reports/create'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ReportController::create
 * @see app/Http/Controllers/ReportController.php:381
 * @route '/dashboard/reports/create'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ReportController::create
 * @see app/Http/Controllers/ReportController.php:381
 * @route '/dashboard/reports/create'
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
* @see \App\Http\Controllers\ReportController::store
 * @see app/Http/Controllers/ReportController.php:389
 * @route '/dashboard/reports'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/reports',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ReportController::store
 * @see app/Http/Controllers/ReportController.php:389
 * @route '/dashboard/reports'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::store
 * @see app/Http/Controllers/ReportController.php:389
 * @route '/dashboard/reports'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ReportController::store
 * @see app/Http/Controllers/ReportController.php:389
 * @route '/dashboard/reports'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ReportController::store
 * @see app/Http/Controllers/ReportController.php:389
 * @route '/dashboard/reports'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\ReportController::show
 * @see app/Http/Controllers/ReportController.php:397
 * @route '/dashboard/reports/{report}'
 */
export const show = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/reports/{report}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::show
 * @see app/Http/Controllers/ReportController.php:397
 * @route '/dashboard/reports/{report}'
 */
show.url = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { report: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    report: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        report: args.report,
                }

    return show.definition.url
            .replace('{report}', parsedArgs.report.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::show
 * @see app/Http/Controllers/ReportController.php:397
 * @route '/dashboard/reports/{report}'
 */
show.get = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ReportController::show
 * @see app/Http/Controllers/ReportController.php:397
 * @route '/dashboard/reports/{report}'
 */
show.head = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ReportController::show
 * @see app/Http/Controllers/ReportController.php:397
 * @route '/dashboard/reports/{report}'
 */
    const showForm = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ReportController::show
 * @see app/Http/Controllers/ReportController.php:397
 * @route '/dashboard/reports/{report}'
 */
        showForm.get = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ReportController::show
 * @see app/Http/Controllers/ReportController.php:397
 * @route '/dashboard/reports/{report}'
 */
        showForm.head = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\ReportController::edit
 * @see app/Http/Controllers/ReportController.php:405
 * @route '/dashboard/reports/{report}/edit'
 */
export const edit = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/dashboard/reports/{report}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::edit
 * @see app/Http/Controllers/ReportController.php:405
 * @route '/dashboard/reports/{report}/edit'
 */
edit.url = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { report: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    report: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        report: args.report,
                }

    return edit.definition.url
            .replace('{report}', parsedArgs.report.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::edit
 * @see app/Http/Controllers/ReportController.php:405
 * @route '/dashboard/reports/{report}/edit'
 */
edit.get = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ReportController::edit
 * @see app/Http/Controllers/ReportController.php:405
 * @route '/dashboard/reports/{report}/edit'
 */
edit.head = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ReportController::edit
 * @see app/Http/Controllers/ReportController.php:405
 * @route '/dashboard/reports/{report}/edit'
 */
    const editForm = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ReportController::edit
 * @see app/Http/Controllers/ReportController.php:405
 * @route '/dashboard/reports/{report}/edit'
 */
        editForm.get = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ReportController::edit
 * @see app/Http/Controllers/ReportController.php:405
 * @route '/dashboard/reports/{report}/edit'
 */
        editForm.head = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\ReportController::update
 * @see app/Http/Controllers/ReportController.php:413
 * @route '/dashboard/reports/{report}'
 */
export const update = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/dashboard/reports/{report}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\ReportController::update
 * @see app/Http/Controllers/ReportController.php:413
 * @route '/dashboard/reports/{report}'
 */
update.url = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { report: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    report: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        report: args.report,
                }

    return update.definition.url
            .replace('{report}', parsedArgs.report.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::update
 * @see app/Http/Controllers/ReportController.php:413
 * @route '/dashboard/reports/{report}'
 */
update.put = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\ReportController::update
 * @see app/Http/Controllers/ReportController.php:413
 * @route '/dashboard/reports/{report}'
 */
update.patch = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\ReportController::update
 * @see app/Http/Controllers/ReportController.php:413
 * @route '/dashboard/reports/{report}'
 */
    const updateForm = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ReportController::update
 * @see app/Http/Controllers/ReportController.php:413
 * @route '/dashboard/reports/{report}'
 */
        updateForm.put = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
            /**
* @see \App\Http\Controllers\ReportController::update
 * @see app/Http/Controllers/ReportController.php:413
 * @route '/dashboard/reports/{report}'
 */
        updateForm.patch = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\ReportController::destroy
 * @see app/Http/Controllers/ReportController.php:421
 * @route '/dashboard/reports/{report}'
 */
export const destroy = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/reports/{report}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ReportController::destroy
 * @see app/Http/Controllers/ReportController.php:421
 * @route '/dashboard/reports/{report}'
 */
destroy.url = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { report: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    report: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        report: args.report,
                }

    return destroy.definition.url
            .replace('{report}', parsedArgs.report.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::destroy
 * @see app/Http/Controllers/ReportController.php:421
 * @route '/dashboard/reports/{report}'
 */
destroy.delete = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\ReportController::destroy
 * @see app/Http/Controllers/ReportController.php:421
 * @route '/dashboard/reports/{report}'
 */
    const destroyForm = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ReportController::destroy
 * @see app/Http/Controllers/ReportController.php:421
 * @route '/dashboard/reports/{report}'
 */
        destroyForm.delete = (args: { report: string | number } | [report: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
/**
* @see \App\Http\Controllers\ReportController::exportInventory
 * @see app/Http/Controllers/ReportController.php:140
 * @route '/dashboard/reports/inventory/pdf'
 */
export const exportInventory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportInventory.url(options),
    method: 'get',
})

exportInventory.definition = {
    methods: ["get","head"],
    url: '/dashboard/reports/inventory/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::exportInventory
 * @see app/Http/Controllers/ReportController.php:140
 * @route '/dashboard/reports/inventory/pdf'
 */
exportInventory.url = (options?: RouteQueryOptions) => {
    return exportInventory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::exportInventory
 * @see app/Http/Controllers/ReportController.php:140
 * @route '/dashboard/reports/inventory/pdf'
 */
exportInventory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportInventory.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ReportController::exportInventory
 * @see app/Http/Controllers/ReportController.php:140
 * @route '/dashboard/reports/inventory/pdf'
 */
exportInventory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportInventory.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ReportController::exportInventory
 * @see app/Http/Controllers/ReportController.php:140
 * @route '/dashboard/reports/inventory/pdf'
 */
    const exportInventoryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: exportInventory.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ReportController::exportInventory
 * @see app/Http/Controllers/ReportController.php:140
 * @route '/dashboard/reports/inventory/pdf'
 */
        exportInventoryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportInventory.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ReportController::exportInventory
 * @see app/Http/Controllers/ReportController.php:140
 * @route '/dashboard/reports/inventory/pdf'
 */
        exportInventoryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportInventory.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    exportInventory.form = exportInventoryForm
/**
* @see \App\Http\Controllers\ReportController::exportHistory
 * @see app/Http/Controllers/ReportController.php:218
 * @route '/dashboard/reports/history/pdf'
 */
export const exportHistory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportHistory.url(options),
    method: 'get',
})

exportHistory.definition = {
    methods: ["get","head"],
    url: '/dashboard/reports/history/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::exportHistory
 * @see app/Http/Controllers/ReportController.php:218
 * @route '/dashboard/reports/history/pdf'
 */
exportHistory.url = (options?: RouteQueryOptions) => {
    return exportHistory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::exportHistory
 * @see app/Http/Controllers/ReportController.php:218
 * @route '/dashboard/reports/history/pdf'
 */
exportHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportHistory.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ReportController::exportHistory
 * @see app/Http/Controllers/ReportController.php:218
 * @route '/dashboard/reports/history/pdf'
 */
exportHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportHistory.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ReportController::exportHistory
 * @see app/Http/Controllers/ReportController.php:218
 * @route '/dashboard/reports/history/pdf'
 */
    const exportHistoryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: exportHistory.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ReportController::exportHistory
 * @see app/Http/Controllers/ReportController.php:218
 * @route '/dashboard/reports/history/pdf'
 */
        exportHistoryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportHistory.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ReportController::exportHistory
 * @see app/Http/Controllers/ReportController.php:218
 * @route '/dashboard/reports/history/pdf'
 */
        exportHistoryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportHistory.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    exportHistory.form = exportHistoryForm
/**
* @see \App\Http\Controllers\ReportController::exportIssues
 * @see app/Http/Controllers/ReportController.php:338
 * @route '/dashboard/reports/issues/pdf'
 */
export const exportIssues = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportIssues.url(options),
    method: 'get',
})

exportIssues.definition = {
    methods: ["get","head"],
    url: '/dashboard/reports/issues/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::exportIssues
 * @see app/Http/Controllers/ReportController.php:338
 * @route '/dashboard/reports/issues/pdf'
 */
exportIssues.url = (options?: RouteQueryOptions) => {
    return exportIssues.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::exportIssues
 * @see app/Http/Controllers/ReportController.php:338
 * @route '/dashboard/reports/issues/pdf'
 */
exportIssues.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportIssues.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ReportController::exportIssues
 * @see app/Http/Controllers/ReportController.php:338
 * @route '/dashboard/reports/issues/pdf'
 */
exportIssues.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportIssues.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ReportController::exportIssues
 * @see app/Http/Controllers/ReportController.php:338
 * @route '/dashboard/reports/issues/pdf'
 */
    const exportIssuesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: exportIssues.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ReportController::exportIssues
 * @see app/Http/Controllers/ReportController.php:338
 * @route '/dashboard/reports/issues/pdf'
 */
        exportIssuesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportIssues.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ReportController::exportIssues
 * @see app/Http/Controllers/ReportController.php:338
 * @route '/dashboard/reports/issues/pdf'
 */
        exportIssuesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportIssues.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    exportIssues.form = exportIssuesForm
const ReportController = { index, create, store, show, edit, update, destroy, exportInventory, exportHistory, exportIssues }

export default ReportController