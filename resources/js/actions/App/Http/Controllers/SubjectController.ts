import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\SubjectController::index
 * @see app/Http/Controllers/SubjectController.php:16
 * @route '/dashboard/subjects'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/subjects',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SubjectController::index
 * @see app/Http/Controllers/SubjectController.php:16
 * @route '/dashboard/subjects'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SubjectController::index
 * @see app/Http/Controllers/SubjectController.php:16
 * @route '/dashboard/subjects'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\SubjectController::index
 * @see app/Http/Controllers/SubjectController.php:16
 * @route '/dashboard/subjects'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SubjectController::importMethod
 * @see app/Http/Controllers/SubjectController.php:23
 * @route '/dashboard/subjects/import'
 */
export const importMethod = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})

importMethod.definition = {
    methods: ["post"],
    url: '/dashboard/subjects/import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SubjectController::importMethod
 * @see app/Http/Controllers/SubjectController.php:23
 * @route '/dashboard/subjects/import'
 */
importMethod.url = (options?: RouteQueryOptions) => {
    return importMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SubjectController::importMethod
 * @see app/Http/Controllers/SubjectController.php:23
 * @route '/dashboard/subjects/import'
 */
importMethod.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SubjectController::toggleStatus
 * @see app/Http/Controllers/SubjectController.php:38
 * @route '/dashboard/subjects/{subject}/toggle'
 */
export const toggleStatus = (args: { subject: number | { id: number } } | [subject: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleStatus.url(args, options),
    method: 'post',
})

toggleStatus.definition = {
    methods: ["post"],
    url: '/dashboard/subjects/{subject}/toggle',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SubjectController::toggleStatus
 * @see app/Http/Controllers/SubjectController.php:38
 * @route '/dashboard/subjects/{subject}/toggle'
 */
toggleStatus.url = (args: { subject: number | { id: number } } | [subject: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { subject: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { subject: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    subject: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        subject: typeof args.subject === 'object'
                ? args.subject.id
                : args.subject,
                }

    return toggleStatus.definition.url
            .replace('{subject}', parsedArgs.subject.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SubjectController::toggleStatus
 * @see app/Http/Controllers/SubjectController.php:38
 * @route '/dashboard/subjects/{subject}/toggle'
 */
toggleStatus.post = (args: { subject: number | { id: number } } | [subject: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggleStatus.url(args, options),
    method: 'post',
})
const SubjectController = { index, importMethod, toggleStatus, import: importMethod }

export default SubjectController