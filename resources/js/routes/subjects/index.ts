import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
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
* @see \App\Http\Controllers\SubjectController::toggle
 * @see app/Http/Controllers/SubjectController.php:38
 * @route '/dashboard/subjects/{subject}/toggle'
 */
export const toggle = (args: { subject: number | { id: number } } | [subject: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

toggle.definition = {
    methods: ["post"],
    url: '/dashboard/subjects/{subject}/toggle',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SubjectController::toggle
 * @see app/Http/Controllers/SubjectController.php:38
 * @route '/dashboard/subjects/{subject}/toggle'
 */
toggle.url = (args: { subject: number | { id: number } } | [subject: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return toggle.definition.url
            .replace('{subject}', parsedArgs.subject.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SubjectController::toggle
 * @see app/Http/Controllers/SubjectController.php:38
 * @route '/dashboard/subjects/{subject}/toggle'
 */
toggle.post = (args: { subject: number | { id: number } } | [subject: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})
const subjects = {
    index: Object.assign(index, index),
import: Object.assign(importMethod, importMethod),
toggle: Object.assign(toggle, toggle),
}

export default subjects