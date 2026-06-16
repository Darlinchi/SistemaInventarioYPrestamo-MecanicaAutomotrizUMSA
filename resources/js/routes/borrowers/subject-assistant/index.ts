import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\BorrowerController::remove
 * @see app/Http/Controllers/BorrowerController.php:238
 * @route '/dashboard/borrowers/{borrower}/subject-assistant'
 */
export const remove = (args: { borrower: number | { id: number } } | [borrower: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: remove.url(args, options),
    method: 'delete',
})

remove.definition = {
    methods: ["delete"],
    url: '/dashboard/borrowers/{borrower}/subject-assistant',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\BorrowerController::remove
 * @see app/Http/Controllers/BorrowerController.php:238
 * @route '/dashboard/borrowers/{borrower}/subject-assistant'
 */
remove.url = (args: { borrower: number | { id: number } } | [borrower: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return remove.definition.url
            .replace('{borrower}', parsedArgs.borrower.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BorrowerController::remove
 * @see app/Http/Controllers/BorrowerController.php:238
 * @route '/dashboard/borrowers/{borrower}/subject-assistant'
 */
remove.delete = (args: { borrower: number | { id: number } } | [borrower: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: remove.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\BorrowerController::remove
 * @see app/Http/Controllers/BorrowerController.php:238
 * @route '/dashboard/borrowers/{borrower}/subject-assistant'
 */
    const removeForm = (args: { borrower: number | { id: number } } | [borrower: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: remove.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\BorrowerController::remove
 * @see app/Http/Controllers/BorrowerController.php:238
 * @route '/dashboard/borrowers/{borrower}/subject-assistant'
 */
        removeForm.delete = (args: { borrower: number | { id: number } } | [borrower: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: remove.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    remove.form = removeForm
const subjectAssistant = {
    remove: Object.assign(remove, remove),
}

export default subjectAssistant